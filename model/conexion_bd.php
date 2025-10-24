<?php
$servername = "bmllg3gwvlv11kqn179l-mysql.services.clever-cloud.com";
$dbname = "bmllg3gwvlv11kqn179l";
$username = "ugquwj92gjgnza1m";
$password = "4IiAB1ZF4E5VYxD2ZrOu"; // 🔥 PON TU PASSWORD REAL
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conexión exitosa";
  } catch(PDOException $e) {
    echo "La conexión ha fallado: " . $e->getMessage();
  }
?>

