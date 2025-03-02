<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Mostrar mensajes de éxito
if (isset($_SESSION['mensaje_exito'])) {
    $mensaje_exito = $_SESSION['mensaje_exito'];
    unset($_SESSION['mensaje_exito']);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Luz De Tus Sueños</title>

    <link rel="icon" type="image/png" href="/TiendaOnline/imagenes/favicon_io/favicon-32x32.png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/TiendaOnline/css/style.css">
    <link rel="stylesheet" href="/TiendaOnline/css/header.css">
    <link rel="stylesheet" href="/TiendaOnline/css/menu_categorias.css">
    <link rel="stylesheet" href="/TiendaOnline/css/productos.css">
    <link rel="stylesheet" href="/TiendaOnline/css/login_form.css">
    <link rel="stylesheet" href="/TiendaOnline/css/footer.css">
    <link rel="stylesheet" href="/TiendaOnline/css/paginacion.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <!-- Mostrar mensaje de éxito -->
    <?php if (!empty($mensaje_exito)): ?>
        <div class="alert alert-success alert-dismissible fade show text-center mb-0" role="alert">
            <?= htmlspecialchars($mensaje_exito) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <?php include 'header.php'; ?>

    <main class="container mt-4 d-flex flex-grow-1">
        <div class="row w-100">
            <!-- Menú de Categorías (Izquierda) -->
            <aside class="col-md-2">
                <?php include 'menu_categorias.php'; ?>
            </aside>

            <!-- Centro (Productos) -->
            <section class="col-md-7">
                <?php
                $categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
                include 'listar_productos.php';
                ?>
            </section>

            <!-- Login (Derecha) -->
            <aside class="col-md-3">
                <?php include 'login_form.php'; ?>
            </aside>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personalizados -->
    <script src="/TiendaOnline/js/lupa_productos.js"></script>

</body>

</html>