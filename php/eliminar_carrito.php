<?php
session_start();

if (isset($_GET['index'])) {
    $index = intval($_GET['index']);
    
    if (isset($_SESSION['carrito'][$index])) {
        // Eliminar el producto del carrito
        unset($_SESSION['carrito'][$index]);
        
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        
        $_SESSION['exito'] = "Producto eliminado correctamente";
    } else {
        $_SESSION['error'] = "El producto no existe en el carrito";
    }
}

header('Location: carrito.php');
exit;