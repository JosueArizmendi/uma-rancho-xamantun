<?php
session_start(); // Añadir al inicio para usar variables de sesión
include 'conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validar campos obligatorios
        $requiredFields = [
            'input_nombrecientifico' => 'Nombre científico',
            'input_nombrecomun' => 'Nombre común',
            'input_reino' => 'Reino',
            'input_clase' => 'Clase'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field => $name) {
            if (empty($_POST[$field])) {
                $missingFields[] = $name;
            }
        }

        if (!empty($missingFields)) {
            $_SESSION['notification'] = [
                'type' => 'warning',
                'title' => 'Campos requeridos',
                'message' => 'Faltan los siguientes campos: ' . implode(', ', $missingFields)
            ];
            header('Location: ../admin/especie_faunaC.php');
            exit;
        }

        // Sanitización de datos
        $nombre_cientifico = filter_input(INPUT_POST, "input_nombrecientifico", FILTER_SANITIZE_SPECIAL_CHARS);
        $nombre_comun = filter_input(INPUT_POST, 'input_nombrecomun', FILTER_SANITIZE_SPECIAL_CHARS);
        $reino = filter_input(INPUT_POST, 'input_reino', FILTER_SANITIZE_SPECIAL_CHARS);
        $filo = filter_input(INPUT_POST, 'input_filo', FILTER_SANITIZE_SPECIAL_CHARS);
        $clase = filter_input(INPUT_POST, 'input_clase', FILTER_SANITIZE_SPECIAL_CHARS);
        $orden = filter_input(INPUT_POST, 'input_orden', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $familia = filter_input(INPUT_POST, 'input_familia', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $genero = filter_input(INPUT_POST, 'input_genero', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $especie = filter_input(INPUT_POST, 'input_especie', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $descripcion_fisica = filter_input(INPUT_POST, 'input_descripcion', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $habitat = filter_input(INPUT_POST, 'input_habitat', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $usos = trim($_POST['input_usos'] ?? '');
        $estado_conservacion = filter_input(INPUT_POST, 'input_conservacion', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        // Consulta SQL preparada
        $sql = "INSERT INTO especies_animales 
                (nombre_cientifico, nombre_comun, reino, filo, clase, orden, familia, genero, especie, 
                descripcion_fisica, habitat, usos, estado_conservacion) 
                VALUES 
                (:nombre_cientifico, :nombre_comun, :reino, :filo, :clase, :orden, :familia, :genero, :especie, 
                :descripcion_fisica, :habitat, :usos, :estado_conservacion)";

        $stmt = $conn->prepare($sql);
        
        // Parámetros con valores por defecto para campos opcionales
        $params = [
            ':nombre_cientifico' => $nombre_cientifico,
            ':nombre_comun' => $nombre_comun,
            ':reino' => $reino,
            ':filo' => $filo,
            ':clase' => $clase,
            ':orden' => $orden,
            ':familia' => $familia,
            ':genero' => $genero,
            ':especie' => $especie,
            ':descripcion_fisica' => $descripcion_fisica,
            ':habitat' => $habitat,
            ':usos' => $usos,
            ':estado_conservacion' => $estado_conservacion
        ];

        if ($stmt->execute($params)) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => '¡Registro exitoso!',
                'message' => 'Especie registrada correctamente'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'warning',
                'title' => 'Advertencia',
                'message' => 'No se pudo completar el registro'
            ];
        }

        header('Location: ../admin/especie_faunaR.php');
        exit;

    } catch (PDOException $e) {
        // Registrar error en logs
        error_log("Error en alta_especie_fauna: " . $e->getMessage());
        
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error crítico',
            'message' => 'Ocurrió un error inesperado. Por favor intente más tarde.'
        ];
        
        header('Location: ../admin/especie_faunaC.php');
        exit;
        
    } catch (Exception $e) {
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error',
            'message' => $e->getMessage()
        ];
        
        header('Location: ../admin/especie_faunaC.php');
        exit;
    }
} else {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Acceso no autorizado',
        'message' => 'Método de solicitud no válido'
    ];
    
    header('Location: ../admin/especie_faunaR.php');
    exit;
}
?>