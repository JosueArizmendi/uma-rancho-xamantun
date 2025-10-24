<?php
include('conexion_bd.php');

function codificar($id) {
    return base64_encode($id);
}

// Para la web pública: Solo obtener blogs activos (activo = 1)
$query = "SELECT b.*, u.nom_usuario as nombre_autor 
          FROM blogs b 
          JOIN usuarios u ON b.autor_id = u.id 
          WHERE b.activo = 1
          ORDER BY b.fecha_publicacion DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>