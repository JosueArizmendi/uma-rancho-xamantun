<?php
// Incluir archivo de conexión a la base de datos
include('../model/conexion_bd.php');
session_start();

// Obtener datos del usuario actual
$usuario = $_SESSION['usuario'];
$query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$usuario]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
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
  <title>Plan de Trabajo</title>

  <style>
    /* Estilos personalizados */
    .document-container {
      max-width: 700px;
      max-height: 530px;
      margin: 0 auto;
      padding: 20px;
      border: 1px solid #cccacaff;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .document-viewer {
      width: 100%;
      height: 32vh;
      border: 1px solid #cccacaff;
      border-radius: 5px;
      margin-top: 20px;
      padding: 10px; /* Añade esta línea */

    }
    
    .document-title {
      text-align: center;
      margin-bottom: 20px;
      color: #1B396A ;
      font-weight: bold;
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
      position: fixed !important;
      top: 0;
      right: 0;
      margin-top: 80px;
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
              <!-- Perfil de usuario con imagen -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button" data-bs-toggle="dropdown">
                  <img src="<?php echo !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg'; ?>" alt="Perfil" class="rounded-circle" width="30" height="30">
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
                  <li><a class="dropdown-item" href="comentarios_blogR.php">Opiniones</a></li>
                </ul>
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
  
  <div class="container mt-2">
    <div id="google_translate_element" style="text-align: right;"></div>
  </div>

  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        includedLanguages: 'es,yua,en,fr,de',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE
      }, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  <div class="container">
    <div class="document-container">
      <h2 class="document-title">Registro Autorizado por la SEMARNAT</h2>
      
      <!-- Aquí va el visor de documentos -->
      <div class="document-viewer">
        <br>
        <p align="justify">Este documento oficial de la SEMARNAT certifica el
            registro de la UMA "Rancho Xamantún" para la conservación de más de 30 especies en 
            150 hectáreas de Campeche. Autoriza actividades de educación ambiental, rescate y 
            aprovechamiento no extractivo, en cumplimiento de la normativa de vida silvestre.
        </p>
      </div>
      <center><img src="../img2/pdf.png" height="70px" width="70px"></center>

      <div class="mt-1 text-center">
        <a href="../documentos/registro.pdf" class="btn btn-danger" download>
          <i class="bi bi-download"></i> Descargar Documento
        </a>
      </div>
    </div>
  </div>

  <br><br><br><br>

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