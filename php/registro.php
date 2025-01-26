<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $id = htmlspecialchars(trim($_POST['id'] ?? ''));
    $nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $localidad = htmlspecialchars(trim($_POST['localidad'] ?? ''));
    $provincia = htmlspecialchars(trim($_POST['provincia'] ?? ''));
    $direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''));
    $telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));

    // Validar DNI
    if (!validarDni($id)) {
        $errores[] = "El DNI no es válido.";
    }

    // Validar que el DNI no exista en la base de datos
    try {
        $conexion = conectar();
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);

        if ($stmt->fetch()) {
            $errores[] = "El DNI ya está registrado.";
        }
    } catch (PDOException $e) {
        $errores[] = "Error al verificar el DNI: " . $e->getMessage();
    }

    // Validar teléfono
    if (!validarTelefono($telefono)) {
        $errores[] = "El teléfono debe tener 9 dígitos.";
    }

    // Validar email
    if (!validarEmail($email)) {
        $errores[] = "El email no es válido.";
    }

    // Validar longitud mínima de la contraseña.
    if (!longPassword($password)) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Validar tamaño de los campos
    if (!validarCampos($nombre, $direccion, $localidad, $provincia, $email)) {
        $errores[] = "Los datos exceden el tamaño permitido.";
    }

    // Si no hay errores, registrar al usuario
    if (empty($errores)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
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

            // Mostrar mensaje de éxito con enlace para iniciar sesión
            echo "<p>Tu cuenta ha sido creada con éxito.</p>";
            echo "<p><a href='login_form.php'>Iniciar sesión</a></p>";
            exit();

        } catch (PDOException $e) {
            $errores[] = "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
