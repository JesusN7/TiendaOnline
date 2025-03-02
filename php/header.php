<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="/TiendaOnline/imagenes/luz1.png" alt="Logo" class="img-fluid">
            </a>

            <!-- Carrito y menú móvil -->
            <div class="d-flex align-items-center">
                <!-- Icono del carrito -->
                <a href="carrito.php" class="btn btn-light position-relative me-3">
                    🛒 Carrito
                    <?php if (!empty($_SESSION['carrito'])): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= array_sum(array_column($_SESSION['carrito'], 'cantidad')) ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Botón menú móvil -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuNavegacion">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Menú principal -->
            <div class="collapse navbar-collapse" id="menuNavegacion">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Quiénes somos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>