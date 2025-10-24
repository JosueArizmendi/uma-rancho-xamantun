<?php
$servername = "bhfes5kuuvyq21brrggx-mysql.services.clever-cloud.com";
$dbname = "bhf6s5kuuvyq21brrqgx";
$username = "uohiphv1z2w7elej";
$password = "cwjVQ1tJNeU8EdzN8ZcMI"; // 🔥 PON TU PASSWORD REAL
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa";
  } catch(PDOException $e) {
    echo "La conexión ha fallado: " . $e->getMessage();
  }
?>

