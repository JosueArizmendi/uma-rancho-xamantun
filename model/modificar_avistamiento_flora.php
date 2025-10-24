<?php
session_start();
include 'conexion_bd.php';

// Obtener el ID del avistamiento (decodificado si está en base64)
$codigo = isset($_REQUEST['id_avistamiento']) ? 
         (is_numeric($_REQUEST['id_avistamiento']) ? $_REQUEST['id_avistamiento'] : base64_decode($_REQUEST['id_avistamiento'])) : 
         null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitización de datos
        $id_avistamiento = filter_input(INPUT_POST, "id_avistamiento", FILTER_SANITIZE_NUMBER_INT);
        $id_avistamiento_especie = filter_input(INPUT_POST, "input_especie", FILTER_SANITIZE_NUMBER_INT);
        $fecha = $_POST['input_fecha_avista'];
        $latitud = filter_input(INPUT_POST, "input_latitud", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $longitud = filter_input(INPUT_POST, "input_longitud", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $descripcion = filter_input(INPUT_POST, 'input_descripcion', FILTER_SANITIZE_SPECIAL_CHARS);

        // Directorio de subida
        $uploadDir = '../uploads/avistamientos_flora/';
        $relativePath = 'uploads/avistamientos_flora/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Imagen nueva (si se carga)
        if (isset($_FILES['input_imagen']) && $_FILES['input_imagen']['error'] === UPLOAD_ERR_OK) {
            // Obtener imagen actual
            $stmt = $conn->prepare("SELECT ruta_imagen FROM avistamientos_flora WHERE id_avistamiento = :id");
            $stmt->bindParam(':id', $codigo);
            $stmt->execute();
            $ruta_actual = $stmt->fetch();
            $imagen_anterior = $ruta_actual['ruta_imagen'];

            // Eliminar imagen anterior
            if ($imagen_anterior && file_exists("../" . $imagen_anterior)) {
                unlink("../" . $imagen_anterior);
            }

            $fileTmpPath = $_FILES['input_imagen']['tmp_name'];
            $fileName = $_FILES['input_imagen']['name'];
            $fileType = $_FILES['input_imagen']['type'];

            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedFileTypes)) {
                throw new Exception("Tipo de archivo no permitido.");
            }

            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;
            $relativePathToSave = $relativePath . $newFileName;

            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                throw new Exception("Error al subir la imagen.");
            }
        } else {
            // Reutilizar imagen existente
            $stmt = $conn->prepare("SELECT ruta_imagen FROM avistamientos_flora WHERE id_avistamiento = :id");
            $stmt->bindParam(':id', $codigo);
            $stmt->execute();
            $ruta_actual = $stmt->fetch();
            $relativePathToSave = $ruta_actual['ruta_imagen'];
        }

        // Actualizar en la base de datos
        $sql = "UPDATE avistamientos_flora SET 
                id_avistamiento_especie = :id_avistamiento_especie, 
                fecha_avistamiento = :fecha_avistamiento, 
                latitud = :latitud, 
                longitud = :longitud, 
                descripcion = :descripcion, 
                ruta_imagen = :ruta_imagen 
                WHERE id_avistamiento = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_avistamiento_especie', $id_avistamiento_especie);
        $stmt->bindParam(':fecha_avistamiento', $fecha);
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':ruta_imagen', $relativePathToSave);
        $stmt->bindParam(':id', $id_avistamiento);

        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'El avistamiento fue actualizado correctamente.'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'title' => 'Error',
                'message' => 'No se pudo actualizar el avistamiento.'
            ];
        }

        header('Location: ../admin/avistamiento_floraR.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error de base de datos',
            'message' => 'Ocurrió un error al actualizar: ' . $e->getMessage()
        ];
        header('Location: ../admin/avistamiento_floraR.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => $e->getMessage()
        ];
        header('Location: ../admin/avistamiento_floraR.php');
        exit;
    }
} else {
    // Consulta para cargar datos si no es POST (por ejemplo, mostrar en formulario)
    $sql = "SELECT * FROM avistamientos_flora WHERE id_avistamiento = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $codigo);
    $stmt->execute();
    $avistamientos = $stmt->fetch();
}
?>
