<?php
include 'conexion_bd.php';

try {

    $stmt=$conn->prepare("SELECT * FROM especies_animales WHERE activo = 1");
    $stmt->execute();







    //$sql="SELECT * FROM especies_animales";
    //$stmt = $conn->query($sql);

    $especies = $stmt->fetchAll();


} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}


?>