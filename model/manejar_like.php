<?php
include '../model/conexion_bd.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

$id_comentario = isset($_GET['id_comentario']) ? (int)$_GET['id_comentario'] : 0;
if ($id_comentario <= 0) {
    header('HTTP/1.1 400 Bad Request');
    exit;
}

// Obtener ID de usuario
$usuario_actual = $_SESSION['usuario'];
$stmt_usuario = $conn->prepare("SELECT id FROM usuarios WHERE nom_usuario = ?");
$stmt_usuario->execute([$usuario_actual]);
$user_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
$id_usuario_actual = $user_data['id'];

// Verificar si el usuario ya dio like
$stmt_check = $conn->prepare("SELECT id_like FROM likes_comentarios WHERE id_comentario = ? AND id_usuario = ?");
$stmt_check->execute([$id_comentario, $id_usuario_actual]);

if ($stmt_check->rowCount() == 0) {
    // Insertar like
    $stmt_like = $conn->prepare("INSERT INTO likes_comentarios (id_comentario, id_usuario) VALUES (?, ?)");
    $stmt_like->execute([$id_comentario, $id_usuario_actual]);
    $action = 'like';
} else {
    // Quitar like
    $stmt_unlike = $conn->prepare("DELETE FROM likes_comentarios WHERE id_comentario = ? AND id_usuario = ?");
    $stmt_unlike->execute([$id_comentario, $id_usuario_actual]);
    $action = 'unlike';
}

// Obtener nuevo total de likes
$stmt_likes = $conn->prepare("SELECT COUNT(*) as total_likes FROM likes_comentarios WHERE id_comentario = ?");
$stmt_likes->execute([$id_comentario]);
$likes = $stmt_likes->fetch(PDO::FETCH_ASSOC);

// Notificación solo si es like (no cuando se quita)
if ($action === 'like') {
    // Obtener información del comentario para notificación
    $stmt_com = $conn->prepare("SELECT id_usuario, id_blog FROM comentarios_blog WHERE id_comentario = ?");
    $stmt_com->execute([$id_comentario]);
    $comentario = $stmt_com->fetch(PDO::FETCH_ASSOC);
    
    // Solo notificar si el comentario es de otro usuario
    if ($comentario && $comentario['id_usuario'] != $id_usuario_actual) {
        $stmt_notif = $conn->prepare("INSERT INTO notificaciones 
            (id_usuario, tipo, id_blog, id_comentario, mensaje) 
            VALUES (?, 'like', ?, ?, 'A alguien le gusta tu comentario')");
        $stmt_notif->execute([
            $comentario['id_usuario'],
            $comentario['id_blog'],
            $id_comentario
        ]);
    }
}

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'action' => $action,
    'total_likes' => $likes['total_likes']
]);