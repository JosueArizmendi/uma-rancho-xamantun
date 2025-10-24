<?php
include 'conexion_bd.php';

function decodificar($hash){
    return base64_decode($hash);
}

$codigo = isset($_REQUEST['id_especie']) ? decodificar($_REQUEST['id_especie']) : null;
echo $codigo;

try {

    //Preparación de la consulta
 
    $stmt=$conn->prepare("SELECT avistamientos_animales.*,especies_animales.* FROM avistamientos_animales 
    INNER JOIN especies_animales ON avistamientos_animales.id_avistamiento_especie = especies_animales.id_especie 
    WHERE avistamientos_animales.activo = 1 AND avistamientos_animales.id_avistamiento IN (
        SELECT MIN(id_avistamiento)
        FROM avistamientos_animales
        WHERE activo = 1
        AND id_especie=:codigo
        GROUP BY id_avistamiento_especie
    ) ORDER BY avistamientos_animales.id_avistamiento_especie ASC;");
    //Ejecución de la consulta
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();
    //Se recuperan los datos y se almacenan en una variable
    $avistamientos = $stmt->fetch();


} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}


?>