<?php
$servername = "bhf6s5kuuvyq21brrqgx-mysql.services.clever-cloud.com";
$dbname = "bhf6s5kuuvyq21brrqgx";
$username = "uohiphvlz2w7e1cj";
$password = "cwjVQ1tJNeU8EdzN8ZcMI"; // 🔥 PON TU PASSWORD REAL
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa";
  } catch(PDOException $e) {
    echo "La conexión ha fallado: " . $e->getMessage();
  }
?>

