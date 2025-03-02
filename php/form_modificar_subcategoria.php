<?php
require_once 'conexion_db.php';
require_once 'logica_categorias.php';

$conexion = conectar();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idSubcategoria = (int) $_GET['id'];
    $subcategoria = obtenerCategoriaPorId($conexion, $idSubcategoria); // Obtener subcategoría

    if (!$subcategoria) {
        // Si no existe, redirigir
        header("Location: gestion_categorias.php?error=subcategoria_no_encontrada");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modificar_subcategoria'])) {
    $nombre = trim($_POST['nombre']);
    $categoriaPadre = (int)$_POST['categoriaPadre'];

    if (!empty($nombre)) {
        // Modificar la subcategoría
        $resultado = modificarCategoria($conexion, $idSubcategoria, $nombre, $categoriaPadre);
        if ($resultado) {
            header("Location: gestion_categorias.php?mensaje=subcategoria_modificada");
            exit;
        } else {
            $error = "Error al modificar la subcategoría.";
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
    <title>Modificar Subcategoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Modificar Subcategoría</h2>

        <?php if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        } ?>

        <form action="form_modificar_subcategoria.php?id=<?= $subcategoria['id'] ?>" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Subcategoría</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($subcategoria['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="categoriaPadre" class="form-label">Categoría Padre</label>
                <select class="form-control" id="categoriaPadre" name="categoriaPadre">
                    <option value="">Selecciona una categoría</option>
                    <?php
                    $categorias = obtenerCategorias($conexion);
                    foreach ($categorias as $categoria):
                        if ($categoria['id'] != $subcategoria['id']) {
                    ?>
                            <option value="<?= $categoria['id'] ?>" <?= $categoria['id'] == $subcategoria['categoriaPadre'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </option>
                    <?php }
                    endforeach; ?>
                </select>
            </div>

            <button type="submit" name="modificar_subcategoria" class="btn btn-primary">Modificar</button>
            <a href="gestion_categorias.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>