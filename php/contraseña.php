<?php

$password = 'password';

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña hasheada: " . $hashedPassword;
?>
