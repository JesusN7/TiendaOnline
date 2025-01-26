<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

$conexion = conectar();

// Recuperar parámetros de búsqueda y orden
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : '';

// Definir la página actual y el número de resultados por página.
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$porPagina = 4;  // Número de productos por página

// Consulta base.
$sqlPrincipal = "SELECT * FROM productos WHERE activo = 1";

// Condiciones y parámetros de búsqueda.
$condiciones = '';
$parámetros = [];

if (!empty($busqueda)) {
    // Si hay búsqueda, añadimos las condiciones
    $condiciones .= " AND (nombre LIKE :busqueda OR descripcion LIKE :busqueda)"; // Cambiado de $condiciones = a $condiciones .=
    $parámetros[':busqueda'] = '%' . $busqueda . '%';
}

// Orden de los productos.
$ordenSQL = '';
if ($orden === 'precio_asc') {
    $ordenSQL = ' ORDER BY precio ASC';
} elseif ($orden === 'precio_desc') {
    $ordenSQL = ' ORDER BY precio DESC';
}

// Comprobar si hay condiciones antes de concatenar
$sqlPrincipalConCondiciones = $sqlPrincipal . ($condiciones ? $condiciones : '');

// Llamamos a la función de paginación.
$paginacion = paginacion($conexion, $paginaActual, $porPagina, $sqlPrincipalConCondiciones . $ordenSQL, $parámetros);

// Obtenemos los productos y el número total de páginas
$productos = $paginacion['resultados'];
$totalPaginas = $paginacion['totalPaginas'];
$paginaActual = $paginacion['paginaActual'];
