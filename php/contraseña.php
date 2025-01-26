<?php
$password = 'password'; // Reemplaza esto con la contraseña que deseas hashear

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña hasheada: " . $hashedPassword;
?>
