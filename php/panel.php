<?php
session_start();

if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - <?= ucfirst($rol) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/TiendaOnline/css/style.css">
    <link rel="stylesheet" href="/TiendaOnline/css/panel.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <main class="container-fluid flex-grow-1 d-flex align-items-center p-4" style="background-color: var(--color-secundario);">
        <div class="row justify-content-center w-100">
            <!-- Cabecera del Panel -->
            <div class="col-12 text-center mb-5">
                <h1 class="display-4 fw-bold" style="color: var(--color-primario);">
                    <i class="bi bi-speedometer2 me-3"></i>
                    Panel de Control - <?= ucfirst($rol) ?>
                </h1>
            </div>

            <!-- Tarjetas de Opciones -->
            <div class="col-12">
                <div class="row justify-content-center g-4">
                    <!-- Opción común para todos -->
                    <div class="col-auto">
                        <a href="perfil.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                            <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                <i class="bi bi-person-gear display-4 mb-3" style="color: var(--color-primario);"></i>
                                <h3 class="h4 mb-0" style="color: var(--color-texto);">Mi Perfil</h3>
                            </div>
                        </a>
                    </div>

                    <?php if ($rol === 'cliente'): ?>
                        <!-- Opciones Cliente -->
                        <div class="col-auto">
                            <a href="gestion_pedidos.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-receipt-cutoff display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Mis Pedidos</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="carrito.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-cart display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Mi Carrito</h3>
                                </div>
                            </a>
                        </div>

                    <?php elseif ($rol === 'empleado'): ?>
                        <!-- Opciones Empleado -->
                        <div class="col-auto">
                            <a href="gestion_usuarios.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-people display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Usuarios</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_productos.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-box-seam display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Productos</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_categorias.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-tags display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Categorías</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_pedidos.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-truck display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Pedidos</h3>
                                </div>
                            </a>
                        </div>

                    <?php elseif ($rol === 'admin'): ?>
                        <!-- Opciones Administrador -->
                        <div class="col-auto">
                            <a href="gestion_usuarios.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-people display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Usuarios</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_productos.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-boxes display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Productos</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_categorias.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-diagram-3 display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Categorías</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="gestion_pedidos.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-clipboard-check display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Pedidos</h3>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-auto">
                            <a href="informes.php" class="card option-card border-0 shadow-lg hover-transform" style="min-width: 250px; max-width: 300px; height: 200px;">
                                <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                                    <i class="bi bi-graph-up-arrow display-4 mb-3" style="color: var(--color-primario);"></i>
                                    <h3 class="h4 mb-0" style="color: var(--color-texto);">Informes</h3>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Botón de Cerrar Sesión -->
            <div class="col-12 text-center mt-5">
                <a href="index.php" class="btn btn-lg btn-primario-custom px-5 py-3">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Volver
                </a>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>