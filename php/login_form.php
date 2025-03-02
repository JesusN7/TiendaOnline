<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener errores de la sesión
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>

<body>
    <div class="container d-flex justify-content-center mt-3">
        <div class="login-container p-4 shadow-lg">

            <?php if (isset($_SESSION['id'])):
                $usuarioNombre = $_SESSION['nombre'] ?? 'Usuario';
            ?>
                <div class="perfil text-center">
                    <img src="/TiendaOnline/imagenes/user-icon.png" alt="Perfil" class="mb-3">
                    <p class="fw-bold">Bienvenido, <strong><?php echo htmlspecialchars($usuarioNombre); ?></strong></p>
                    <a href="panel.php" class="btn w-100 mb-2">Mi cuenta</a>
                    <a href="logout.php" class="btn btn-danger w-100">Cerrar sesión</a>
                </div>
            <?php else: ?>
                <h3 class="text-center mb-3">Iniciar sesión</h3>

                <?php if (!empty($errores)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errores as $error): ?>
                            <p class="m-0"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <label for="id" class="fw-bold">DNI:</label>
                    <div class="input-container">
                        <i class="bi bi-person"></i>
                        <input type="text" name="id" id="id" class="form-control" required placeholder="Ej: 12345678A">
                    </div>

                    <label for="password" class="fw-bold">Contraseña:</label>
                    <div class="input-container">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Contraseña">
                    </div>

                    <button type="submit" class="btn w-100">Login</button>
                </form>

                <p class="text-center mt-3">
                    <a href="recuperar_password.php" class="link-personalizado">¿Olvidaste tu contraseña?</a>
                </p>
                <p class="text-center">
                    <a href="registro_form.php" class="link-personalizado">¿No tienes cuenta? Regístrate</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>