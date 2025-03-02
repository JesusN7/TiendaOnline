<?php
session_start();
require_once 'conexion_db.php';

if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['cliente', 'empleado', 'admin'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$rol_usuario = $_SESSION['rol'];

try {
    $conexion = conectar();
    $sql = "SELECT id, nombre, email, telefono, direccion, localidad, provincia FROM usuarios WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/TiendaOnline/css/perfil.css">
</head>

<body class="d-flex flex-column min-vh-100">
    <main class="container-fluid flex-grow-1 p-4" style="background-color: var(--color-secundario);">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="card shadow-lg border-0">
                    <!-- Cabecera -->
                    <div class="card-header py-3" style="background-color: var(--color-primario);">
                        <h2 class="text-white mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            Perfil de <?= ucfirst($rol_usuario) ?>
                        </h2>
                    </div>

                    <!-- Cuerpo -->
                    <div class="card-body p-4" style="background-color: var(--color-fondo);">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php else: ?>
                            <div class="row g-4">
                                <!-- Columna Izquierda -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-person-fill fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">DNI</h5>
                                            <p class="mb-0"><?= htmlspecialchars($usuario['id']) ?></p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-person fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">Nombre</h5>
                                            <p class="mb-0"><?= htmlspecialchars($usuario['nombre']) ?></p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-envelope fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">Email</h5>
                                            <p class="mb-0"><?= htmlspecialchars($usuario['email']) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna Derecha -->
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-telephone fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">Teléfono</h5>
                                            <p class="mb-0"><?= htmlspecialchars($usuario['telefono'] ?? 'No especificado') ?></p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-geo-alt fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">Dirección</h5>
                                            <p class="mb-0"><?= htmlspecialchars($usuario['direccion'] ?? 'No especificado') ?></p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <i class="bi bi-geo fs-4 me-3" style="color: var(--color-primario);"></i>
                                        <div>
                                            <h5 class="mb-0" style="color: var(--color-texto);">Localidad/Provincia</h5>
                                            <p class="mb-0">
                                                <?= htmlspecialchars($usuario['localidad'] ?? 'No especificado') ?>,
                                                <?= htmlspecialchars($usuario['provincia'] ?? 'No especificado') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Pie de Tarjeta -->
                    <div class="card-footer p-3" style="background-color: var(--color-primario);">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- En el botón "Modificar datos" -->
                            <a href="modificar_usuario.php?id=<?= urlencode($usuario['id']) ?>&context=perfil"
                                class="btn btn-light hover-transform">
                                <i class="bi bi-pencil-square me-2"></i>Modificar datos
                            </a>

                            <div class="d-flex gap-2">
                                <?php if ($rol_usuario === 'cliente'): ?>
                                    <a href="eliminar_usuario.php?id=<?= urlencode($usuario['id']) ?>&accion=desactivar&context=perfil"
                                        class="btn btn-danger hover-transform"
                                        onclick="return confirm('¿Seguro que deseas eliminar tu cuenta?');">
                                        <i class="bi bi-trash me-2"></i>Eliminar cuenta
                                    </a>


                                <?php endif; ?>
                                <a href="panel.php" class="btn btn-secondary hover-transform">
                                    <i class="bi bi-box-arrow-right me-2"></i>Volver al panel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>