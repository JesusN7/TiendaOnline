<?php
session_start();
require_once 'conexion_db.php';
require_once 'logica_productos.php';

// Verificar si el usuario está autenticado y tiene permisos
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'empleado' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Archivo de estilos CSS -->
    <link rel="stylesheet" href="/TiendaOnline/css/gestion_productos.css">
</head>

<body class="container mt-4">
    <h2 class="mb-3">Gestión de Productos</h2>

    <a href="form_crear_producto.php" class="btn btn-custom mb-3">Crear nuevo producto</a>

    <!-- Formulario de búsqueda -->
    <form action="gestion_productos.php" method="get" class="mb-3 d-flex">
        <input type="text" name="buscar" class="form-control me-2" placeholder="Buscar por nombre..." value="<?= htmlspecialchars($busqueda); ?>">
        <button type="submit" class="btn btn-custom">Buscar</button>
    </form>

    <!-- Formulario de ordenamiento -->
    <form action="gestion_productos.php" method="get" id="formOrden" class="mb-3">
        <input type="hidden" name="buscar" value="<?= htmlspecialchars($busqueda); ?>">
        <label for="orden" class="me-2">Ordenar por precio:</label>
        <select name="orden" id="orden" class="form-select d-inline w-auto" onchange="document.getElementById('formOrden').submit()">
            <option value="">Selecciona...</option>
            <option value="precio_asc" <?= ($orden === 'precio_asc') ? 'selected' : ''; ?>>Precio: &uarr;</option>
            <option value="precio_desc" <?= ($orden === 'precio_desc') ? 'selected' : ''; ?>>Precio: &darr;</option>
        </select>
    </form>

    <?php if (count($productos) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Descuento</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Activo</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['codigo']) ?></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                            <td><?= htmlspecialchars($producto['precio']) ?></td>
                            <td><?= htmlspecialchars($producto['descuento']) ?></td>
                            <td><?= htmlspecialchars($producto['stock']) ?></td>
                            <td>
                                <?php
                                $categoria = obtenerCategoriaPorId($conexion, $producto['categoria_id']);
                                echo htmlspecialchars($categoria['nombre'] ?? 'Desconocida');
                                ?>
                            </td>
                            <td><?= $producto['activo'] ? 'Sí' : 'No' ?></td>
                            <td>
                                <?php if ($producto['imagen']): ?>
                                    <img src="/TiendaOnline/imagenes/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen de <?= htmlspecialchars($producto['nombre']) ?>" width="100">
                                <?php else: ?>
                                    No hay imagen
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2"> 
                                    <a href="form_modificar_producto.php?codigo=<?= htmlspecialchars($producto['codigo']) ?>"
                                        class="btn btn-warning btn-action">
                                        <i class="fas fa-edit me-1"></i> Modificar 
                                    </a>
                                    <?php if ($producto['activo']) : ?>
                                        <a href="eliminar_producto.php?codigo=<?= htmlspecialchars($producto['codigo']) ?>&accion=desactivar"
                                            class="btn btn-danger btn-action">
                                            <i class="fas fa-ban me-1"></i> Desactivar 
                                        </a>
                                    <?php else : ?>
                                        <a href="eliminar_producto.php?codigo=<?= htmlspecialchars($producto['codigo']) ?>&accion=activar"
                                            class="btn btn-success btn-action">
                                            <i class="fas fa-check me-1"></i> Activar 
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-danger">No se encontraron productos.</p>
    <?php endif; ?>

    <!-- Paginación -->
    <nav>
        <ul class="pagination">
            <li class="page-item <?= ($paginaActual == 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=<?= $paginaActual - 1; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>">Anterior</a>
            </li>
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $i; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>">
                        <?= $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($paginaActual == $totalPaginas) ? 'disabled' : '' ?>">
                <a class="page-link" href="?pagina=<?= $paginaActual + 1; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>

    <button type="button" class="btn btn-secondary mt-3" onclick="window.location.href='panel.php'">Volver</button>
</body>

</html>