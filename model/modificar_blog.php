<?php
include('conexion_bd.php');
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}

$id_blog = base64_decode($_GET['id_blog']);

// Procesar imágenes
function subirImagen($file, $nombreCampo, $conn, $imagenActual) {
    if (isset($file[$nombreCampo])) {
        if ($file[$nombreCampo]['error'] === UPLOAD_ERR_OK) {
            $nombreTemporal = $file[$nombreCampo]['tmp_name'];
            $nombreArchivo = uniqid() . '_' . basename($file[$nombreCampo]['name']);
            $rutaDestino = '../imagenes_blogs/' . $nombreArchivo;
            
            if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
                // Eliminar imagen anterior si existe
                if ($imagenActual && file_exists($imagenActual)) {
                    unlink($imagenActual);
                }
                return $rutaDestino;
            }
        }
    }
    return $imagenActual;
}

// Obtener blog actual para las imágenes
$query = "SELECT imagen1, imagen2, imagen3 FROM blogs WHERE id_blog = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $id_blog, PDO::PARAM_INT);
$stmt->execute();
$blog_actual = $stmt->fetch(PDO::FETCH_ASSOC);

$imagen1 = subirImagen($_FILES, 'imagen1', $conn, $blog_actual['imagen1']);
$imagen2 = subirImagen($_FILES, 'imagen2', $conn, $blog_actual['imagen2']);
$imagen3 = subirImagen($_FILES, 'imagen3', $conn, $blog_actual['imagen3']);

// Actualizar blog
$query = "UPDATE blogs SET 
          titulo = ?, 
          descripcion = ?, 
          imagen1 = ?, 
          imagen2 = ?, 
          imagen3 = ? 
          WHERE id_blog = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $_POST['titulo'], PDO::PARAM_STR);
$stmt->bindParam(2, $_POST['descripcion'], PDO::PARAM_STR);
$stmt->bindParam(3, $imagen1, PDO::PARAM_STR);
$stmt->bindParam(4, $imagen2, PDO::PARAM_STR);
$stmt->bindParam(5, $imagen3, PDO::PARAM_STR);
$stmt->bindParam(6, $id_blog, PDO::PARAM_INT);

if ($stmt->execute()) {
    header("Location: ../admin/blogsR.php?success=blog_updated");
} else {
    header("Location: ../admin/blogU.php?id_blog=" . base64_encode($id_blog) . "&error=update_failed");
}
?>