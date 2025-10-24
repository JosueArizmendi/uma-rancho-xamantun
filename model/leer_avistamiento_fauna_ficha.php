<?php
include 'conexion_bd.php';

function decodificar($hash){
    return base64_decode($hash);
}

$codigo = isset($_REQUEST['id_avistamiento']) ? decodificar( $_REQUEST['id_avistamiento'])  : null;

    // Consulta SQL para lectura de datos
    $sql2 = "SELECT avistamientos_animales.*,especies_animales.* FROM avistamientos_animales 
    INNER JOIN especies_animales ON avistamientos_animales.id_avistamiento_especie = especies_animales.id_especie 
    WHERE avistamientos_animales.id_avistamiento = :id ORDER BY avistamientos_animales.id_avistamiento_especie ASC;";
    $miConsulta = $conn->prepare($sql2);

    // Asociar los parámetros
    $miConsulta->bindParam(':id', $codigo);
    // Ejecuta de la consulta
    $miConsulta->execute();
    
    $avistamientos = $miConsulta->fetch();
?>