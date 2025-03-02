<?php
session_start();
require_once 'conexion_db.php';

// Verificar permisos, solo admin y empleado pueden crear usuarios
if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['admin', 'empleado'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$rolUsuario = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Crear Nuevo Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h2>Crear Nuevo Usuario</h2>
        <form method="POST" action="crear_usuario.php">
            <!-- Campos comunes -->
            <div class="mb-3">
                <label for="id" class="form-label">DNI/NIE</label>
                <input type="text" name="id" class="form-control" required maxlength="9">
            </div>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control">
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>
            <div class="mb-3">
                <label for="localidad" class="form-label">Localidad</label>
                <input type="text" name="localidad" class="form-control">
            </div>
            <div class="mb-3">
                <label for="provincia" class="form-label">Provincia</label>
                <input type="text" name="provincia" class="form-control">
            </div>

            <!-- Campo de Rol (solo visible para admin) -->
            <?php if ($rolUsuario === 'admin'): ?>
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            <?php else: ?>
                <!-- Empleados solo pueden crear clientes -->
                <input type="hidden" name="rol" value="cliente">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Crear Usuario</button>
            <a href="gestion_usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>

</html>