<?php
session_start();
require_once 'conexion_db.php';

$conexion = conectar();

// Obtener las categorías existentes para poder seleccionarlas en el formulario.
$sql = "SELECT id, nombre FROM categorias";
$stmt = $conexion->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener errores de la sesión
$errores = $_SESSION['errores'] ?? [];
$valoresIngresados = $_SESSION['valoresIngresados'] ?? [];
unset($_SESSION['errores'], $_SESSION['valoresIngresados']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="container mt-4">
    <h2 class="mb-3">Crear Producto</h2>

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

    <!-- Formulario nuevo producto -->
    <form action="crear_producto.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código:</label>
            <input type="text" id="codigo" name="codigo" class="form-control" required pattern="[A-Za-z]{3}[0-9]{1,3}" title="El código debe tener 3 letras seguidas de 1 a 3 números." value="<?= htmlspecialchars($valoresIngresados['codigo'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required value="<?= htmlspecialchars($valoresIngresados['nombre'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control" required><?= htmlspecialchars($valoresIngresados['descripcion'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="precio" class="form-label">Precio:</label>
            <input type="number" id="precio" name="precio" class="form-control" required min="0" step="0.01" value="<?= htmlspecialchars($valoresIngresados['precio'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="descuento" class="form-label">Descuento (%):</label>
            <input type="number" id="descuento" name="descuento" class="form-control" required min="0" max="100" step="0.01" value="<?= htmlspecialchars($valoresIngresados['descuento'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="stock" class="form-label">Stock:</label>
            <input type="number" id="stock" name="stock" class="form-control" required min="0" value="<?= htmlspecialchars($valoresIngresados['stock'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría:</label>
            <select id="categoria_id" name="categoria_id" class="form-select" required>
                <?php foreach ($categorias as $categoria) : ?>
                    <option value="<?= $categoria['id'] ?>" <?= isset($valoresIngresados['categoria_id']) && $valoresIngresados['categoria_id'] == $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="activo" class="form-label">Activo:</label>
            <select id="activo" name="activo" class="form-select" required>
                <option value="1" <?= isset($valoresIngresados['activo']) && $valoresIngresados['activo'] == 1 ? 'selected' : '' ?>>Sí</option>
                <option value="0" <?= isset($valoresIngresados['activo']) && $valoresIngresados['activo'] == 0 ? 'selected' : '' ?>>No</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen:</label>
            <input type="file" id="imagen" name="imagen" class="form-control" accept=".jpg,.jpeg,.png" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Crear producto
        </button>

        <a href="gestion_productos.php" class="btn btn-secondary ms-3">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
    </form>
</body>

</html>