<?php
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
include 'logica_productos.php';
?>

<!-- Contenedor de filtros (búsqueda y ordenamiento) -->
<div class="filtros-container">
    <!-- Formulario de búsqueda -->
    <form action="index.php" method="get" class="busqueda-form">
        <input type="hidden" name="categoria" value="<?= isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : '' ?>">
        <input type="hidden" name="orden" value="<?= isset($_GET['orden']) ? htmlspecialchars($_GET['orden']) : '' ?>">

        <div class="busqueda-container">
            <button type="submit" class="boton-lupa">
                <img src="/TiendaOnline/imagenes/lupa.png" alt="Buscar" class="icono-busqueda">
            </button>
            <input type="text" name="buscar" placeholder="Buscar productos...">
        </div>
    </form>

    <!-- Formulario de ordenamiento -->
    <form action="index.php" method="get" id="formOrden" class="orden-form">
        <input type="hidden" name="buscar" value="<?= htmlspecialchars($busqueda); ?>">
        <input type="hidden" name="categoria" value="<?= isset($_GET['categoria']) ? htmlspecialchars($_GET['categoria']) : '' ?>">
        <label for="orden">Ordenar por &raquo;</label>
        <select name="orden" id="orden" onchange="document.getElementById('formOrden').submit()">
            <option value="">Selecciona...</option>
            <option value="precio_asc" <?= ($orden === 'precio_asc') ? 'selected' : ''; ?>>Precio: &uarr;</option>
            <option value="precio_desc" <?= ($orden === 'precio_desc') ? 'selected' : ''; ?>>Precio: &darr;</option>
        </select>
    </form>
</div>

<hr>

<!-- Mostrar productos -->
<div class="productos-grid">
    <?php if (count($productos) > 0): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="producto">
                <!-- Título del producto -->
                <h2 class="titulo-producto"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                <!-- Contenedor de imagen con etiqueta de descuento -->
                <div class="imagen-container">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="/TiendaOnline/imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>"
                            alt="Imagen de <?php echo htmlspecialchars($producto['nombre']); ?>"
                            class="producto-imagen">
                    <?php endif; ?>

                    <!-- Etiqueta de descuento -->
                    <?php if ($producto['descuento'] > 0): ?>
                        <span class="etiqueta-descuento"><?= number_format($producto['descuento'], 0); ?>%</span>
                    <?php endif; ?>
                </div>

                <!-- Precio y descuento -->
                <div>
                    <?php if ($producto['descuento'] > 0): ?>
                        <span class="precio-original">€<?php echo number_format($producto['precio'], 2); ?></span>
                        <span class="precio-con-descuento">€<?php echo number_format($producto['precio'] * (1 - $producto['descuento'] / 100), 2); ?></span>
                    <?php else: ?>
                        <span class="precio-con-descuento">€<?php echo number_format($producto['precio'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Descripción del producto -->
                <p class="descripcion-producto"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>

                <!-- Stock con colores -->
                <div class="stock-container">
                    <p class="stock <?= $producto['stock'] == 0 ? 'stock-rojo' : ($producto['stock'] < 5 ? 'stock-naranja' : 'stock-verde'); ?>">
                        Stock: <?php echo htmlspecialchars($producto['stock']); ?>
                    </p>

                    <!-- Botón Añadir -->
                    <?php
                    if ($producto['stock'] > 0): ?>
                        <div class="stock-container">
                            <form method="POST" action="anadir_carrito.php">
                                <input type="hidden" name="codigo_producto" value="<?= $producto['codigo'] ?>">
                                <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" class="cantidad-input">
                                <button type="submit" class="boton-anadir">Añadir</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <button class="boton-anadir boton-desactivado" disabled>Sin stock</button>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No hay productos disponibles.</p>
    <?php endif; ?>
</div>

<!-- Paginación -->
<div class="paginacion">
    <!-- Botón Anterior -->
    <?php if ($paginaActual > 1): ?>
        <a href="index.php?pagina=<?= $paginaActual - 1; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>&categoria=<?= isset($_GET['categoria']) ? $_GET['categoria'] : '' ?>">Anterior</a>
    <?php else: ?>
        <span>Anterior</span>
    <?php endif; ?>

    <!-- Mostrar páginas numeradas -->
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="index.php?pagina=<?= $i; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>&categoria=<?= isset($_GET['categoria']) ? $_GET['categoria'] : '' ?>"
            class="<?= ($i === $paginaActual) ? 'activo' : ''; ?>">
            <?= $i; ?>
        </a>
    <?php endfor; ?>

    <!-- Botón Siguiente -->
    <?php if ($paginaActual < $totalPaginas): ?>
        <a href="index.php?pagina=<?= $paginaActual + 1; ?>&orden=<?= urlencode($orden); ?>&buscar=<?= urlencode($busqueda); ?>&categoria=<?= isset($_GET['categoria']) ? $_GET['categoria'] : '' ?>">Siguiente</a>
    <?php else: ?>
        <span>Siguiente</span>
    <?php endif; ?>
</div>