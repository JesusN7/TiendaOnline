<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>

<body>
    <div>
        <?php
        session_start();
        if (isset($_SESSION['id'])) {
            // Usuario autenticado, mostrar imagen de perfil, enlace "Mi cuenta" y enlace "Cerrar sesión"
            $rol = $_SESSION['rol'];
            $panel = ($rol === 'admin') ? 'panel_admin.php' : (($rol === 'empleado') ? 'panel_empleado.php' : 'panel_cliente.php');
            $usuarioNombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
        ?>
            <div>
                <img src="/TiendaOnline/imagenes/user-icon.png" alt="Imagen de perfil" width="50" height="50">
                <p>Bienvenido, <?php echo htmlspecialchars($usuarioNombre); ?></p>
                <a href="<?php echo $panel; ?>">Mi cuenta</a><br>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        <?php
        } else {
            // Usuario no autenticado, mostrar formulario de inicio de sesión
        ?>
            <h2>Iniciar sesión</h2>

            <form action="login.php" method="POST">
                <label for="id">DNI: </label>
                <input type="text" name="id" id="id" required placeholder="Ej: 12345678A">

                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required placeholder="Contraseña">

                <button type="submit">Login</button>
            </form>

            <p>¿Olvidaste tu contraseña? <a href="recuperar_contraseña.php">Recuperar contraseña</a></p>
            <p>¿No tienes cuenta? <a href="registro_form.php">Regístrate aquí</a></p>
        <?php
        }
        ?>
    </div>
</body>

</html>
