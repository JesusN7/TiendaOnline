<?php
require_once 'conexion_db.php';
require_once 'funciones.php';

$conexion = conectar();
$categorias = obtenerCategorias($conexion);
?>

<div class="menu-categorias">
    <?php if (!empty($categorias)): ?>
        <ul class="lista-categorias list-unstyled">
            <?php foreach ($categorias as $categoria): ?>
                <li class="categoria border-bottom pb-2">
                    <a href="#" class="categoria-principal d-flex justify-content-between align-items-center text-decoration-none"
                        data-target="subcategoria-<?= $categoria['id'] ?>">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                        <img src="/TiendaOnline/imagenes/flecha2.png" class="icono-flecha" alt="Desplegar">
                    </a>
                    <?php if (!empty($categoria['subcategorias'])): ?>
                        <ul id="subcategoria-<?= $categoria['id'] ?>" class="subcategorias list-unstyled ps-3" style="display: none;">
                            <?php foreach ($categoria['subcategorias'] as $subcategoria): ?>
                                <li>
                                    <a href="index.php?categoria=<?= $subcategoria['id'] ?>" class="text-decoration-none d-block py-1">
                                        <?= htmlspecialchars($subcategoria['nombre']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="mensaje-vacio text-center text-muted">No hay categorías disponibles.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Menú de categorías cargado.");

        document.querySelectorAll(".categoria-principal").forEach(function(categoria) {
            categoria.addEventListener("click", function(event) {
                event.preventDefault();

                let subcategoria = document.getElementById(this.getAttribute("data-target"));
                let flecha = this.querySelector(".icono-flecha");

                if (!subcategoria) return; // Evita errores si no hay subcategorías

                let estaAbierta = subcategoria.style.display === "block";

                // Cerrar todas las subcategorías antes de abrir/cerrar la actual
                document.querySelectorAll(".subcategorias").forEach(function(abierta) {
                    abierta.style.display = "none";
                    let flechaAbierta = abierta.previousElementSibling.querySelector(".icono-flecha");
                    if (flechaAbierta) {
                        flechaAbierta.classList.remove("rotada");
                    }
                    abierta.previousElementSibling.classList.remove("activa");
                });

                // Si estaba cerrada, la abrimos; si estaba abierta, la cerramos
                subcategoria.style.display = estaAbierta ? "none" : "block";
                flecha.classList.toggle("rotada", !estaAbierta);
                this.classList.toggle("activa", !estaAbierta);
            });
        });
    });
</script>