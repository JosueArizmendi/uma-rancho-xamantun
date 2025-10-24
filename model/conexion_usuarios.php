<?php
$servername = "192.168.1.18"; // IP de la PC2
$dbname = "usuarios_db";      // Nombre de la BD en PC2
$username = "root";
$password = "";

try {
    $conn_usuarios = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn_usuarios->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de conexión con la tabla usuarios: " . $e->getMessage();
    die(); // detiene el sistema si no se puede conectar
}
?>