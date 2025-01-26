<?php
include 'logica_productos.php';
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Productos</title>
</head>

<body>

    <!-- Formulario de búsqueda -->
    <form action="listar_productos.php" method="get"> <!-- Asegúrate de que apunta a listar_productos.php -->
        <input type="text" id="buscar" name="buscar" placeholder="Buscar productos...">
        <button type="submit">Buscar</button>
    </form>

    <!-- Formulario ordenamiento -->
    <form action="listar_productos.php" method="get" id="formOrden">
    <input type="hidden" name="buscar" value="<?php echo htmlspecialchars($busqueda); ?>">
        <label for="orden">Ordenar por &raquo;</label>
        <select name="orden" id="orden" onchange="document.getElementById('formOrden').submit()">
            <option value="">Selecciona...</option>
            <option value="precio_asc" <?php echo ($orden === 'precio_asc') ? 'selected' : ''; ?>>
                Precio: &uarr;
            </option>
            <option value="precio_desc" <?php echo ($orden === 'precio_desc') ? 'selected' : ''; ?>>
                Precio: &darr;
            </option>
        </select>
    </form>

    <hr>

    <!-- Mostrar productos -->
    <?php if (count($productos) > 0): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="producto">
                <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>

                <!-- Mostrar la imagen del producto si existe -->
                <?php if ($producto['imagen']): ?>
                    <img src="/TiendaOnline/imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>" width="150">
                <?php endif; ?>

                <div class="precio">
                    <!-- Precio original, si tiene descuento -->
                    <?php if ($producto['descuento'] > 0): ?>
                        <span class="precio-original">€<?php echo number_format($producto['precio'], 2); ?></span>
                        <span class="precio-con-descuento">€<?php echo number_format($producto['precio'] * (1 - $producto['descuento'] / 100), 2); ?></span>
                        <span class="etiqueta-descuento"><?php echo number_format($producto['descuento'], 0); ?>% DTO.</span>
                    <?php else: ?>
                        <span class="precio-con-descuento">€<?php echo number_format($producto['precio'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                <p>Stock: <?php echo htmlspecialchars($producto['stock']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>

    <!-- Paginación -->
    <div>
        <!-- Botón Anterior -->
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?php echo $paginaActual - 1; ?>">Anterior</a>
        <?php else: ?>
            <span>Anterior</span> <!-- Deshabilitar el botón si estamos en la primera página -->
        <?php endif; ?>

        <!-- Mostrar páginas numeradas -->
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a href="?pagina=<?php echo $i; ?>" <?php echo ($i === $paginaActual) ? 'class="activo"' : ''; ?>>
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Botón Siguiente -->
        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?php echo $paginaActual + 1; ?>">Siguiente</a>
        <?php else: ?>
            <span>Siguiente</span> <!-- Deshabilitar el botón si estamos en la última página -->
        <?php endif; ?>
    </div>

</body>

</html>