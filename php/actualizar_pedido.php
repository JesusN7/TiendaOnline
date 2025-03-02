<?php
session_start();
require_once 'conexion_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numPedido'], $_POST['estado'])) {
    try {
        $conexion = conectar();
        $numPedido = $_POST['numPedido'];
        $nuevoEstado = $_POST['estado'];
        $rol = $_SESSION['rol'];

        // Validar permisos
        if ($rol === 'cliente') {
            // Clientes solo pueden cancelar sus propios pedidos
            if ($nuevoEstado !== 'cancelado') {
                throw new Exception("Acción no permitida");
            }

            $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? 
                                      WHERE numPedido = ? AND usuario_id = ?");
            $stmt->execute([$nuevoEstado, $numPedido, $_SESSION['id']]);
        } else { // empleado o admin
            $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE numPedido = ?");
            $stmt->execute([$nuevoEstado, $numPedido]);
        }

        // Si se cancela, restaurar stock
        if ($nuevoEstado === 'cancelado') {
            $stmt = $conexion->prepare("SELECT codigo_producto, cantidad 
                                      FROM lineapedido 
                                      WHERE numPedido = ?");
            $stmt->execute([$numPedido]);
            $lineas = $stmt->fetchAll();

            foreach ($lineas as $linea) {
                $conexion->prepare("UPDATE productos 
                                   SET stock = stock + ? 
                                   WHERE codigo = ?")
                    ->execute([$linea['cantidad'], $linea['codigo_producto']]);
            }
        }

        $_SESSION['exito'] = "Estado actualizado correctamente."; 
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage(); 
    }
}

// Redirigir a la vista de gestión de pedidos
header('Location: gestion_pedidos.php');
exit;
