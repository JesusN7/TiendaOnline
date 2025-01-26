<?php
function conectar(){
    try {
        // Actualiza el nombre de la base de datos
        $dsn = 'mysql:host=localhost;dbname=jesus_db;charset=utf8mb4';
        
        // El usuario de XAMPP por defecto es 'root', sin contraseña
        $usuario = 'root';
        $password = ''; // Contraseña vacía por defecto en XAMPP

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        return new PDO($dsn, $usuario, $password, $opciones);
    } catch (PDOException $e) {
        die ("Error al conectar con la base de datos: " . $e->getMessage());
    }
}

