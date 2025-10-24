<?php
include('conexion_bd.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}

$id_blog = base64_decode($_GET['id_blog']);

// Obtener imágenes para eliminarlas
$query = "SELECT imagen1, imagen2, imagen3 FROM blogs WHERE id_blog = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $id_blog, PDO::PARAM_INT);
$stmt->execute();
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

// Eliminar imágenes si existen
if ($blog['imagen1'] && file_exists($blog['imagen1'])) unlink($blog['imagen1']);
if ($blog['imagen2'] && file_exists($blog['imagen2'])) unlink($blog['imagen2']);
if ($blog['imagen3'] && file_exists($blog['imagen3'])) unlink($blog['imagen3']);

// Eliminar blog
$query = "UPDATE blogs SET activo = 0 WHERE id_blog = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $id_blog, PDO::PARAM_INT);



if ($stmt->execute()) {
    header("Location: ../admin/blogsR.php");
} else {
    header("Location: ../admin/blogsR.php?error=delete_failed");
}
?>