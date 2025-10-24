<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion_bd.php';

// Lógica para iniciar sesión
if (isset($_POST['login'])) {
    $usuario = $_POST['nom_usuario'];
    $password = $_POST['contraseña'];

    if (empty($usuario) || empty($password)) {
        $error_message = "Por favor, completa todos los campos.";
    } else {
        // Verificar si el valor ingresado parece un correo electrónico
        if (filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
            // Si es un correo, podemos tratarlo igual que un nombre de usuario y buscar en el campo nom_usuario
            $query = "SELECT * FROM usuarios WHERE nom_usuario = ? AND activo = 1";
        } else {
            // Si no es un correo, lo tratamos como un nombre de usuario normal
            $query = "SELECT * FROM usuarios WHERE nom_usuario = ? AND activo = 1";
        }
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if ($result) {
            // Verificar si la contraseña es correcta
            if ($password === $result['contraseña']) {
                session_start();
                $_SESSION['usuario'] = $result['nom_usuario'];
                // Verificar el rol del usuario
                if ($result['rol'] == 'admin') {
                    header('Location: admin/template.php'); // Redirige al admin
                } else {
                    header('Location: user/template2.php'); // Redirige a usuario normal
                }
                exit();
            } else {
                $error_message = "Contraseña incorrecta.";
            }
        } else {
            $error_message = "Usuario no encontrado.";
        }
    }
}

// Lógica para registrar usuarios
if (isset($_POST['register'])) {
    // Recuperar los datos del formulario
    $nombre = $_POST['nombre_s'];
    $apellido1 = $_POST['primer_apellido'];
    $apellido2 = $_POST['segundo_apellido'];
    $usuario = $_POST['nom_usuario'];
    $password = $_POST['contraseña'];

    // Verificar que todos los campos estén llenos
    if (empty($nombre) || empty($apellido1) || empty($apellido2) || empty($usuario) || empty($password)) {
        $error_message = "Por favor, completa todos los campos.";
    } else {
        // Verificar si el nombre de usuario ya existe
        $query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $usuario, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si el nombre de usuario ya existe
        if ($result) {
            $error_message = "El nombre de usuario ya está registrado.";
        } else {
            // Insertar nuevo usuario
            $query = "INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, nom_usuario, contraseña, rol) 
                      VALUES (?, ?, ?, ?, ?, 'usuario')";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
            $stmt->bindParam(2, $apellido1, PDO::PARAM_STR);
            $stmt->bindParam(3, $apellido2, PDO::PARAM_STR);
            $stmt->bindParam(4, $usuario, PDO::PARAM_STR);
            $stmt->bindParam(5, $password, PDO::PARAM_STR);
            
            // Ejecutar la inserción
            if ($stmt->execute()) {
                $success_message = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
            } else {
                $error_message = "Error al registrar el usuario.";
            }
        }
    }
}
?>