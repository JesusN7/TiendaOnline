<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Verificar autenticación
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

// Obtener rol del usuario
$rol = $_SESSION['rol'] ?? 'cliente';
$usuario_id = $_SESSION['id'];

// Consulta según rol
try {
    $conexion = conectar();

    // Consulta de pedidos
    if ($rol === 'cliente') {
        $sql_pedidos = "SELECT * FROM pedidos WHERE usuario_id = ? AND activo = 1 ORDER BY fecha_pedido DESC";
        $stmt_pedidos = $conexion->prepare($sql_pedidos);
        $stmt_pedidos->execute([$usuario_id]);
    } else { // empleado o admin
        $sql_pedidos = "SELECT * FROM pedidos WHERE activo = 1 ORDER BY fecha_pedido DESC";
        $stmt_pedidos = $conexion->query($sql_pedidos);
    }

    $pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener nombres de los usuarios para cada pedido
    $clientes = [];
    if ($pedidos) {
        foreach ($pedidos as $pedido) {
            $sql_cliente = "SELECT nombre FROM usuarios WHERE id = ?";
            $stmt_cliente = $conexion->prepare($sql_cliente);
            $stmt_cliente->execute([$pedido['usuario_id']]);
            $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
            $clientes[$pedido['numPedido']] = $cliente['nombre'] ?? 'Desconocido';
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error al cargar pedidos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Gestión de Pedidos</h2>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_SESSION['exito'])): ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['exito'] ?>
            </div>
            <?php unset($_SESSION['exito']); // Limpiar el mensaje de éxito 
            ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); // Limpiar el mensaje de error 
            ?>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido['numPedido'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></td>
                        <td><?= htmlspecialchars($clientes[$pedido['numPedido']]) ?></td>
                        <td><?= number_format($pedido['total'], 2) ?>€</td>
                        <td>
                            <?php if (in_array($rol, ['empleado', 'admin'])): ?>
                                <form method="POST" action="actualizar_pedido.php" class="form-inline">
                                    <input type="hidden" name="numPedido" value="<?= $pedido['numPedido'] ?>">
                                    <select name="estado" class="form-select">
                                        <?php foreach (['pendiente', 'enviado', 'entregado', 'cancelado'] as $estado): ?>
                                            <option value="<?= $estado ?>" <?= $pedido['estado'] === $estado ? 'selected' : '' ?>>
                                                <?= ucfirst($estado) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary mt-1">Actualizar</button>
                                </form>
                            <?php else: ?>
                                <span class="badge bg-<?= obtenerColorEstado($pedido['estado']) ?>">
                                    <?= ucfirst($pedido['estado']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($rol === 'admin'): ?>
                                <a href="eliminar_pedido.php?num=<?= $pedido['numPedido'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar permanentemente este pedido?')">
                                    Eliminar
                                </a>
                            <?php elseif ($rol === 'cliente' && $pedido['estado'] === 'pendiente'): ?>
                                <a href="eliminar_pedido.php?num=<?= $pedido['numPedido'] ?>&cliente=1"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Cancelar este pedido? Esta acción no se puede deshacer')">
                                    <i class="fas fa-trash-alt"></i> Cancelar Pedido
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Botón para volver al panel -->
        <a href="panel.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</body>

</html>