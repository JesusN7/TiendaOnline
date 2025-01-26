<?php
session_start();

require_once 'conexion_db.php';
require_once 'funciones.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'], $_POST['password']) || empty($_POST['id']) || empty($_POST['password'])) {
        die("Por favor, complete todos los campos.");
    }

    $id = htmlspecialchars(trim($_POST['id']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!validarDni($id)) {
        echo "El DNI no es válido.";
        exit();
    }

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
            echo "Credenciales incorrectas.";
        }
    } catch (PDOException $e) {
        die("Error al procesar la solicitud. Inténtelo más tarde.");
    }
}

