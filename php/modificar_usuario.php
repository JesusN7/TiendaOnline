<?php
session_start();
require_once 'conexion_db.php';

// Obtener información del usuario en sesión
$usuarioSesion = [
    'id' => $_SESSION['id'] ?? '',
    'rol' => $_SESSION['rol'] ?? ''
];

$conexion = conectar();

// Obtener el ID del usuario y el contexto
$idUsuario = $_GET['id'] ?? '';
$context = $_GET['context'] ?? 'gestion';  // Valor por defecto: gestión

// Determinar URL de retorno
$urlVolver = ($context === 'perfil') ? 'perfil.php' : 'gestion_usuarios.php';

// Obtener datos del usuario objetivo
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->execute([':id' => $idUsuario]);
$usuarioObjetivo = $stmt->fetch();

if (!$usuarioObjetivo) {
    die("Usuario no encontrado.");
}

// Verificar reglas para cada rol
if ($usuarioSesion['rol'] === 'cliente' && $usuarioSesion['id'] !== $idUsuario) {
    die("Un cliente solo puede modificar su propio perfil.");
}

// Validación corregida para empleados
if ($usuarioSesion['rol'] === 'empleado') {
    // Permitir modificar su propio perfil
    if ($usuarioSesion['id'] === $idUsuario) {
        // Solo puede modificar sus datos personales, no rol ni estado
    }
    // Validar si intenta modificar a otro usuario
    else {
        if ($usuarioObjetivo['rol'] !== 'cliente') {
            die("Un empleado solo puede gestionar clientes.");
        }
    }
}

// Actualizar datos al enviar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $direccion = $_POST['direccion'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $localidad = $_POST['localidad'] ?? null;
    $provincia = $_POST['provincia'] ?? null;

    $sql = "UPDATE usuarios SET 
            nombre = :nombre, 
            email = :email, 
            direccion = :direccion, 
            telefono = :telefono, 
            localidad = :localidad, 
            provincia = :provincia 
            WHERE id = :id";

    $parametros = [
        ':nombre' => $nombre,
        ':email' => $email,
        ':direccion' => $direccion,
        ':telefono' => $telefono,
        ':localidad' => $localidad,
        ':provincia' => $provincia,
        ':id' => $idUsuario
    ];

    // Solo admin puede modificar roles de otros usuarios
    if ($usuarioSesion['rol'] === 'admin' && isset($_POST['rol']) && $usuarioSesion['id'] !== $idUsuario) {
        $sql = str_replace("WHERE id = :id", ", rol = :rol WHERE id = :id", $sql);
        $parametros[':rol'] = $_POST['rol'];
    }

    try {
        $stmt = $conexion->prepare($sql);
        $stmt->execute($parametros);
        $_SESSION['exito'] = "Cambios guardados correctamente";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar: " . $e->getMessage();
    }

    header("Location: $urlVolver");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Usuario</title>
</head>

<body>
    <div class="container mt-4">
        <h2>Modificar Usuario</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['direccion']) ?>">
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['telefono']) ?>">
            </div>

            <div class="mb-3">
                <label for="localidad" class="form-label">Localidad</label>
                <input type="text" name="localidad" id="localidad" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['localidad']) ?>">
            </div>

            <div class="mb-3">
                <label for="provincia" class="form-label">Provincia</label>
                <input type="text" name="provincia" id="provincia" class="form-control"
                    value="<?= htmlspecialchars($usuarioObjetivo['provincia']) ?>">
            </div>

            <?php if ($usuarioSesion['rol'] === 'admin' && $usuarioSesion['id'] !== $idUsuario): ?>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" id="rol" class="form-select">
                        <option value="cliente" <?= $usuarioObjetivo['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
                        <option value="empleado" <?= $usuarioObjetivo['rol'] === 'empleado' ? 'selected' : '' ?>>Empleado</option>
                        <option value="admin" <?= $usuarioObjetivo['rol'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="<?= $urlVolver ?>" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>