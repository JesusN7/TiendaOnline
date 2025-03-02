<?php
session_start();
require_once 'conexion_db.php';

// Verificar si el carrito está vacío
if (empty($_SESSION['carrito'])) {
    $_SESSION['error'] = "El carrito está vacío";
    header('Location: carrito.php');
    exit;
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['id'])) {
    $_SESSION['error'] = "Debes iniciar sesión para finalizar la compra";
    header('Location: index.php');
    exit;
}

// Procesar el pago si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'])) {
    // Tarjetas de prueba y sus comportamientos
    $tarjetas = [
        '4242424242424242' => 'success', // Pago exitoso
        '4000000000000002' => 'card_declined', // Tarjeta rechazada
        '4000000000000127' => 'incorrect_cvc', // CVC incorrecto
        '4000000000000069' => 'expired_card', // Tarjeta expirada
    ];

    // Obtener datos del formulario
    $card_number = str_replace(' ', '', $_POST['card_number']); 
    $expiry_date = $_POST['expiry_date'];
    $cvc = $_POST['cvc'];

    // Validar tarjeta
    if (array_key_exists($card_number, $tarjetas)) {
        $resultado = $tarjetas[$card_number];

        switch ($resultado) {
            case 'success':
                try {
                    $conexion = conectar();
                    $conexion->beginTransaction();

                    // Crear el pedido
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $item) {
                        $total += $item['precio'] * $item['cantidad'];
                    }

                    $stmt = $conexion->prepare("INSERT INTO pedidos 
                                              (usuario_id, total, estado, fecha_pedido) 
                                              VALUES (?, ?, 'pendiente', NOW())");
                    $stmt->execute([$_SESSION['id'], $total]);
                    $num_pedido = $conexion->lastInsertId();

                    // Insertar líneas de pedido
                    $num_linea = 1;
                    foreach ($_SESSION['carrito'] as $item) {
                        $stmt = $conexion->prepare("INSERT INTO lineapedido 
                                                  (numPedido, numLinea, codigo_producto, cantidad, precio) 
                                                  VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $num_pedido,
                            $num_linea++,
                            $item['codigo'],
                            $item['cantidad'],
                            $item['precio']
                        ]);

                        // Actualizar stock
                        $stmt = $conexion->prepare("UPDATE productos 
                                                  SET stock = stock - ? 
                                                  WHERE codigo = ?");
                        $stmt->execute([$item['cantidad'], $item['codigo']]);
                    }

                    $conexion->commit();

                    // Vaciar carrito y redirigir
                    unset($_SESSION['carrito']);
                    $_SESSION['exito'] = "¡Compra realizada con éxito! Nº Pedido: $num_pedido";
                    header('Location: gracias.php');
                    exit;
                } catch (PDOException $e) {
                    $conexion->rollBack();
                    error_log("Error en checkout: " . $e->getMessage());
                    $_SESSION['error'] = "Error al procesar el pedido: " . $e->getMessage();
                    // No redirigimos, mostramos el error en la misma página
                }
                break;

            case 'card_declined':
                $_SESSION['error'] = "Tarjeta rechazada. Por favor, usa otra tarjeta.";
                break;
            case 'incorrect_cvc':
                $_SESSION['error'] = "CVC incorrecto. Verifica los datos de la tarjeta.";
                break;
            case 'expired_card':
                $_SESSION['error'] = "Tarjeta expirada. Usa una tarjeta válida.";
                break;
            default:
                $_SESSION['error'] = "Error desconocido. Intenta nuevamente.";
                break;
        }
    } else {
        $_SESSION['error'] = "Error: Tarjeta no válida.";
    }

}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Checkout</h2>

        <!-- Mostrar mensajes de éxito o error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Formulario de pago -->
        <form action="checkout.php" method="POST">
            <div class="mb-3">
                <label for="card_number" class="form-label">Número de Tarjeta</label>
                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="4242 4242 4242 4242" required>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Fecha de Expiración</label>
                <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="12/34" required>
            </div>
            <div class="mb-3">
                <label for="cvc" class="form-label">CVC</label>
                <input type="text" class="form-control" id="cvc" name="cvc" placeholder="123" required>
            </div>
            <button type="submit" class="btn btn-primary">Pagar</button>
            <a href="carrito.php" class="btn btn-secondary">Volver al Carrito</a>
        </form>
    </div>
</body>

</html>