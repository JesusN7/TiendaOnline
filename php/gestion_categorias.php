<?php
require_once 'conexion_db.php';
require_once 'logica_categorias.php';

$conexion = conectar();
$categorias = obtenerCategorias($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Enlazamos nuestro archivo de CSS -->
    <link rel="stylesheet" href="/TiendaOnline/css/gestion_categorias.css">
</head>

<body>
    <div class="container mt-4">
        <h2 class="mb-4">Gestión de Categorías</h2>

        <!-- Mensajes de éxito o error -->
        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_GET['mensaje']) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <a href="panel.php" class="btn btn-secondary mb-3">Volver</a>
        <a href="form_crear_categoria.php" class="btn btn-custom mb-3">Agregar Categoría</a>

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                    <tr>
                        <td><?= htmlspecialchars($categoria['id']) ?></td>
                        <td><?= htmlspecialchars($categoria['nombre']) ?></td>
                        <td><?= $categoria['activo'] ? 'Activo' : 'Inactivo' ?></td>
                        <td>
                            <a href="form_modificar_categoria.php?id=<?= $categoria['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Modificar
                            </a>
                            <a href="activar_desactivar_categoria.php?id=<?= $categoria['id'] ?>" class="btn <?= $categoria['activo'] ? 'btn-danger' : 'btn-success' ?> btn-sm">
                                <i class="fas <?= $categoria['activo'] ? 'fa-ban' : 'fa-check' ?>"></i> 
                                <?= $categoria['activo'] ? 'Desactivar' : 'Activar' ?>
                            </a>
                        </td>
                    </tr>
                    <?php
                    // Mostrar subcategorías debajo de la categoría principal
                    if (!empty($categoria['subcategorias'])):
                        foreach ($categoria['subcategorias'] as $subcategoria):
                    ?>
                            <tr class="table-secondary">
                                <td><?= htmlspecialchars($subcategoria['id']) ?></td>
                                <td><?= htmlspecialchars($subcategoria['nombre']) ?></td>
                                <td><?= $subcategoria['activo'] ? 'Activo' : 'Inactivo' ?></td>
                                <td>
                                    <a href="form_modificar_categoria.php?id=<?= $subcategoria['id'] ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Modificar
                                    </a>
                                    <a href="activar_desactivar_categoria.php?id=<?= $subcategoria['id'] ?>" class="btn <?= $subcategoria['activo'] ? 'btn-danger' : 'btn-success' ?> btn-sm">
                                        <i class="fas <?= $subcategoria['activo'] ? 'fa-ban' : 'fa-check' ?>"></i> 
                                        <?= $subcategoria['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </a>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
