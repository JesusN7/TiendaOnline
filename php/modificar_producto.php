<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

// Verificar permisos
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'empleado' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

$errores = [];
$nombreArchivo = null; // Inicializamos la variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['codigo'])) {
    $codigo = htmlspecialchars($_GET['codigo']);
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $categoria_id = isset($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : 0;
    $precio = isset($_POST['precio']) ? (float) $_POST['precio'] : 0.0;
    $descuento = isset($_POST['descuento']) ? (float) $_POST['descuento'] : 0.0;
    $stock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
    $activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;
    $imagen = $_FILES['imagen'] ?? null;

    // Validaciones
    if (!validarCodigo($codigo)) {
        $errores[] = "El código debe tener 3 letras seguidas de 1 a 3 números.";
    }
    if (!validarDescripcion($descripcion)) {
        $errores[] = "La descripción debe tener entre 1 y 500 caracteres.";
    }
    if (!validarPrecio($precio)) {
        $errores[] = "El precio debe ser mayor que 0.";
    }
    if (!validarDescuento($descuento)) {
        $errores[] = "El descuento debe ser un valor entre 0 y 100.";
    }
    if (!validarStock($stock)) {
        $errores[] = "El stock debe ser un número entero positivo.";
    }

    try {
        $conexion = conectar();

        // Verificar si el código ya existe en otro producto
        $sql = "SELECT COUNT(*) FROM productos WHERE codigo = :codigo AND codigo != :codigo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $errores[] = "El código ya existe en otro producto.";
        }

        // Procesar la imagen si se ha subido una nueva
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/TiendaOnline/imagenes/';
            $nombreArchivo = basename($imagen['name']);
            $archivoDestino = $directorioDestino . $nombreArchivo;

            if (validarImagen($nombreArchivo)) {
                if (!move_uploaded_file($imagen['tmp_name'], $archivoDestino)) {
                    $errores[] = "Error al mover la imagen.";
                }
            } else {
                $errores[] = "Formato de imagen no válido.";
            }
        }

        if (empty($errores)) {
            // Construcción de consulta SQL
            if ($nombreArchivo === null) {
                $sql = "UPDATE productos 
                        SET nombre = :nombre, descripcion = :descripcion, 
                            categoria_id = :categoria_id, precio = :precio, 
                            descuento = :descuento, stock = :stock, activo = :activo 
                        WHERE codigo = :codigo";
                $stmt = $conexion->prepare($sql);
            } else {
                $sql = "UPDATE productos 
                        SET nombre = :nombre, descripcion = :descripcion, 
                            categoria_id = :categoria_id, precio = :precio, 
                            descuento = :descuento, stock = :stock, 
                            imagen = :imagen, activo = :activo 
                        WHERE codigo = :codigo";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':imagen', $nombreArchivo, PDO::PARAM_STR);
            }

            // Bind de parámetros
            $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
            $stmt->bindParam(':descuento', $descuento, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);

            $stmt->execute();

            $_SESSION['mensaje_exito'] = "Producto modificado correctamente.";
            header("Location: gestion_productos.php");
            exit();
        }
    } catch (PDOException $e) {
        $errores[] = "Error: " . $e->getMessage();
    }

    // Redirigir si hay errores
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_form'] = $_POST;
        header("Location: form_modificar_producto.php?codigo=" . urlencode($codigo));
        exit();
    }
} else {
    header("Location: gestion_productos.php");
    exit();
}
