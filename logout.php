<?php
session_start();  // Inicia la sesión (esto es necesario para acceder a la variable de sesión)
session_unset();  // Elimina todas las variables de sesión
session_destroy();  // Destruye la sesión completamente

// Redirige a la página de inicio de sesión después de cerrar sesión
header("Location: login.php");  // Cambia 'login.php' a la página donde tienes el formulario de login
exit();
?>

