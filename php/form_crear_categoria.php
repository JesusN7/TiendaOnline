<?php
require_once 'conexion_db.php';
require_once 'logica_categorias.php';

$conexion = conectar();
$categorias = obtenerCategorias($conexion); 

// Verificar si se está enviando el formulario de creación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_categoria'])) {
    $nombre = trim($_POST['nombre']);
    $categoriaPadre = !empty($_POST['categoriaPadre']) ? (int)$_POST['categoriaPadre'] : null;

    // Validar el nombre
    if (!empty($nombre)) {
        // Crear la categoría
        $resultado = crearCategoria($conexion, $nombre, $categoriaPadre);
        if ($resultado) {
            header("Location: gestion_categorias.php?mensaje=categoria_creada");
            exit;
        } else {
            $error = "Error al crear la categoría.";
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
    <title>Crear Categoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Crear Categoría</h2>

        <!-- Volver atrás -->
        <a href="gestion_categorias.php" class="btn btn-secondary mb-3">Volver</a>

        <!-- Formulario para crear categoría -->
        <form action="form_crear_categoria.php" method="POST">
            <!-- Campo para el nombre de la categoría -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Categoría:</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <!-- Campo para seleccionar categoría padre -->
            <div class="mb-3">
                <label for="categoriaPadre" class="form-label">Categoría Padre (opcional):</label>
                <select name="categoriaPadre" class="form-control">
                    <option value="">Ninguna</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id'] ?>">
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botón para crear la categoría -->
            <button type="submit" name="crear_categoria" class="btn btn-success">Crear Categoría</button>
        </form>

        <!-- Mostrar mensaje de error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?= $error ?></div>
        <?php endif; ?>
    </div>

    <!-- JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>