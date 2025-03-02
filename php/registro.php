<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar datos
    $id = trim($_POST['id'] ?? '');
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $localidad = htmlspecialchars(trim($_POST['localidad'] ?? ''));
    $provincia = htmlspecialchars(trim($_POST['provincia'] ?? ''));
    $direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password'] ?? '');

    $_SESSION['valores'] = compact('id', 'nombre', 'localidad', 'provincia', 'direccion', 'telefono', 'email');

    // Validaciones
    if (!validarDni($id)) {
        $errores[] = "El DNI no es válido.";
    }
    if (!validarTelefono($telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }
    if (!validarEmail($email)) {
        $errores[] = "El email no es válido.";
    }
    if (!longPassword($password)) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    if (!validarCampos($nombre, $direccion, $localidad, $provincia, $email)) {
        $errores[] = "Los datos exceden el tamaño permitido.";
    }

    // Verificar si DNI o email ya existen con una sola consulta
    if (empty($errores)) {
        try {
            $conexion = conectar();
            $sql = "SELECT id, email FROM usuarios WHERE id = :id OR email = :email";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id, ':email' => $email]);
            $usuarioExistente = $stmt->fetch();

            if ($usuarioExistente) {
                if ($usuarioExistente['id'] === $id) {
                    $errores[] = "El DNI ya está registrado.";
                }
                if ($usuarioExistente['email'] === $email) {
                    $errores[] = "El email ya está registrado.";
                }
            }
        } catch (PDOException $e) {
            $errores[] = "Error al verificar usuario.";
            error_log("Error de PDO: " . $e->getMessage());
        }
    }

    // Registrar usuario si no hay errores
    if (empty($errores)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (id, nombre, localidad, provincia, direccion, telefono, email, password, rol)
                    VALUES (:id, :nombre, :localidad, :provincia, :direccion, :telefono, :email, :password, 'cliente')";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':localidad' => $localidad,
                ':provincia' => $provincia,
                ':direccion' => $direccion,
                ':telefono' => $telefono,
                ':email' => $email,
                ':password' => $hashedPassword,
            ]);

            $_SESSION['mensaje_exito'] = "Tu cuenta ha sido creada con éxito.";
            unset($_SESSION['valores']); // Limpiar valores previos
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar usuario.";
            error_log("Error de PDO: " . $e->getMessage());
            
        }
    }

    $_SESSION['errores'] = $errores;
    header('Location: registro_form.php');
    exit;
}
