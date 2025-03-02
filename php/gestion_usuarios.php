<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Verificar permisos
$rol = $_SESSION['rol'] ?? '';
$idSesion = $_SESSION['id'] ?? '';
if (!in_array($rol, ['admin', 'empleado', 'cliente'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$conexion = conectar();

// Parámetros de búsqueda
$busqueda = $_GET['buscar'] ?? '';

// Parámetros de paginación
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 3; // Cantidad de usuarios por página

// Parámetros de ordenamiento
$orden = $_GET['orden'] ?? '';

// Parámetros de filtrado por rol
$rolFiltro = $_GET['rol'] ?? '';

// Parámetros de filtrado por estado (activo/inactivo)
$estadoFiltro = $_GET['estado'] ?? '';

// Definir los parámetros por defecto
$parametros = [];

// Consulta base para obtener todos los usuarios
$sqlPrincipal = "SELECT * FROM usuarios";

// Aplicar filtro de búsqueda si existe
if (!empty($busqueda)) {
    $sqlPrincipal .= " WHERE (nombre LIKE :busqueda OR id LIKE :busqueda)";
    $parametros[':busqueda'] = "%$busqueda%";
}

// Aplicar filtro por rol si existe
if (!empty($rolFiltro)) {
    $sqlPrincipal .= empty($parametros) ? " WHERE" : " AND";
    $sqlPrincipal .= " rol = :rol";
    $parametros[':rol'] = $rolFiltro;
}

// Aplicar filtro por estado si existe
if (!empty($estadoFiltro)) {
    $sqlPrincipal .= empty($parametros) ? " WHERE" : " AND";
    $sqlPrincipal .= ($estadoFiltro === 'activo') ? " activo = 1" : " activo = 0";
}

// Aplicar ordenamiento si se ha seleccionado
if ($orden === 'asc') {
    $sqlPrincipal .= " ORDER BY nombre ASC";
} elseif ($orden === 'desc') {
    $sqlPrincipal .= " ORDER BY nombre DESC";
}

// Función de paginación
$paginacion = paginacion($conexion, $paginaActual, $porPagina, $sqlPrincipal, $parametros);
$usuarios = $paginacion['resultados'];
$totalRegistros = $paginacion['totalRegistros'];
$totalPaginas = $paginacion['totalPaginas'];
?>

<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/TiendaOnline/css/gestion_usuarios.css">
    <title>Gestión de Usuarios</title>
</head>

<body>
    <div class="container mt-4">
        <h2>Gestión de Usuarios</h2>

        <!-- Botón para Crear Nuevo Usuario (Solo Admins y Empleados) -->
        <?php if (in_array($rol, ['admin', 'empleado'])): ?>
            <a href="form_crear_usuario.php" class="btn btn-primario mb-3">Crear Nuevo Usuario</a>
        <?php endif; ?>

        <!-- Formulario de Búsqueda, Ordenamiento y Filtrado -->
        <form action="gestion_usuarios.php" method="get" class="mb-3">
            <div class="input-group">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Buscar por nombre o DNI" value="<?= htmlspecialchars($busqueda) ?>">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>

            <div class="row g-3 mt-3">
                <!-- Ordenamiento -->
                <div class="col-md-4">
                    <label for="orden" class="form-label">Ordenar por nombre:</label>
                    <select name="orden" id="orden" class="form-select" onchange="this.form.submit()">
                        <option value="">Selecciona...</option>
                        <option value="asc" <?= (isset($_GET['orden']) && $_GET['orden'] == 'asc') ? 'selected' : ''; ?>>Ascendente</option>
                        <option value="desc" <?= (isset($_GET['orden']) && $_GET['orden'] == 'desc') ? 'selected' : ''; ?>>Descendente</option>
                    </select>
                </div>

                <!-- Filtro por Rol -->
                <div class="col-md-4">
                    <label for="rol" class="form-label">Filtrar por Rol:</label>
                    <select name="rol" id="rol" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los Roles</option>
                        <option value="admin" <?= (isset($_GET['rol']) && $_GET['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        <option value="empleado" <?= (isset($_GET['rol']) && $_GET['rol'] == 'empleado') ? 'selected' : ''; ?>>Empleado</option>
                        <option value="cliente" <?= (isset($_GET['rol']) && $_GET['rol'] == 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                    </select>
                </div>

                <!-- Filtro por Estado -->
                <div class="col-md-4">
                    <label for="estado" class="form-label">Filtrar por Estado:</label>
                    <select name="estado" id="estado" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <option value="activo" <?= (isset($_GET['estado']) && $_GET['estado'] == 'activo') ? 'selected' : ''; ?>>Activos</option>
                        <option value="inactivo" <?= (isset($_GET['estado']) && $_GET['estado'] == 'inactivo') ? 'selected' : ''; ?>>Inactivos</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Tabla de Usuarios -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Rol</th>
                    <th>Activo</th>
                    <th>Localidad</th>
                    <th>Provincia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr class="<?= $usuario['activo'] ? '' : 'table-secondary' ?>">
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                        <td><?= htmlspecialchars($usuario['direccion']) ?></td>
                        <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($usuario['rol'])) ?></td>
                        <td><?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?></td>
                        <td><?= htmlspecialchars($usuario['localidad']) ?></td>
                        <td><?= htmlspecialchars($usuario['provincia']) ?></td>
                        <td>
                            <div class="acciones">
                                <!-- Botón Modificar -->
                                <?php if (
                                    ($rol === 'admin') || // Admin puede modificar a todos
                                    ($rol === 'empleado' && $usuario['rol'] === 'cliente') || // Empleado puede modificar clientes
                                    ($_SESSION['id'] === $usuario['id']) // Cualquier usuario puede modificarse a sí mismo
                                ): ?>
                                    <a href="modificar_usuario.php?id=<?= $usuario['id'] ?>&context=gestion"
                                        class="btn btn-warning btn-sm btn-action">
                                        <i class="fas fa-edit"></i> Modificar
                                    </a>
                                <?php endif; ?>

                                <!-- Botones Activar/Desactivar -->
                                <?php if (
                                    ($rol === 'admin' && $_SESSION['id'] !== $usuario['id']) || // Admin puede activar/desactivar a todos excepto a sí mismo
                                    ($rol === 'empleado' && $usuario['rol'] === 'cliente' && $_SESSION['id'] !== $usuario['id']) // Empleado puede activar/desactivar clientes
                                ): ?>
                                    <?php if ($usuario['activo']): ?>
                                        <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>&accion=desactivar"
                                            class="btn btn-danger btn-sm btn-action">
                                            <i class="fas fa-ban"></i> Desactivar
                                        </a>
                                    <?php else: ?>
                                        <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>&accion=activar"
                                            class="btn btn-success btn-sm btn-action">
                                            <i class="fas fa-check"></i> Activar
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginación -->
        <nav>
            <ul class="pagination">
                <li class="page-item <?= ($paginaActual == 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $paginaActual - 1; ?>&buscar=<?= urlencode($busqueda); ?>&orden=<?= urlencode($orden); ?>&rol=<?= urlencode($rolFiltro); ?>">Anterior</a>
                </li>
                <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?= ($i == $paginaActual) ? 'active' : '' ?>">
                        <a class="page-link" href="?pagina=<?= $i; ?>&buscar=<?= urlencode($busqueda); ?>&orden=<?= urlencode($orden); ?>&rol=<?= urlencode($rolFiltro); ?>">
                            <?= $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($paginaActual == $totalPaginas) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?pagina=<?= $paginaActual + 1; ?>&buscar=<?= urlencode($busqueda); ?>&orden=<?= urlencode($orden); ?>&rol=<?= urlencode($rolFiltro); ?>">Siguiente</a>
                </li>
            </ul>
        </nav>

        <a href="panel.php" class="btn btn-secondary volv-btn">Volver</a>
    </div>
</body>

</html>