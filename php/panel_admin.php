<?php
session_start();
include 'conexion_db.php';

// Verificamos que el usuario sea admin
if ($_SESSION['rol'] != 'admin') {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
</head>

<body>
    <h1>Bienvenido al Panel de Administración</h1>
    <nav>
        <ul>
            <li><a href="gestion_pedidos.php">Gestión de Pedidos</a></li>
            <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
            <li><a href="gestion_productos.php">Gestión de Productos</a></li>
            <li><a href="informes.php">Informes de Ventas</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</body>

</html>