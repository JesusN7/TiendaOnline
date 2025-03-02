<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'], $_POST['password']) || empty($_POST['id']) || empty($_POST['password'])) {
        $errores[] = "Por favor, complete todos los campos.";
    } else {
        $id = htmlspecialchars(trim($_POST['id']));
        $password = htmlspecialchars(trim($_POST['password']));

        if (!validarDni($id)) {
            $errores[] = "El DNI no es válido.";
        } else {
            try {
                $conexion = conectar();

                $sql = "SELECT * FROM usuarios WHERE id = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $id]);
                $usuario = $stmt->fetch();

                if ($usuario && password_verify($password, $usuario['password'])) {
                    // Variables de sesión
                    $_SESSION['id'] = $usuario['id'];
                    $_SESSION['rol'] = $usuario['rol'];
                    $_SESSION['nombre'] = $usuario['nombre'];

                    header("Location: index.php");
                    exit();
                } else {
                    $errores[] = "Credenciales incorrectas.";
                }
            } catch (PDOException $e) {
                $errores[] = "Error al procesar la solicitud. Inténtelo más tarde.";
                error_log("Error de PDO: " . $e->getMessage());
            }
        }
    }

    // Guardar errores en la sesión si los hay y redirigir al formulario
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
    }
    header("Location: login_form.php");
    exit();
}
