<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario recuperar contraseña</title>
</head>

<body>
    <div>
        <h2>Restablecer contraseña</h2>

        <form action="recuperar_contraseña.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']); ?>">

            <label for="password">Nueva contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Restablecer contraseña</button>
        </form>
    </div>
</body>

</html>