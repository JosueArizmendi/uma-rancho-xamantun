<?php
session_start();
include('../model/conexion_bd.php');

try {
    
    // Verificar si el ID codificado en Base64 ha sido proporcionado
    if (!isset($_GET['id'])) {
        throw new Exception("No se ha proporcionado un ID de usuario");
    }

    $encoded_id = $_GET['id'];
    $decoded_id = base64_decode($encoded_id);

    // Validar el ID decodificado
    if (!is_numeric($decoded_id)) {
        throw new Exception("ID de usuario no válido");
    }

    // No permitir desactivar el propio usuario
    if ($decoded_id == $_SESSION['id_usuario']) {
        throw new Exception("No puedes desactivar tu propia cuenta");
    }

    // Preparar la consulta para desactivar el usuario
    $query = "UPDATE usuarios SET activo = 0 WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $decoded_id, PDO::PARAM_INT);

    // Ejecutar y verificar resultado
    if ($stmt->execute()) {
        $affectedRows = $stmt->rowCount();
        
        if ($affectedRows > 0) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => 'Usuario desactivado',
                'message' => 'El usuario ha sido eliminado correctamente'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'warning',
                'title' => 'Advertencia',
                'message' => 'No se encontró el usuario especificado'
            ];
        }
    } else {
        throw new Exception("Error al ejecutar la consulta");
    }

} catch (PDOException $e) {
    error_log("Error en eliminar_usuario: " . $e->getMessage());
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error de base de datos',
        'message' => 'Ocurrió un error al procesar la solicitud'
    ];
} catch (Exception $e) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => $e->getMessage()
    ];
}

// Redirigir siempre al listado de usuarios
header("Location: usuarios.php");
exit;
?>