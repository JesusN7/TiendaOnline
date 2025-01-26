<?php
require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si es una solicitud para cambiar la contraseña (formulario de recuperación)
    if (!empty($_POST['password']) && !empty($_POST['id'])) {
        $id = htmlspecialchars(trim($_POST['id']));
        $newPassword = htmlspecialchars(trim($_POST['password']));

        // Actualizar la contraseña
        try {
            $conexion = conectar();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET password = :password WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':password' => $hashedPassword, ':id' => $id]);

            if ($stmt->rowCount() > 0) {
                echo "Tu contraseña ha sido actualizada con éxito.";
                echo "<p><a href='login_form.php'>Iniciar sesión</a></p>";
                exit();
            } else {
                $errores[] = "No se encontró ningún usuario con el DNI proporcionado.";
            }
        } catch (PDOException $e) {
            $errores[] = "Error en la consulta: " . $e->getMessage();
        }
    }
    // Verificar si es una solicitud para enviar el formulario de recuperación
    else if (!empty($_POST['id']) && !empty($_POST['email'])) {
        $id = htmlspecialchars(trim($_POST['id']));
        $email = htmlspecialchars(trim($_POST['email']));

        // Validar DNI
        if (!validarDni($id)) {
            $errores[] = "El DNI no es válido.";
        } else {
            try {
                $conexion = conectar();
                $sql = "SELECT * FROM usuarios WHERE id = :id AND email = :email";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $id, ':email' => $email]);
                $usuario = $stmt->fetch();

                if ($usuario) {
                    // Mostrar formulario para cambiar la contraseña
                    include 'form_recuperar_contraseña.php';
                    exit();
                } else {
                    $errores[] = "No se ha encontrado el DNI y el correo electrónico proporcionado.";
                }
            } catch (PDOException $e) {
                $errores[] = "Error en la consulta: " . $e->getMessage();
            }
        }
    } else {
        $errores[] = "Por favor, complete todos los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
</head>

<body>
    <h2>Recuperar contraseña</h2>

    <!-- Mostrar errores -->
    <?php if (!empty($errores)) : ?>
        <ul style="color: red;">
            <?php foreach ($errores as $error) : ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div>
        <form action="recuperar_contraseña.php" method="POST">
            <label for="id">DNI:</label>
            <input type="text" name="id" required placeholder="Ej: 12345678A">

            <label for="email">Email:</label>
            <input type="email" name="email" required placeholder="tuemail@example.com">

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>

</html>