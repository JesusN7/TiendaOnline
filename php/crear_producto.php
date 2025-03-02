<?php
session_start();
require_once 'conexion_db.php';
require_once 'funciones.php';

$errores = [];

// Verificar si el usuario está autenticado y tiene los permisos adecuados
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] !== 'empleado' && $_SESSION['rol'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y validar los datos del formulario
    $codigo = htmlspecialchars(trim($_POST['codigo']));
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $descripcion = htmlspecialchars(trim($_POST['descripcion']));
    $categoria_id = isset($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : 0;
    $precio = isset($_POST['precio']) ? (float) $_POST['precio'] : 0.0;
    $descuento = isset($_POST['descuento']) ? (float) $_POST['descuento'] : 0.0;
    $stock = isset($_POST['stock']) ? (int) $_POST['stock'] : 0;
    $activo = isset($_POST['activo']) ? (int) $_POST['activo'] : 1;
    $imagen = $_FILES['imagen'];

    // Validar campos
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

        // Comprobar si el código ya existe
        $sql = "SELECT COUNT(*) FROM productos WHERE codigo = :codigo";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        $existe = $stmt->fetchColumn();

        if ($existe > 0) {
            $errores[] = "El código ya existe. Por favor, elige un código diferente.";
        }

        // Procesar la imagen
        if ($imagen['error'] === UPLOAD_ERR_OK) {
            $directorioDestino = $_SERVER['DOCUMENT_ROOT'] . '/TiendaOnline/imagenes/';
            $nombreArchivo = basename($imagen['name']);
            $archivoDestino = $directorioDestino . $nombreArchivo;

            // Validar la imagen
            if (validarImagen($nombreArchivo)) {
                if (move_uploaded_file($imagen['tmp_name'], $archivoDestino)) {
                    // Insertar el producto en la base de datos
                    $sql = "INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, descuento, stock, imagen, activo)
                            VALUES (:codigo, :nombre, :descripcion, :categoria_id, :precio, :descuento, :stock, :imagen, :activo)";
                    $stmt = $conexion->prepare($sql);
                    $stmt->bindParam(':codigo', $codigo, PDO::PARAM_STR);
                    $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                    $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                    $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
                    $stmt->bindParam(':precio', $precio, PDO::PARAM_STR);
                    $stmt->bindParam(':descuento', $descuento, PDO::PARAM_STR);
                    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
                    $stmt->bindParam(':imagen', $nombreArchivo, PDO::PARAM_STR);
                    $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);

                    $stmt->execute();

                    // Redirigir al panel de gestión de productos
                    $_SESSION['mensaje_exito'] = "El producto ha sido creado correctamente.";
                    header("Location: gestion_productos.php");
                    exit();
                } else {
                    $errores[] = "Error al mover la imagen.";
                }
            } else {
                $errores[] = "Solo se permiten imágenes de tipo JPG, JPEG, PNG.";
            }
        } else {
            $errores[] = "Error en la carga de la imagen.";
        }
    } catch (PDOException $e) {
        $errores[] = "Error al crear el producto: " . $e->getMessage();
    }

    // Guardar errores en la sesión
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_form'] = $_POST;
        header("Location: form_crear_producto.php");
        exit();
    }
}
