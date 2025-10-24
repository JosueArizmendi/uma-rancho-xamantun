<?php
include('conexion_bd.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}

// Obtener ID del usuario logueado
$usuario = $_SESSION['usuario'];
$query = "SELECT id FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $usuario, PDO::PARAM_STR);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
$autor_id = $user_data['id'];

// Procesar imágenes
function subirImagen($file, $nombreCampo, $conn) {
    if ($file[$nombreCampo]['error'] === UPLOAD_ERR_OK) {
        $nombreTemporal = $file[$nombreCampo]['tmp_name'];
        $nombreArchivo = uniqid() . '_' . basename($file[$nombreCampo]['name']);
        $rutaDestino = '../imagenes_blogs/' . $nombreArchivo;
        
        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            return $rutaDestino;
        }
    }
    return null;
}

$imagen1 = subirImagen($_FILES, 'imagen1', $conn);
$imagen2 = subirImagen($_FILES, 'imagen2', $conn);
$imagen3 = subirImagen($_FILES, 'imagen3', $conn);

// Insertar blog
$query = "INSERT INTO blogs (titulo, autor_id, descripcion, imagen1, imagen2, imagen3) 
          VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $_POST['titulo'], PDO::PARAM_STR);
$stmt->bindParam(2, $autor_id, PDO::PARAM_INT);
$stmt->bindParam(3, $_POST['descripcion'], PDO::PARAM_STR);
$stmt->bindParam(4, $imagen1, PDO::PARAM_STR);
$stmt->bindParam(5, $imagen2, PDO::PARAM_STR);
$stmt->bindParam(6, $imagen3, PDO::PARAM_STR);

$stmt->execute();
    header("Location: ../admin/blogsR.php?success=blog_created");
?>