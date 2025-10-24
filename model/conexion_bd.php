<?php
$servername="localhost";
$dbname="uma_xamantun";
$username="root";
$password="";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa";
  } catch(PDOException $e) {
    echo "La conexión ha fallado: " . $e->getMessage();
  }
?>

