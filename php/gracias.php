<?php
session_start();
if (!isset($_SESSION['exito'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 text-center">
        <div class="alert alert-success">
            <h4><?= $_SESSION['exito'] ?></h4>
        </div>
        <a href="index.php" class="btn btn-primary">Volver a la tienda</a>
    </div>
</body>
</html>
<?php unset($_SESSION['exito']); ?>