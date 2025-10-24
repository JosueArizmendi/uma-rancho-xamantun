<?php
session_start();
include('conexion_bd.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => 'Debes iniciar sesión para realizar esta acción'
    ];
    header("Location: ../login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Verificar si se ha enviado un archivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagenPerfil'])) {
    $archivo = $_FILES['imagenPerfil'];
    
    // Verificar errores de subida
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por el servidor',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido',
            UPLOAD_ERR_PARTIAL => 'El archivo fue subido parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en el disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida del archivo'
        ];
        
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => $errorMessages[$archivo['error']] ?? 'Error desconocido al subir el archivo'
        ];
        header("Location: ../admin/perfil.php");
        exit;
    }

    // Validar tipo y tamaño del archivo
    $tamanioMaximo = 2 * 1024 * 1024; // 2MB
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    
    if ($archivo['size'] > $tamanioMaximo) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => 'El archivo excede el tamaño máximo de 2MB'
        ];
        header("Location: ../admin/perfil.php");
        exit;
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($archivo['tmp_name']);
    
    if (!in_array($mime, $tiposPermitidos)) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => 'Formato de archivo no permitido. Solo se aceptan JPG, PNG o GIF'
        ];
        header("Location: ../admin/perfil.php");
        exit;
    }

    // Generar nombre único para el archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreUnico = uniqid('profile_', true) . '.' . $extension;
    $directorio = '../imagenes_perfil/';
    $rutaDestino = $directorio . $nombreUnico;

    // Mover el archivo subido
    if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        // Obtener la imagen anterior para eliminarla después
        $query = "SELECT imagen_perfil FROM usuarios WHERE nom_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario]);
        $imagenAnterior = $stmt->fetchColumn();
        
        // Actualizar la base de datos con la nueva imagen
        $query = "UPDATE usuarios SET imagen_perfil = ? WHERE nom_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$rutaDestino, $usuario]);
        
        // Eliminar la imagen anterior si existe y no es la imagen por defecto
        if ($imagenAnterior && $imagenAnterior !== '../imagenes_perfil/vacio.jpg' && file_exists($imagenAnterior)) {
            unlink($imagenAnterior);
        }
        
        $_SESSION['notification'] = [
            'type' => 'success',
            'title' => 'Éxito',
            'message' => 'Imagen de perfil actualizada correctamente'
        ];
    } else {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => 'No se pudo guardar la imagen en el servidor'
        ];
    }
} else {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => 'No se recibió ninguna imagen válida'
    ];
}

header("Location: ../admin/perfil.php");
exit;
?>
