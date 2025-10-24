<?php
// Incluir el archivo de conexión
include('conexion_bd.php');

// Obtener los usuarios
$query = "SELECT id, nombre, primer_apellido, segundo_apellido, nom_usuario, contraseña, rol FROM usuarios";
$stmt = $conn->prepare($query);
$stmt->execute();

// Obtener todos los resultados
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica si hay usuarios en la base de datos
if (empty($usuarios)) {
    echo "No se encontraron usuarios.";
}
?>

