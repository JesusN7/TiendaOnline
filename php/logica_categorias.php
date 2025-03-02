<?php
require_once 'conexion_db.php';

$conexion = conectar();

// Obtener todas las categorías activas con sus subcategorías
function obtenerCategorias($conexion)
{
    // Obtener categorías principales 
    $query = "SELECT id, nombre, categoriaPadre, activo FROM categorias WHERE categoriaPadre IS NULL";
    $stmt = $conexion->prepare($query);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener las subcategorías de cada categoría
    foreach ($categorias as &$categoria) {
        $querySubcategorias = "SELECT id, nombre, activo FROM categorias WHERE categoriaPadre = :categoriaPadre";
        $stmtSub = $conexion->prepare($querySubcategorias);
        $stmtSub->bindParam(':categoriaPadre', $categoria['id']);
        $stmtSub->execute();
        $categoria['subcategorias'] = $stmtSub->fetchAll(PDO::FETCH_ASSOC);
    }

    return $categorias;
}

// Obtener una categoría por su ID
function obtenerCategoriaPorId($conexion, $id)
{
    $query = "SELECT * FROM categorias WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Crear una nueva categoría
function crearCategoria($conexion, $nombre, $categoriaPadre)
{
    try {
        $query = "INSERT INTO categorias (nombre, categoriaPadre, activo) VALUES (:nombre, :categoriaPadre, 1)";
        $stmt = $conexion->prepare($query);

        // Vincula los parámetros
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':categoriaPadre', $categoriaPadre, PDO::PARAM_INT);

        // Ejecuta la consulta y verifica
        if ($stmt->execute()) {
            return true;
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Error al crear categoría: ' . $errorInfo[2]);
        }
    } catch (Exception $e) {
        echo "Excepción: " . $e->getMessage();
        return false;
    }
}

// Modificar una categoría existente
function modificarCategoria($conexion, $id, $nombre, $categoriaPadre)
{
    $query = "UPDATE categorias SET nombre = :nombre, categoriaPadre = :categoriaPadre WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindParam(':categoriaPadre', $categoriaPadre, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

// Activar o desactivar una categoría
function cambiarEstadoCategoria($conexion, $id, $nuevoEstado)
{
    // Primero actualizamos la categoría principal
    $query = "UPDATE categorias SET activo = :nuevoEstado WHERE id = :id";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':nuevoEstado', $nuevoEstado, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Si la categoría es desactivada, también desactivamos sus subcategorías
    if ($nuevoEstado == 0) {
        // Desactivar subcategorías asociadas
        $querySubcategorias = "UPDATE categorias SET activo = 0 WHERE categoriaPadre = :id";
        $stmtSubcategorias = $conexion->prepare($querySubcategorias);
        $stmtSubcategorias->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtSubcategorias->execute();
    }

    return $stmt->rowCount() > 0;
}
