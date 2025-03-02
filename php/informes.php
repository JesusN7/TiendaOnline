<?php
session_start();
require_once 'conexion_db.php';

// Verificar si el usuario es admin
if (!isset($_SESSION['id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

try {
    $conexion = conectar();

    // Consulta para obtener los productos más vendidos
    $sql_productos_vendidos = "
        SELECT 
            p.codigo, 
            p.nombre, 
            SUM(lp.cantidad) AS total_vendido
        FROM 
            lineapedido lp
        JOIN 
            productos p ON lp.codigo_producto = p.codigo
        GROUP BY 
            p.codigo, p.nombre
        ORDER BY 
            total_vendido DESC
    ";
    $stmt_productos = $conexion->prepare($sql_productos_vendidos);
    $stmt_productos->execute();
    $productos_vendidos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para obtener las ganancias totales
    $sql_ganancias = "
        SELECT 
            SUM(total) AS ganancias_totales
        FROM 
            pedidos
        WHERE 
            estado = 'entregado'
    ";
    $stmt_ganancias = $conexion->prepare($sql_ganancias);
    $stmt_ganancias->execute();
    $ganancias = $stmt_ganancias->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error en informes.php: " . $e->getMessage());
    $_SESSION['error'] = "Error al generar los informes.";
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Informes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #graficoProductos {
            width: 100% !important;
            height: 400px !important;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Informes de Ventas</h2>

        <!-- Mostrar mensajes de error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Ganancias totales -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Ganancias Totales</h4>
            </div>
            <div class="card-body">
                <p class="card-text">
                    Ganancias totales: <strong><?= number_format($ganancias['ganancias_totales'], 2) ?>€</strong>
                </p>
            </div>
        </div>

        <!-- Productos más vendidos -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Productos Más Vendidos</h4>
            </div>
            <div class="card-body">
                <!-- Gráfico de productos más vendidos -->
                <canvas id="graficoProductos"></canvas>
            </div>
        </div>

        <!-- Tabla de productos más vendidos -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Detalle de Productos Más Vendidos</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Cantidad Vendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos_vendidos as $producto): ?>
                            <tr>
                                <td><?= htmlspecialchars($producto['codigo']) ?></td>
                                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                <td><?= $producto['total_vendido'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Botón para volver al panel -->
        <a href="panel.php" class="btn btn-secondary">Volver al Panel</a>
    </div>

    <!-- Script para el gráfico -->
    <script>
        const ctx = document.getElementById('graficoProductos').getContext('2d');
        const productos = <?= json_encode(array_column($productos_vendidos, 'nombre')) ?>;
        const cantidades = <?= json_encode(array_column($productos_vendidos, 'total_vendido')) ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: productos,
                datasets: [{
                    label: 'Cantidad Vendida',
                    data: cantidades,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true, // Hace que el gráfico sea responsive
                maintainAspectRatio: false, // Permite ajustar la altura sin mantener la relación de aspecto
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>