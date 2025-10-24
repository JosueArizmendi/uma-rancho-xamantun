<?php
include 'conexion_bd.php';

function decodificar($hash) {
    return base64_decode($hash);
}

// Decodificar el ID de la especie
$id = isset($_REQUEST['id_especie']) ? decodificar($_REQUEST['id_especie']) : null;
$estado = 0; // Estado inactivo

// Iniciar la sesión para manejar las notificaciones
session_start();

try {
    // Consulta SQL para cambiar el estado a inactivo (en lugar de eliminar)
    $sql = "UPDATE especies_flora SET activo=:estado WHERE id_especie =:id";
    $stmt = $conn->prepare($sql);

    // Asociar parámetros
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id);
    
    // Ejecutar la consulta
    $stmt->execute();

    // Crear una notificación de éxito
    $_SESSION['notification'] = [
        'type' => 'success',
        'title' => 'Éxito',
        'message' => 'La especie de flora se ha eliminado correctamente.'
    ];

    // Redirigir a la página de especies de flora
    header('Location: ../admin/especie_floraR.php');
    exit;
} catch (PDOException $e) {
    // En caso de error, crear una notificación de error
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => 'Ha ocurrido un error al intentar inactivar la especie de flora. Intenta nuevamente.'
    ];

    // Redirigir a la página de especies de flora
    header('Location: ../admin/especie_floraR.php');
    exit;
}
?>
