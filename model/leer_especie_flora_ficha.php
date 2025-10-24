<?php
include 'conexion_bd.php';

function decodificar($hash){
    return base64_decode($hash);
}

$codigo = isset($_REQUEST['id_especie']) ? decodificar($_REQUEST['id_especie']) : null;
echo $codigo;

try {

    //Preparación de la consulta

    $stmt=$conn->prepare("SELECT avistamientos_flora.*,especies_flora.* FROM avistamientos_flora 
    INNER JOIN especies_flora ON avistamientos_flora.id_avistamiento_especie = especies_flora.id_especie 
    WHERE avistamientos_flora.activo = 1 AND avistamientos_flora.id_avistamiento IN (
        SELECT MIN(id_avistamiento)
        FROM avistamientos_flora
        WHERE activo = 1
        AND id_especie=:codigo
        GROUP BY id_avistamiento_especie
    ) ORDER BY avistamientos_flora.id_avistamiento_especie ASC;");
    //Ejecución de la consulta
    $stmt->bindParam(':codigo', $codigo);
    $stmt->execute();
    //Se recuperan los datos y se almacenan en una variable
    $avistamientos = $stmt->fetch();


} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}


?>