<?php
session_start();

// Obtener errores de la sesión
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

// Obtener valores previos del formulario
$valores = [
    'id' => $_SESSION['valores']['id'] ?? '',
    'nombre' => $_SESSION['valores']['nombre'] ?? '',
    'localidad' => $_SESSION['valores']['localidad'] ?? '',
    'provincia' => $_SESSION['valores']['provincia'] ?? '',
    'direccion' => $_SESSION['valores']['direccion'] ?? '',
    'telefono' => $_SESSION['valores']['telefono'] ?? '',
    'email' => $_SESSION['valores']['email'] ?? '',
];
unset($_SESSION['valores']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/TiendaOnline/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

    <main class="container mt-4 d-flex flex-grow-1">
        <div class="row w-100">
            <!-- Formulario de Registro -->
            <section class="col-md-8 offset-md-2">
                <div class="card border-light shadow-sm">
                    <div class="card-header" style="background-color: var(--color-primario); color: white;">
                        <h3>Registro de Usuario</h3>
                    </div>
                    <div class="card-body" style="background-color: var(--color-secundario);">
                        <!-- Mostrar errores -->
                        <?php if (!empty($errores)) : ?>
                            <div class="alert alert-danger">
                                <ul>
                                    <?php foreach ($errores as $error) : ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="registro.php" method="POST">

                            <div class="mb-3">
                                <label for="id" class="form-label">DNI:</label>
                                <input type="text" class="form-control" name="id" id="id" value="<?= htmlspecialchars($valores['id']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" value="<?= htmlspecialchars($valores['nombre']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="localidad" class="form-label">Localidad:</label>
                                <input type="text" class="form-control" name="localidad" id="localidad" value="<?= htmlspecialchars($valores['localidad']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="provincia" class="form-label">Provincia:</label>
                                <input type="text" class="form-control" name="provincia" id="provincia" value="<?= htmlspecialchars($valores['provincia']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección:</label>
                                <input type="text" class="form-control" name="direccion" id="direccion" value="<?= htmlspecialchars($valores['direccion']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono" id="telefono" value="<?= htmlspecialchars($valores['telefono']) ?>" required pattern="[0-9]{9}" title="Debe ser un número de 9 dígitos.">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($valores['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" name="password" id="password" required minlength="8">
                            </div>

                            <div class="mb-3 text-center">
                                <button type="submit" class="btn-personalizado">Registrarme</button>
                            </div>
                            <div class="mb-3 text-center">
                            <a href="javascript:history.back()" class="btn-personalizado" role="button">Atrás</a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>