<footer class="footer mt-auto py-4 bg-light border-top">
    <div class="container">
        <div class="row">
            <!-- Derechos de autor -->
            <div class="col-12 text-center">
                <p class="mb-0">&copy; <?php echo date("Y"); ?> La Luz De Tus Sueños - Todos los derechos reservados.</p>
            </div>
        </div>
        <div class="row mt-3">
            <!-- Enlaces de navegación -->
            <div class="col-md-6 text-center text-md-start">
                <nav>
                    <ul class="list-unstyled mb-0">
                        <li><a href="politica_privacidad.php" class="text-decoration-none">Política de Privacidad</a></li>
                        <li><a href="terminos_condiciones.php" class="text-decoration-none">Términos y Condiciones</a></li>
                        <li><a href="contacto.php" class="text-decoration-none">Contacto</a></li>
                    </ul>
                </nav>
            </div>

            <!-- Redes sociales -->
            <div class="col-md-6 text-center text-md-end">
                <div class="redes-sociales">
                    <?php
                    $redes = ['twitter', 'instagram', 'facebook', 'youtube'];
                    foreach ($redes as $red) {
                        echo '<a href="https://' . $red . '.com" target="_blank" class="me-2">
                                <img src="/TiendaOnline/imagenes/' . $red . '.png" alt="' . ucfirst($red) . '" width="30">
                              </a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>