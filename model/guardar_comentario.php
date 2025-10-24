<?php
include 'conexion_bd.php';
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comentario'])) {
    $id_blog = $_POST['id_blog'];
    $comentario = $_POST['comentario'];
    
    // Obtener ID del usuario actual
    $usuario = $_SESSION['usuario'];
    $query = "SELECT id FROM usuarios WHERE nom_usuario = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $usuario, PDO::PARAM_STR);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        $id_usuario = $user_data['id'];
        
        // Insertar comentario
        $query = "INSERT INTO comentarios_blog (id_blog, id_usuario, comentario) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_blog, $id_usuario, $comentario]);
        
        header("Location: ../ver_blog.php?id=$id_blog&success=comment_added");
        exit;
    } else {
        header("Location: ../ver_blog.php?id=$id_blog&error=user_not_found");
        exit;
    }
} else {
    header("Location: ../ver_blog.php?error=invalid_request");
    exit;
}
?>