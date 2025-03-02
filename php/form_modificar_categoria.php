<?php
require_once 'conexion_db.php';
require_once 'logica_categorias.php';

$conexion = conectar();

// Verificar si se ha pasado un 'id' válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $categoria = obtenerCategoriaPorId($conexion, $id); // Obtener la categoría actual para modificar

    if (!$categoria) {
        // Si no se encuentra la categoría, redirigir con mensaje de error
        header("Location: gestion_categorias.php?error=categoria_no_encontrada");
        exit;
    }
} else {
    // Si no se pasa un 'id' válido, redirigir con mensaje de error
    header("Location: gestion_categorias.php?error=id_invalido");
    exit;
}

// Obtener todas las categorías para el campo de categoría padre
$categorias = obtenerCategorias($conexion);

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_categoria'])) {
    $nombre = trim($_POST['nombre']);
    $categoriaPadre = !empty($_POST['categoriaPadre']) ? (int)$_POST['categoriaPadre'] : null;

    // Validar que el nombre no esté vacío
    if (!empty($nombre)) {
        // Modificar la categoría en la base de datos
        $resultado = modificarCategoria($conexion, $id, $nombre, $categoriaPadre);
        if ($resultado) {
            header("Location: gestion_categorias.php?mensaje=categoria_modificada");
            exit;
        } else {
            $error = "Error al modificar la categoría.";
        }
    } else {
        $error = "El nombre no puede estar vacío.";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Modificar Categoría</h2>

        <!-- Volver atrás -->
        <a href="gestion_categorias.php" class="btn btn-secondary mb-3">Volver</a>

        <!-- Mostrar errores -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para modificar la categoría -->
        <form action="form_modificar_categoria.php?id=<?= $categoria['id'] ?>" method="POST">
            <!-- Campo para el nombre de la categoría -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
            </div>

            <!-- Campo para seleccionar la categoría padre -->
            <div class="mb-3">
                <label for="categoriaPadre" class="form-label">Categoría Padre (opcional):</label>
                <select name="categoriaPadre" class="form-control">
                    <option value="">Ninguna</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoria['categoriaPadre'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botón para modificar la categoría -->
            <button type="submit" name="modificar_categoria" class="btn btn-primary">Modificar Categoría</button>
        </form>
    </div>

    <!-- JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>