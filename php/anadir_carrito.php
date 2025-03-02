<?php
session_start();
require_once 'conexion_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_producto'])) {
    $codigo = $_POST['codigo_producto'];
    $cantidad = max(1, intval($_POST['cantidad'])); // Asegurar que la cantidad sea al menos 1

    try {
        $conexion = conectar();
        $stmt = $conexion->prepare("SELECT * FROM productos WHERE codigo = ? AND activo = 1");
        $stmt->execute([$codigo]);
        $producto = $stmt->fetch();

        if ($producto && $producto['stock'] >= $cantidad) {
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }

            // Buscar si ya existe el producto en el carrito
            $encontrado = false;
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['codigo'] === $codigo) {
                    $item['cantidad'] += $cantidad;
                    $encontrado = true;
                    break;
                }
            }

            if (!$encontrado) {
                $_SESSION['carrito'][] = [
                    'codigo' => $producto['codigo'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad,
                    'imagen' => $producto['imagen']
                ];
            }

            $_SESSION['mensaje_exito'] = "Producto añadido al carrito";
        } else {
            $_SESSION['mensaje_error'] = "No hay suficiente stock para este producto";
        }
    } catch (PDOException $e) {
        error_log("Error al añadir al carrito: " . $e->getMessage());
        $_SESSION['mensaje_error'] = "Error al añadir el producto";
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
