<?php
include 'conexion_bd.php';

try {

    //Preparación de la consulta

    $stmt=$conn->prepare("SELECT avistamientos_animales.*,especies_animales.* FROM avistamientos_animales 
    INNER JOIN especies_animales ON avistamientos_animales.id_avistamiento_especie = especies_animales.id_especie 
    WHERE avistamientos_animales.activo = 1 AND avistamientos_animales.id_avistamiento IN (
        SELECT MIN(id_avistamiento)
        FROM avistamientos_animales
        WHERE activo = 1
        GROUP BY id_avistamiento_especie
    ) ORDER BY avistamientos_animales.id_avistamiento_especie ASC;");
    //Ejecución de la consulta
    $stmt->execute();
    //Se recuperan los datos y se almacenan en una variable
    $avistamientos = $stmt->fetchAll();


} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}


?>