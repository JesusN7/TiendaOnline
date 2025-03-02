<?php
session_start();
require_once 'conexion_db.php';

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$numPedido = $_GET['num'] ?? null;
$esCliente = isset($_GET['cliente']);

try {
    $conexion = conectar();

    // Verificar existencia del pedido y permisos
    $sql = "SELECT usuario_id, estado FROM pedidos WHERE numPedido = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$numPedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        throw new Exception("Pedido no encontrado");
    }

    // Validaciones para clientes
    if ($esCliente) {
        // Solo pueden eliminar sus propios pedidos
        if ($pedido['usuario_id'] != $_SESSION['id']) {
            throw new Exception("No tienes permiso para esta acción");
        }

        // Solo si el estado es "pendiente"
        if ($pedido['estado'] !== 'pendiente') {
            throw new Exception("Solo se pueden cancelar pedidos pendiente de envío.");
        }
    }

    // Eliminación lógica
    $sqlUpdate = "UPDATE pedidos SET activo = 0 WHERE numPedido = ?";
    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->execute([$numPedido]);

    $_SESSION['exito'] = $esCliente ?
        "Pedido cancelado correctamente" :
        "Pedido eliminado correctamente";
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: gestion_pedidos.php');
exit;
