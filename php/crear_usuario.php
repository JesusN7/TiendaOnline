<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Verificar permisos
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'empleado'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$rolUsuario = $_SESSION['rol'];

// Recoger datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$email = $_POST['email'];
$password = $_POST['password'];
$direccion = $_POST['direccion'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$localidad = $_POST['localidad'] ?? null;
$provincia = $_POST['provincia'] ?? null;
$rol = ($rolUsuario === 'admin') ? $_POST['rol'] : 'cliente';

// Validaciones
if (!validarDni($id) || !validarEmail($email) || !longPassword($password) || !validarCampos($nombre, $direccion, $localidad, $provincia, $email)) {
    header("Location: form_crear_usuario.php?error=Datos inválidos");
    exit();
}
if ($telefono && !validarTelefono($telefono)) {
    header("Location: form_crear_usuario.php?error=Teléfono inválido");
    exit();
}

try {
    $conexion = conectar();

    // Verificar si el usuario ya existe
    $stmt = $conexion->prepare("SELECT id, email FROM usuarios WHERE id = ? OR email = ?");
    $stmt->execute([$id, $email]);
    if ($stmt->fetch()) {
        header("Location: form_crear_usuario.php?error=El DNI/NIE o email ya está registrado");
        exit();
    }

    // Insertar usuario
    $stmt = $conexion->prepare("INSERT INTO usuarios (id, nombre, email, password, direccion, telefono, localidad, provincia, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id, $nombre, $email, password_hash($password, PASSWORD_DEFAULT), $direccion, $telefono, $localidad, $provincia, $rol]);

    header("Location: gestion_usuarios.php?mensaje=Usuario creado correctamente");
    exit();
} catch (PDOException $e) {
    header("Location: form_crear_usuario.php?error=Error al crear usuario");
    exit();
}
