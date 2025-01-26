<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="./css/errores.css">
</head>

<body>
    <div>
        <h2>Registro de Usuario</h2>

        <!-- Mostrar errores -->
        <?php if (!empty($errores)) : ?>
            <ul class="error-list">
                <?php foreach ($errores as $error) : ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="registro.php" method="POST">
            <label for="id">DNI:</label>
            <input type="text" name="id" id="id" value="<?= htmlspecialchars($id ?? '') ?>" required>

            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($nombre ?? '') ?>" required>

            <label for="localidad">Localidad:</label>
            <input type="text" name="localidad" id="localidad" value="<?= htmlspecialchars($localidad ?? '') ?>" required>

            <label for="provincia">Provincia:</label>
            <input type="text" name="provincia" id="provincia" value="<?= htmlspecialchars($provincia ?? '') ?>" required>

            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" id="direccion" value="<?= htmlspecialchars($direccion ?? '') ?>" required>

            <label for="telefono">Teléfono:</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($telefono ?? '') ?>" required pattern="[0-9]{9}" title="Debe ser un número de 9 dígitos.">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Registrarme</button>
        </form>
    </div>
</body>

</html>
