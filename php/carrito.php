<?php
session_start();
require_once 'conexion_db.php';
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <?php if (!empty($_SESSION['carrito'])): ?>
            <form action="actualizar_carrito.php" method="POST">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['carrito'] as $index => $item):
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><img src="/TiendaOnline/imagenes/<?= $item['imagen'] ?>" width="50"></td>
                                <td><?= htmlspecialchars($item['nombre']) ?></td>
                                <td><?= number_format($item['precio'], 2) ?>€</td>
                                <td>
                                    <input type="number" name="cantidades[<?= $index ?>]"
                                        value="<?= $item['cantidad'] ?>"
                                        min="1"
                                        class="form-control"
                                        style="width: 80px;">
                                </td>
                                <td><?= number_format($subtotal, 2) ?>€</td>
                                <td>
                                    <a href="eliminar_carrito.php?index=<?= $index ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Seguro que quieres eliminar este producto?')">
                                        Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Total: <?= number_format($total, 2) ?>€</h4>
                    <div>
                        <button type="submit" class="btn btn-warning me-2">Actualizar Cantidades</button>
                        <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">El carrito está vacío</div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-primary">Seguir Comprando</a>
    </div>
</body>

</html>