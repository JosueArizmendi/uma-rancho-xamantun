<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está logueado (si la sesión está activa)
if (!isset($_SESSION['usuario'])) {
  // Si no está logueado, redirige al login.php con el parámetro 'error'
  header("Location: ../login.php?error=session_expired");
  exit;
}

// Obtener datos del usuario
$usuario = $_SESSION['usuario'];
include('../model/conexion_bd.php');
$query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $usuario, PDO::PARAM_STR);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../CSS/custom-styles.css">
  <link rel="stylesheet" href="../CSS/style2.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <title>Document</title>
  <style>
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

  #offcanvasNavbar {
    color: white !important; 
  }

  .card {
    border: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s;
    overflow: hidden;
  }
    
  /*.card:hover {
    transform: translateY(-5px);
  }*/
    
  .carousel-item img {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }
    
  .welcome-card {
    background: linear-gradient(105deg, #1B396A  0%, #1B396A  100%);
    color: white;
    width: 1100px;
    border-color: #f7faf8ff;
  }
    
  .feature-icon {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #dc3545;
  }
    
  .feature-card {
    text-align: center;
    padding: 20px;
    height: 100%;
  }
    
  .map-container {
    height: 300px;
    width: 450px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    margin-bottom: 30px;
  }
    
  .gallery-container {
    margin-top: 30px;
  }
    
  .gallery-img {
    border-radius: 10px;
    transition: transform 0.3s;
    height: 200px;
    object-fit: cover;
    width: 100%;
  }
    
  .gallery-img:hover {
    transform: scale(1.05);
  }
    
  footer {
    margin-top: 50px;
  }
    
  .info-window-content {
    max-width: 250px;
    text-align: center;
  }
    
  .info-window-content img {
    border-radius: 5px;
    margin-bottom: 10px;
    width: 100%;
  }

  .card {
    border: 2px solid #ccc;  /* Borde gris claro */
  }
 </style>
</head>

<body>
  <!-- El contenido de tu página va aquí -->
  <header>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <div class="me-3 ms-2">
        <img src="../img2/rancho.png" class="rounded" width="60" height="56">
      </div>
      <div class="container-fluid">
        <a class="navbar-brand fs-3 text-white" href="template2.php">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
          aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end text-bg-dark " tabindex="-1" id="offcanvasNavbar"
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
                  <li><a class="dropdown-item" href="acerca_de.php">Acerca del Rancho Xamantún</a></li>
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
                  data-bs-toggle="dropdown">Opciones</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="avistamiento_faunaR.php">Registros avistamientos Fauna</a></li>
                  <li><a class="dropdown-item" href="avistamiento_floraR.php">Registros avistamientos Flora</a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link fs-3 text-white" href="blogs.php">Blogs</a>
              </li>
              <li class="nav-item fs-2">
              <?php 
                // Asegúrate de que $id_usuario_actual esté disponible
                if (isset($user_data['id'])) {
                    $id_usuario_actual = $user_data['id'];
                    include('../includes/notificaciones.php');
                }
              ?>
              </li>
              <!-- Perfil de usuario con imagen -->
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" role="button" data-bs-toggle="dropdown">
                  <img src="<?php echo !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg'; ?>"  class="rounded-circle" width="30" height="30">
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>

        <!-- Botones de Cerrar sesión y Traducir a Maya -->
        <div class="d-flex justify-content-end">
          <a href="../logout.php" class="btn btn-danger ms-2">Cerrar sesión</a>
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
  <br>

  <main class="fixed-top-offset">
     <div class="container" style="max-width: 600px; margin: 0 auto;">
      <div class="card card-default border:3 p-1 mb-8 text-center">
          <!-- Aquí va el contenido de la página -->
        <h5 style="font-size: 30px;">
          <?php
          // Verificar si la sesión contiene el nombre de usuario
           if (isset($_SESSION['usuario'])) {
            echo "Bienvenido: " . htmlspecialchars($_SESSION['usuario']) . "!";
           } else {
            echo "Bienvenido: usuario invitado!";
           }
          ?>
        </h5>
      </div>
    </div>
    <br><br>
    <center>
      <div style="
        background-color: #dce0e0ff; 
        height: 407px; 
        margin-top: -20px; 
        border: 15px; 
        border: 15px;
        position: relative;
        z-index: 0;">
      <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
              <p><img src="../img2/especie.jpg" width="600" height="405"></p>
            </div>
            <div class="carousel-item">
              <p><img src="../img2/especie2.jpg" width="600" height="405"></p>
            </div>
            <div class="carousel-item">
              <p><img src="../img2/especie3.jpg" width="600" height="405"></p>
            </div>
            <div class="carousel-item">
              <p><img src="../img2/especie4.jpg" width="600" height="405"></p>
        </div>
      </div>
     <!-- Flechas de navegación -->
       <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleSlidesOnly" data-bs-slide="prev">
         <span class="carousel-control-prev-icon" aria-hidden="true"></span>
         <span class="visually-hidden"></span>
         </button>
         <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleSlidesOnly" data-bs-slide="next">
         <span class="carousel-control-next-icon" aria-hidden="true"></span>
         <span class="visually-hidden"></span>
       </button>
      </div>
     </div>
    </center> 
  </main>

  <br>
  <center>
    <div style="
      background-color: #dce0e0ff; 
      height: 555px; 
      margin-top: -20px; 
      border: 15px; 
      border: 15px;
      position: relative;
      z-index: 0;">
    <!-- Main Content -->
    <div class="container mt-4">
      <!-- Welcome Card -->
      <div class="card welcome-card mb-4">
        <div class="card-body py-2">
          <div class="d-flex justify-content-between align-items-center">
            <!-- Imagen a la izquierda -->
            <img src="../img2/rancho.png" class="rounded" width="70" height="68">
            
            <!-- Texto en el centro -->
            <h2 class="text-center mb-0">Ubicación del Rancho Xamantún</h2>
            
            <!-- Imagen a la derecha -->
            <img src="../img2/rancho.png" class="rounded" width="70" height="68">
          </div>
          <br>
          
          <!-- Contenedor para mapa y galería -->
          <div class="row">
            <!-- Columna para la galería (izquierda) -->
            <div class="col-md-6">
              <h4 class="text-center mb-3">Galería</h4>
              <div class="row">
                <!-- Imagen 1 -->
                <div class="col-6 mb-3">
                  <div class="card h-100">
                    <img src="../img2/produccion.jpg" 
                        class="gallery-img" alt="Bosque" style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                      <h6 class="card-title mb-0" style="color:black">Plan de Producción</h6>
                    </div>
                  </div>
                </div>
                
                <!-- Imagen 2 -->
                <div class="col-6 mb-3">
                  <div class="card h-100">
                    <img src="../img2/especie2.jpg" 
                        class="gallery-img" alt="Fauna" style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                      <h6 class="card-title mb-0" style="color:black">Poda y corte de bambu.</h6>
                    </div>
                  </div>
                </div>
                
                <!-- Imagen 3 -->
                <div class="col-6 mb-3">
                  <div class="card h-100">
                    <img src="https://mexicogob.com/escuelas/wp-content/uploads/2022/03/thumbnail-1893.jpg" 
                        class="gallery-img" alt="Flora" style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                      <h6 class="card-title mb-0" style="color:black">Rancho de Xamantún</h6>
                    </div>
                  </div>
                </div>
                
                <!-- Imagen 4 -->
                <div class="col-6 mb-3">
                  <div class="card h-100">
                    <img src="../img2/maps2.png" 
                        class="gallery-img" alt="Paisaje" style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                      <h6 class="card-title mb-0" style="color:black">Ubicación</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Columna para el mapa (derecha) -->
            <div class="col-md-6">
              <div class="map-container" style="height: 400px;">
                <iframe 
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3732.650300496026!2d-90.4213538!3d19.7224082!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85f825034c7fdc5b%3A0xbdf8dc044d27a1ba!2sRancho%20Xamant%C3%BAn!5e1!3m2!1ses!2smx!4v1725123456789!5m2!1ses!2smx" 
                  width="100%" 
                  height="100%" 
                  style="border:0;" 
                  allowfullscreen="" 
                  loading="lazy" 
                  referrerpolicy="no-referrer-when-downgrade">
                </iframe>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
   </div>
  </center>
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
    <br><br>
</body>
</html>
