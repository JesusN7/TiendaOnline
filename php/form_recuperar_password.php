<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Si no hay sesión de recuperación, redirigir
if (!isset($_SESSION['usuario_recuperacion'])) {
    header('Location: recuperar_password.php');
    exit;
}

$errores = [];

// Procesar nueva contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $id = $_SESSION['usuario_recuperacion'];
    $newPassword = htmlspecialchars(trim($_POST['password']));

    if (!longPassword($newPassword)) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        try {
            $conexion = conectar();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET password = :password WHERE id = :id";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':password' => $hashedPassword, ':id' => $id]);

            if ($stmt->rowCount() > 0) {
                unset($_SESSION['usuario_recuperacion']);
                $_SESSION['mensaje_exito'] = "Tu contraseña ha sido actualizada.";
                header('Location: login_form.php');
                exit;
            } else {
                $errores[] = "No se pudo actualizar la contraseña. Intenta nuevamente.";
            }
        } catch (PDOException $e) {
            $errores[] = "Error en la base de datos.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nueva Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/TiendaOnline/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <main class="container mt-5 d-flex flex-grow-1 justify-content-center align-items-center">
        <div class="card shadow-lg p-4" style="max-width: 450px; width: 100%;">
            <div class="card-header text-center" style="background-color: var(--color-primario); color: white;">
                <h4>Restablecer Contraseña</h4>
            </div>
            <div class="card-body" style="background-color: var(--color-secundario);">
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errores as $error): ?>
                            <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nueva Contraseña:</label>
                        <input type="password" name="password" class="form-control" required minlength="8" placeholder="Mínimo 8 caracteres">
                    </div>
                    <button type="submit" class="btn btn-personalizado w-100">Actualizar</button>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
