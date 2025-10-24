<?php
session_start();
include '../model/conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitización de los datos del formulario
        $id_avistamiento = filter_input(INPUT_POST, "input_especie", FILTER_SANITIZE_NUMBER_INT);
        $fecha = $_POST['input_fecha_avista'];
        $latitud = filter_input(INPUT_POST, "input_latitud", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $longitud = filter_input(INPUT_POST, "input_longitud", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $descripcion = filter_input(INPUT_POST, 'input_descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
        $relativePathToSave = null; // Inicializar variable

        // Validación y procesado de la imagen
        if (isset($_FILES['input_imagen']) && $_FILES['input_imagen']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['input_imagen']['tmp_name'];
            $fileName = $_FILES['input_imagen']['name'];
            $fileSize = $_FILES['input_imagen']['size'];
            $fileType = $_FILES['input_imagen']['type'];

            // Validar el tipo de archivo (solo imágenes)
            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileType, $allowedFileTypes)) {
                $_SESSION['notification'] = [
                    'type' => 'danger',
                    'title' => 'Error',
                    'message' => 'Tipo de archivo no permitido. Solo se aceptan imágenes JPG, PNG o GIF.'
                ];
                header('Location: ../user/avistamiento_faunaC.php');
                exit;
            }

            // Definir directorio de subida y asegurar que exista
            $uploadDir = '../uploads/avistamientos_fauna/';
            $relativePath = 'uploads/avistamientos_fauna/';

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception("No se pudo crear el directorio de subida.");
                }
            }

            // Generar un nombre único para evitar colisiones
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = uniqid('img_', true) . '.' . $fileExtension;

            // Ruta completa para mover el archivo
            $destPath = $uploadDir . $newFileName;

            // Ruta relativa para guardar en la base de datos
            $relativePathToSave = $relativePath . $newFileName;

            // Mover el archivo a la ubicación deseada
            if (!move_uploaded_file($fileTmpPath, $destPath)) {
                $_SESSION['notification'] = [
                    'type' => 'danger',
                    'title' => 'Error',
                    'message' => 'Error al subir la imagen.'
                ];
                header('Location: ../user/avistamiento_faunaC.php');
                exit;
            }
        } else {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'title' => 'Error',
                'message' => 'Debes subir una imagen del avistamiento.'
            ];
            header('Location: ../user/avistamiento_faunaC.php');
            exit;
        }

        // Consulta SQL preparada para la inserción
        $sql = "INSERT INTO avistamientos_animales (id_avistamiento_especie, fecha_avistamiento, latitud, longitud, descripcion, ruta_imagen) 
                VALUES (:id_avistamiento_especie, :fecha_avistamiento, :latitud, :longitud, :descripcion, :ruta_imagen)";
        $stmt = $conn->prepare($sql);

        // Asociar los parámetros
        $stmt->bindParam(':id_avistamiento_especie', $id_avistamiento, PDO::PARAM_INT);
        $stmt->bindParam(':fecha_avistamiento', $fecha);
        $stmt->bindParam(':latitud', $latitud);
        $stmt->bindParam(':longitud', $longitud);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':ruta_imagen', $relativePathToSave);

        // Ejecución de la consulta
        if ($stmt->execute()) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => '¡Éxito!',
                'message' => 'El avistamiento se ha registrado correctamente.'
            ];
            header('Location: ../user/avistamiento_faunaR.php');
            exit;
        } else {
            throw new Exception("Error al ejecutar la consulta SQL.");
        }
    } catch (PDOException $e) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error de base de datos',
            'message' => 'Ha ocurrido un error al guardar el avistamiento: ' . $e->getMessage()
        ];
        header('Location: ../user/avistamiento_faunaC.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => $e->getMessage()
        ];
        header('Location: ../user/avistamiento_faunaC.php');
        exit;
    }
} else {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Acceso no permitido',
        'message' => 'El formulario debe ser enviado por POST.'
    ];
    header('Location: ../user/avistamiento_faunaC.php');
    exit;
}
?>