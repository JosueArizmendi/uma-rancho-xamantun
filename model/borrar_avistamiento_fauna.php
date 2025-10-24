<?php
session_start();
include 'conexion_bd.php';

$id = isset($_REQUEST['id_avistamiento']) ? base64_decode($_REQUEST['id_avistamiento']) : null;
$estado = 0;

if (!$id) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => 'ID de avistamiento no válido.'
    ];
    header('Location: ../admin/avistamiento_faunaR.php');
    exit;
}

try {
    $sql = "UPDATE avistamientos_animales SET activo = :estado WHERE id_avistamiento = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['notification'] = [
            'type' => 'success',
            'title' => '¡Éxito!',
            'message' => 'El avistamiento fue eliminado correctamente.'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => 'No se pudo eliminar el avistamiento.'
        ];
    }

    header('Location: ../admin/avistamiento_faunaR.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error de base de datos',
        'message' => 'Ocurrió un error al eliminar: ' . $e->getMessage()
    ];
    header('Location: ../admin/avistamiento_faunaR.php');
    exit;
}
?>
