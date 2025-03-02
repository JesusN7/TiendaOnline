<?php
require_once 'conexion_db.php';
require_once 'logica_categorias.php';

$conexion = conectar();

// Verificar si el parámetro 'id' es válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $categoria = obtenerCategoriaPorId($conexion, $id);  // Obtener la categoría

    if ($categoria) {
        // Determinar el nuevo estado: si está activo, lo desactivamos, y viceversa
        $nuevoEstado = $categoria['activo'] ? 0 : 1;

        // Llamar a la función para cambiar el estado de la categoría y las subcategorías
        if (cambiarEstadoCategoria($conexion, $id, $nuevoEstado)) {
            header("Location: gestion_categorias.php?mensaje=estado_actualizado");
            exit;
        } else {
            header("Location: gestion_categorias.php?error=error_actualizar");
            exit;
        }
    }
}

header("Location: gestion_categorias.php?error=categoria_no_encontrada");
exit;
