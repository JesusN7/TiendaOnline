<?php
session_start();
require_once 'conexion_db.php';

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'empleado' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['codigo']) && isset($_GET['accion'])) {
    $codigo = htmlspecialchars($_GET['codigo']);
    $accion = $_GET['accion'];

    // Definir el nuevo estado
    $nuevoEstado = ($accion === 'activar') ? 1 : 0;

    // Conectar a la base de datos
    $conexion = conectar();

    // Actualizar el estado del producto
    $stmt = $conexion->prepare("UPDATE productos SET activo = :activo WHERE codigo = :codigo");
    $stmt->execute([':activo' => $nuevoEstado, ':codigo' => $codigo]);

    // Redirigir a la página de gestión de productos
    header("Location: gestion_productos.php");
    exit();
}
