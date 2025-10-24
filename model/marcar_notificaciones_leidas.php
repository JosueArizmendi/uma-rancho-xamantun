<?php
include 'conexion_bd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usuario = $_POST['id_usuario'] ?? null;
    
    if ($id_usuario) {
        try {
            $stmt = $conn->prepare("UPDATE notificaciones SET leida = 1 WHERE id_usuario = ? AND leida = 0");
            $stmt->execute([$id_usuario]);
            
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID de usuario no proporcionado']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
}
?>