<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errores = $_SESSION['errores'] ?? [];
    unset($_SESSION['errores']);
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperar contraseña</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/TiendaOnline/css/style.css">
    </head>

    <body class="d-flex flex-column min-vh-100">

        <main class="container mt-4 d-flex flex-grow-1">
            <div class="row w-100">
                <section class="col-md-6 offset-md-3">
                    <div class="card border-light shadow-sm">
                        <div class="card-header" style="background-color: var(--color-primario); color: white;">
                            <h3>Recuperar contraseña</h3>
                        </div>
                        <div class="card-body" style="background-color: var(--color-secundario);">
                            <?php if (!empty($errores)): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php foreach ($errores as $error): ?>
                                            <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="recuperar_password.php" method="POST">
                                <div class="mb-3">
                                    <label for="id" class="form-label">DNI:</label>
                                    <input type="text" class="form-control" name="id" id="id" required placeholder="Ej: 12345678A">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico:</label>
                                    <input type="email" class="form-control" name="email" id="email" required placeholder="ejemplo@dominio.com">
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn-personalizado">Continuar</button>
                                </div><br>
                                <div class="mb-3 text-center">
                                <a href="javascript:history.back()" class="btn-personalizado" role="button">Atrás</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
    exit; 
}

if (isset($_POST['id'], $_POST['email']) && !empty($_POST['id']) && !empty($_POST['email'])) {
    $id = htmlspecialchars(trim($_POST['id']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Validar formato del DNI
    if (!validarDni($id)) {
        $errores[] = "El DNI no es válido.";
    } else {
        try {
            $conexion = conectar();
            $sql = "SELECT id, email FROM usuarios WHERE id = :id AND email = :email";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([':id' => $id, ':email' => $email]);
            $usuario = $stmt->fetch();

            if ($usuario) {
                $_SESSION['usuario_recuperacion'] = $usuario['id'];
                header('Location: form_recuperar_password.php');
                exit;
            } else {
                $errores[] = "No se encontró un usuario con el DNI y correo electrónico proporcionados.";
            }
        } catch (PDOException $e) {
            $errores[] = "Error en la consulta: " . $e->getMessage();
            error_log("Error de PDO: " . $e->getMessage());
        }
    }
} else {
    $errores[] = "Por favor, complete todos los campos obligatorios.";
}

$_SESSION['errores'] = $errores;
header('Location: recuperar_password.php');
exit;
?>