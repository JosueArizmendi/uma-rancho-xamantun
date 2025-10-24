<?php
include 'conexion_bd.php';

function decodificar($hash){
    return base64_decode($hash);
}

$codigo = isset($_REQUEST['id_avistamiento']) ? decodificar ($_REQUEST['id_avistamiento']) : null;

    // Consulta SQL para lectura de datos
    $sql2 = "SELECT avistamientos_flora.*,especies_flora.* FROM avistamientos_flora 
    INNER JOIN especies_flora ON avistamientos_flora.id_avistamiento_especie = especies_flora.id_especie 
    WHERE avistamientos_flora.id_avistamiento = :id ORDER BY avistamientos_flora.id_avistamiento_especie ASC;";
    $miConsulta = $conn->prepare($sql2);

    // Asociar los parámetros
    $miConsulta->bindParam(':id', $codigo);
    // Ejecuta de la consulta
    $miConsulta->execute();
    
    $avistamientos = $miConsulta->fetch();

?>