<?php
session_start();
require_once 'conexion_db.php';

// Verificar sesión y rol
if (!isset($_SESSION['rol'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$idSesion = $_SESSION['id'];
$rolSesion = $_SESSION['rol'];

// Verificar parámetros obligatorios: id y accion
if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    header("Location: gestion_usuarios.php?mensaje=Error en los parámetros");
    exit();
}

$idUsuario = $_GET['id'];
$accion = $_GET['accion'];
$context = $_GET['context'] ?? 'gestion';

// Validar acción permitida
if (!in_array($accion, ['activar', 'desactivar'])) {
    header("Location: gestion_usuarios.php?mensaje=Acción no válida");
    exit();
}

$nuevoEstado = ($accion === 'activar') ? 1 : 0;

try {
    $conexion = conectar();

    // Cliente desactiva SU propia cuenta desde perfil
    if ($rolSesion === 'cliente' && $idSesion == $idUsuario && $context === 'perfil' && $accion === 'desactivar') {
        $stmt = $conexion->prepare("UPDATE usuarios SET activo = 0 WHERE id = :id");
        $stmt->execute([':id' => $idSesion]);
        
        session_destroy();
        header("Location: index.php?mensaje=Cuenta desactivada correctamente");
        exit();
    }

    // Resto de lógica para empleados y admin...
    
    // Empleados solo pueden gestionar clientes
    if ($rolSesion === 'empleado') {
        $stmt = $conexion->prepare("SELECT rol FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $idUsuario]);
        $usuario = $stmt->fetch();
        
        if ($usuario['rol'] !== 'cliente') {
            header("Location: gestion_usuarios.php?mensaje=No tienes permisos");
            exit();
        }
    }

    // Admin no puede desactivarse a sí mismo
    if ($rolSesion === 'admin' && $idUsuario == $idSesion && $nuevoEstado === 0) {
        header("Location: gestion_usuarios.php?mensaje=No puedes desactivarte a ti mismo");
        exit();
    }

    // Actualizar estado
    $stmt = $conexion->prepare("UPDATE usuarios SET activo = :activo WHERE id = :id");
    $stmt->execute([':activo' => $nuevoEstado, ':id' => $idUsuario]);

    // Redirigir según contexto
    if ($context === 'perfil') {
        session_destroy();
        header("Location: index.php?mensaje=Cuenta desactivada");
    } else {
        header("Location: gestion_usuarios.php?mensaje=Estado actualizado");
    }
    exit();

} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}