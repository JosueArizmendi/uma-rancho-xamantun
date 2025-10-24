<?php
// Incluir archivo de conexión a la base de datos
include('../model/conexion_bd.php');

session_start(); // Inicia la sesión

// Verificar si el ID del usuario ha sido proporcionado
if (isset($_GET['id'])) {
  // Decodificar el ID desde base64
  $encoded_id = $_GET['id'];
  $user_id = base64_decode($encoded_id); // Ahora tienes el ID original

  // Obtener los datos del usuario a editar
  $query = "SELECT * FROM usuarios WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
  $stmt->execute();
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  // Si no se encuentra el usuario
  if (!$usuario) {
      $_SESSION['error'] = "Usuario no encontrado.";
      header("Location: usuarios.php");
      exit;
  }
} else {
  $_SESSION['error'] = "No se proporcionó un ID de usuario.";
  header("Location: usuarios.php");
  exit;
}

// Lógica para actualizar un usuario
if (isset($_POST['update_user'])) {
  $nombre = $_POST['nombre_s'];
  $apellido1 = $_POST['primer_apellido'];
  $apellido2 = $_POST['segundo_apellido'];
  $usuario = $_POST['nom_usuario'];
  $rol = $_POST['rol'];
  $password = $_POST['contraseña'];

  // Actualizar los datos del usuario
  $query = "UPDATE usuarios SET nombre = ?, primer_apellido = ?, segundo_apellido = ?, nom_usuario = ?, contraseña = ?, rol = ? WHERE id = ?";
  $stmt = $conn->prepare($query);
  $stmt->bindParam(1, $nombre, PDO::PARAM_STR);
  $stmt->bindParam(2, $apellido1, PDO::PARAM_STR);
  $stmt->bindParam(3, $apellido2, PDO::PARAM_STR);
  $stmt->bindParam(4, $usuario, PDO::PARAM_STR);
  $stmt->bindParam(5, $password, PDO::PARAM_STR);
  $stmt->bindParam(6, $rol, PDO::PARAM_STR);
  $stmt->bindParam(7, $user_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
      $_SESSION['notification'] = [
          'type' => 'success',
          'title' => 'Éxito',
          'message' => 'Usuario actualizado correctamente'
      ];
      header("Location: usuarios.php");
      exit;
  } else {
      $_SESSION['notification'] = [
          'type' => 'danger',
          'title' => 'Error',
          'message' => 'Ocurrió un error al actualizar el usuario'
      ];
      header("Location: usuarios.php");
      exit;
  }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../CSS/custom-styles.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <title>Crear Usuario</title>

  <style>
    /* Estilos personalizados */
    .form-container {
      max-width: 500px; /* Redefinimos el tamaño máximo del formulario */
      margin: 0 auto; /* Centramos el formulario */
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 8px;
      background-color: #f9f9f9; /* Agregamos un fondo */
    }
    .form-control {
        max-width: 100%; /* Aseguramos que los campos se ajusten al contenedor */
        width: 100%; /* Ajustamos los campos a un 90% del ancho del contenedor */
    }

    .container {
        padding-top: 50px; /* Añadimos espacio para no solapar con el navbar */
    }
    /* Ocultar la barra de traducción */
    .goog-te-banner-frame {
       display: none !important;
    }

    /* Ocultar el logo de Google */
    .goog-logo-link {
      display: none !important;
    }

    /* Asegura que el traductor se vea por encima de otros elementos si es necesario */
    #google_translate_element {
      z-index: 9999 !important;
      position: fixed !important; /* Fija el traductor en la pantalla */
      top: 0; /* Lo coloca en la parte superior */
      right: 0; /* Lo coloca a la derecha */
      margin-top: 80px; /* Ajusta según la altura de tu navbar */
    }
     /* Estilos para submenús desplegables */
    .dropdown-submenu {
      position: relative;
    }

    .dropdown-submenu .dropdown-menu {
      top: 0;
      left: 100%;
      margin-top: -1px;
      margin-left: .1rem;
    }

    .dropdown-submenu:hover .dropdown-menu {
      display: block;
    }

    .form-container {
    border: 2px solid #ccc;  /* Borde gris claro */
    }
  </style>
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <div class="me-3 ms-2">
        <img src="../img2/rancho.png" class="rounded" width="60" height="56">
      </div>
      <div class="container-fluid">
        <a class="navbar-brand fs-3 text-white" href="template.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
          aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar"
          aria-labelledby="offcanvasDarkNavbarLabel">
          <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Opciones</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
              aria-label="Close"></button>
          </div>
          <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-start flex-grow-1">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button"
                  data-bs-toggle="dropdown">Nosotros</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="historia.php">Historia</a></li>
                  <li><a class="dropdown-item" href="acerca2_de.php">Acerca del Rancho Xamantún</a></li>
                  <li><a class="dropdown-item" href="plan.php">Plan de Manejo</a></li>
                  <li><a class="dropdown-item" href="autorizacion.php">Registro Autorizado por parte de la SEMARNAT</a></li>
                  <li><a class="dropdown-item" href="app.php">Mapa Interactivo</a></li>
                  <li><a class="dropdown-item" href="curriculum.php">Curriculum</a></li> 
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button"
                  data-bs-toggle="dropdown">Fauna</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_fauna.php">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_fauna.php">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                  data-bs-toggle="dropdown">Flora</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_flora.php">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_flora.php">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                      data-bs-toggle="dropdown">Administración</a>
                  <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="especie_faunaR.php">Registros especies Fauna</a></li>
                      <li><a class="dropdown-item" href="avistamiento_faunaR.php">Registros avistamientos Fauna</a></li>
                      <li><a class="dropdown-item" href="especie_floraR.php">Registros especies Flora</a></li>
                      <li><a class="dropdown-item" href="avistamiento_floraR.php">Registros avistamientos Flora</a></li>
                      <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#">Catálogo de Reg. Especies Fauna</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Mammalia">Mamíferos</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Aves">Aves</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Reptilia">Reptiles</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Amphibia">Anfibios</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Pisces">Peces</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Insecta">Insectos</a></li>
                              <li><a class="dropdown-item" href="especie_faunaR.php?categoria=Arachnida">Arácnidos</a></li>
                          </ul>
                      </li>
                      <li class="dropdown-submenu">
                          <a class="dropdown-item dropdown-toggle" href="#">Catálogo de Reg. Especies Flora</a>
                          <ul class="dropdown-menu">
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Magnoliopsida">Dicotiledóneas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Liliopsida">Monocotiledóneas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Gimnospermas">Gimnospermas</a></li>
                              <li><a class="dropdown-item" href="especie_floraR.php?categoria=Pteridopsida">Helechos</a></li>
                          </ul>
                      </li>
                  </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link text-dark fs-3" href="usuarios.php">Usuarios</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-dark fs-3" href="blogsR.php">Blogs</a>
              </li>
            </ul>
          </div>
        </div>

        <div class="d-flex align-items-center">
          <div class="d-flex justify-content-end">
            <a href="../logout.php" class="btn btn-danger ms-2">Cerrar sesión</a>
          </div>
        </div>
      </div>
    </nav>
  </header>
  <div class="container mt-2"><!-- Ajustamos el padding-top para evitar que se oculte debajo de la cabecera fija -->
    <div id="google_translate_element" style="text-align: right;"></div>
  </div>

  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        includedLanguages: 'es,yua,en,fr,de',  // Idiomas disponibles
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
      }, 'google_translate_element');
    }

  </script>
  <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  
  <div class="container">
    <div class="d-flex justify-content-center">
        <div class="form-container">
            <form method="POST" action="editar_usuario.php?id=<?= base64_encode($usuario['id']); ?>">
             <h4 class="text-center">Editar Usuario</h4>
                <div class="mb-3">
                    <label for="nombre_s" class="form-label">Nombre:</label>
                    <input type="text" class="form-control" id="nombre_s" name="nombre_s" value="<?= htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="primer_apellido" class="form-label">Primer Apellido:</label>
                    <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" value="<?= htmlspecialchars($usuario['primer_apellido']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="segundo_apellido" class="form-label">Segundo Apellido:</label>
                    <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" value="<?= htmlspecialchars($usuario['segundo_apellido']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nom_usuario" class="form-label">Nombre de Usuario:</label>
                    <input type="text" class="form-control" id="nom_usuario" name="nom_usuario" value="<?= htmlspecialchars($usuario['nom_usuario']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="rol" class="form-label">Tipo de Usuario</label>
                    <select name="rol" class="form-control" required>
                        <option value="usuario" <?= $usuario['rol'] == 'usuario' ? 'selected' : '' ?>>Usuario Normal</option>
                        <option value="admin" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="contraseña" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="contraseña" name="contraseña" value="<?= htmlspecialchars($usuario['contraseña']); ?>" required>
                </div>
                <button type="submit" name="update_user" class="btn btn-success">Actualizar Usuario</button>
                <a href="usuarios.php" class="btn btn-secondary ms-2">Cancelar</a>
            </form>
        </div>
    </div>
</div>


<br>
    <div style="padding-bottom:0.5cm" class="align-items-center">
        <div class="col text-center">
            <a href="usuarios.php" class="btn btn-success bi bi-arrow-return-left"></a>
        </div>
    </div>
    
    <br><br>
    <footer class="bg-danger text-white text-center py-3 fixed-bottom">
        <div class="container-fluid">
            <h4 class="d-flex justify-content-center align-items-center">
                &copy; 2025 - UMA del Rancho Xamantún. Todos los derechos reservados. Versión 2.0
                <div class="bg-white rounded-pill p-0 d-flex align-items-center ms-3">
                    <a href="https://www.facebook.com" target="_blank" class="ms-1 me-1">
                        <i class="bi bi-facebook" style="font-size: 1.5rem; color: #1877F2;"></i>
                    </a>
                    <a href="https://www.instagram.com" target="_blank" class="ms-1 me-1">
                        <i class="bi bi-instagram" style="font-size: 1.5rem; color: #E4405F;"></i>
                    </a>
                    <a href="https://wa.me/" target="_blank" class="ms-1 me-1">
                        <i class="bi bi-whatsapp" style="font-size: 1.5rem; color: #25D366;"></i>
                    </a>
                </div>
            </h4>
        </div>
    </footer>
</body>
</html>
