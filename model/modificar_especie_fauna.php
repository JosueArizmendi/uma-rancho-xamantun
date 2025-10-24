<?php
session_start(); // Añadir al inicio para usar variables de sesión
include 'conexion_bd.php';

function decodificar($hash){
    return base64_decode($hash);
}

$codigo = isset($_REQUEST['id_especie']) ? decodificar($_REQUEST['id_especie']) : null;

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Procesar formulario de actualización
        $id = filter_input(INPUT_POST, "id_especie", FILTER_SANITIZE_NUMBER_INT);
        $nombre_cientifico = filter_input(INPUT_POST, "input_nombrecientifico", FILTER_SANITIZE_SPECIAL_CHARS);
        $nombre_comun = filter_input(INPUT_POST, 'input_nombrecomun', FILTER_SANITIZE_SPECIAL_CHARS);
        $reino = filter_input(INPUT_POST, 'input_reino', FILTER_SANITIZE_SPECIAL_CHARS);
        $filo = filter_input(INPUT_POST, 'input_filo', FILTER_SANITIZE_SPECIAL_CHARS);
        $clase = filter_input(INPUT_POST, 'input_clase', FILTER_SANITIZE_SPECIAL_CHARS);
        $orden = filter_input(INPUT_POST, 'input_orden', FILTER_SANITIZE_SPECIAL_CHARS);
        $familia = filter_input(INPUT_POST, 'input_familia', FILTER_SANITIZE_SPECIAL_CHARS);
        $genero = filter_input(INPUT_POST, 'input_genero', FILTER_SANITIZE_SPECIAL_CHARS);
        $especie = filter_input(INPUT_POST, 'input_especie', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion_fisica = filter_input(INPUT_POST, 'input_descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
        $habitat = filter_input(INPUT_POST, 'input_habitat', FILTER_SANITIZE_SPECIAL_CHARS);
        $estado_conservacion = filter_input(INPUT_POST, 'input_conservacion', FILTER_SANITIZE_SPECIAL_CHARS);
        $usos = trim($_POST['input_usos']);


        $sql = "UPDATE especies_animales SET 
                nombre_cientifico = :nombre_cientifico, 
                nombre_comun = :nombre_comun, 
                reino = :reino,
                filo = :filo,
                clase = :clase,
                orden = :orden,
                familia = :familia,
                genero = :genero,
                especie = :especie,
                descripcion_fisica = :descripcion_fisica,
                habitat = :habitat,
                usos = :usos,
                estado_conservacion = :estado_conservacion 
                WHERE id_especie = :id";

        $stmt = $conn->prepare($sql);
        
        // Bind parameters
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
            ':estado_conservacion' => $estado_conservacion,
            ':id' => $id
        ];

        $stmt->execute($params);

        $_SESSION['notification'] = [
            'type' => 'success',
            'title' => '¡Actualización exitosa!',
            'message' => 'La especie se ha actualizado correctamente'
        ];
        
        header('Location: ../admin/especie_faunaR.php');
        exit;

    } else {
        // Consulta para obtener datos existentes
        $sql = "SELECT * FROM especies_animales WHERE id_especie = :id";
        $miConsulta = $conn->prepare($sql);
        $miConsulta->bindParam(':id', $codigo);
        $miConsulta->execute();
        
        if($miConsulta->rowCount() == 0) {
            $_SESSION['notification'] = [
                'type' => 'danger',
                'title' => 'Error',
                'message' => 'Especie no encontrada'
            ];
            header('Location: ../admin/especie_faunaR.php');
            exit;
        }
        
        $especies = $miConsulta->fetch();
    }

} catch (PDOException $e) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error de base de datos',
        'message' => 'Error: ' . $e->getMessage()
    ];
    
    $redirect = $_SERVER['REQUEST_METHOD'] == 'POST' 
        ? '../admin/especie_faunaR.php' 
        : '../admin/especie_faunaU.php?id_especie=' . urlencode($_REQUEST['id_especie']);
        
    header("Location: $redirect");
    exit;
    
} catch (Exception $e) {
    $_SESSION['notification'] = [
        'type' => 'danger',
        'title' => 'Error',
        'message' => $e->getMessage()
    ];
    
    header('Location: ../admin/especie_faunaR.php');
    exit;
}