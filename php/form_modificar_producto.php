<?php
session_start();
require_once 'conexion_db.php';

$conexion = conectar();

// Obtener errores de la sesión
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

// Verificar si el usuario está autenticado y tiene los permisos adecuados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'empleado' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

// Verificar si se ha proporcionado un código de producto
if (!isset($_GET['codigo'])) {
    echo "Código de producto no especificado.";
    exit();
}

// Obtener los detalles del producto
$codigo = htmlspecialchars($_GET['codigo']);
$sql = "SELECT * FROM productos WHERE codigo = :codigo";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
$stmt->execute();
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    echo "Producto no encontrado.";
    exit();
}

// Obtener los id y nombres de las categorías
$sql = "SELECT id, nombre FROM categorias";
$stmt = $conexion->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="container mt-4">
    <h2 class="mb-3">Modificar Producto</h2>

    <!-- Mostrar errores -->
    <?php if (!empty($errores)) : ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errores as $error) : ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="modificar_producto.php?codigo=<?= htmlspecialchars($producto['codigo']) ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($producto['codigo']) ?>">

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" id="precio" name="precio" class="form-control" value="<?= htmlspecialchars($producto['precio']) ?>" required min="0" step="0.01">
        </div>

        <div class="mb-3">
            <label for="descuento" class="form-label">Descuento (%):</label>
            <input type="number" id="descuento" name="descuento" class="form-control" value="<?= htmlspecialchars($producto['descuento']) ?>" required min="0" max="100" step="0.01">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" id="stock" name="stock" class="form-control" value="<?= htmlspecialchars($producto['stock']) ?>" required min="0">
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría:</label>
            <select id="categoria_id" name="categoria_id" class="form-select" required>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?= $categoria['id'] ?>" <?= ($producto['categoria_id'] == $categoria['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="activo" class="form-label">Activo:</label>
            <select id="activo" name="activo" class="form-select" required>
                <option value="1" <?= $producto['activo'] ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= !$producto['activo'] ? 'selected' : '' ?>>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen:</label>
            <input type="file" id="imagen" name="imagen" class="form-control" accept=".jpg,.jpeg,.png">
        </div>

        <button type="submit" name="modificar" class="btn btn-warning">
            <i class="fas fa-save"></i> Modificar producto
        </button>
    </form>

    <a href="gestion_productos.php" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Cancelar
    </a>
</body>

</html>
