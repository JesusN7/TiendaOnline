<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cantidades'])) {
    foreach ($_POST['cantidades'] as $index => $cantidad) {
        if (isset($_SESSION['carrito'][$index])) {
            $_SESSION['carrito'][$index]['cantidad'] = max(1, intval($cantidad));
        }
    }
}

header('Location: carrito.php');
exit;