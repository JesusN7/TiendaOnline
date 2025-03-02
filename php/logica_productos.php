<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'conexion_db.php';
require_once 'funciones.php';

$conexion = conectar();

$esGestionInterna = strpos($_SERVER['SCRIPT_NAME'], 'gestion_productos.php') !== false;

// Obtener filtros
$busqueda = isset($_GET['buscar']) ? htmlspecialchars(trim($_GET['buscar'])) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : '';
$categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;

// Validar ordenamiento
$ordenesPermitidos = ['precio_asc' => 'precio ASC', 'precio_desc' => 'precio DESC'];
$ordenSQL = isset($ordenesPermitidos[$orden]) ? " ORDER BY " . $ordenesPermitidos[$orden] : "";

// Paginación
$paginaActual = isset($_GET['pagina']) ? filter_var($_GET['pagina'], FILTER_VALIDATE_INT) : 1;
if (!$paginaActual || $paginaActual < 1) {
    $paginaActual = 1;
}
$porPagina = 4;

// Construir consulta base
$sqlPrincipal = "SELECT * FROM productos";
$condicionesWhere = []; // Array para acumular condiciones
$parametros = [];

// Si NO estamos en gestión interna, filtrar por activo = 1
if (!$esGestionInterna) {
    $condicionesWhere[] = "activo = 1";
}

// Filtro por categoría y subcategorías
if ($categoria_id) {
    $stmt = $conexion->prepare("SELECT id FROM categorias WHERE categoriaPadre = :categoria_id");
    $stmt->execute([':categoria_id' => $categoria_id]);
    $subcategorias = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $idsCategorias = array_merge([$categoria_id], $subcategorias);

    $marcadores_posicion = [];
    foreach ($idsCategorias as $index => $id) {
        $marcador = ":cat$index";
        $marcadores_posicion[] = $marcador;
        $parametros[$marcador] = $id;
    }

    $condicionesWhere[] = "categoria_id IN (" . implode(',', $marcadores_posicion) . ")";
}

// Filtro por búsqueda
if (!empty($busqueda)) {
    $busqueda = strtolower(trim($busqueda));
    $condicionesWhere[] = "(LOWER(nombre) LIKE :busqueda OR LOWER(descripcion) LIKE :busqueda)";
    $parametros[':busqueda'] = "%$busqueda%";
}

// Combinar condiciones con WHERE si hay al menos una
if (!empty($condicionesWhere)) {
    $sqlPrincipal .= " WHERE " . implode(' AND ', $condicionesWhere);
}

// Aplicar paginación y ordenamiento
$paginacion = paginacion($conexion, $paginaActual, $porPagina, $sqlPrincipal . $ordenSQL, $parametros);
$productos = $paginacion['resultados'];
$totalPaginas = $paginacion['totalPaginas'];
