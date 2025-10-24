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
  <title>Historia del Rancho Xamantún</title>
  <style>
    /* Estilos personalizados */
    .story-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
      background-color: #fff;
    }
    
    .story-header {
      background: linear-gradient(105deg, #1B396A 0%, #2c5282 100%);
      color: white;
      padding: 50px 30px;
      border-radius: 15px;
      margin-bottom: 40px;
      text-align: center;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      position: relative;
      overflow: hidden;
    }
    
    .story-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('../img2/pattern.png') repeat;
      opacity: 0.1;
    }
    
    .story-title {
      font-size: 3rem;
      font-weight: bold;
      margin-bottom: 15px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    
    .story-subtitle {
      font-size: 1.3rem;
      opacity: 0.95;
      font-style: italic;
    }
    
    .chapter {
      margin-bottom: 60px;
      padding: 40px;
      border-radius: 15px;
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      box-shadow: 0 5px 20px rgba(0,0,0,0.08);
      border-left: 5px solid #1B396A;
      position: relative;
    }
    
    .chapter-title {
      color: #1B396A;
      font-size: 2rem;
      margin-bottom: 25px;
      padding-bottom: 15px;
      border-bottom: 2px solid #e9ecef;
      font-weight: bold;
    }
    
    .story-text {
      font-size: 1.1rem;
      line-height: 1.8;
      text-align: justify;
      color: #444;
      margin-bottom: 25px;
    }
    
    .story-image {
      width: 100%;
      border-radius: 12px;
      margin: 25px 0;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    
    .story-image:hover {
      transform: scale(1.02);
    }
    
    .quote {
      background: linear-gradient(135deg, #1B396A 0%, #2c5282 100%);
      color: white;
      padding: 30px;
      border-radius: 12px;
      margin: 35px 0;
      font-style: italic;
      font-size: 1.2rem;
      text-align: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      position: relative;
    }
    
    .quote::before {
      content: '"';
      font-size: 4rem;
      position: absolute;
      top: 10px;
      left: 20px;
      opacity: 0.3;
    }
    
    .legacy-section {
      background: linear-gradient(135deg, #1B396A 0%, #2c5282 100%);
      color: white;
      padding: 50px 40px;
      border-radius: 15px;
      margin: 50px 0;
      text-align: center;
    }
    
    .legacy-title {
      font-size: 2.5rem;
      margin-bottom: 30px;
      font-weight: bold;
    }
    
    .feature-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
      margin-top: 40px;
    }
    
    .feature-card {
      background: rgba(255,255,255,0.1);
      padding: 25px;
      border-radius: 12px;
      text-align: center;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
      transition: transform 0.3s ease;
    }
    
    .feature-card:hover {
      transform: translateY(-5px);
    }
    
    .feature-icon {
      font-size: 2.5rem;
      margin-bottom: 15px;
      color: #ffd700;
    }
    
    .gallery-container {
      margin: 40px 0;
    }
    
    .gallery-img {
      border-radius: 10px;
      height: 200px;
      object-fit: cover;
      width: 100%;
      margin-bottom: 15px;
      transition: transform 0.3s ease;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .gallery-img:hover {
      transform: scale(1.05);
    }
    
    .floating-element {
      animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
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
    
    #offcanvasNavbar {
      color: white !important; 
    }
    
    .container {
      padding-top: 50px;
    }
    
    @media (max-width: 768px) {
      .story-title {
        font-size: 2.2rem;
      }
      
      .chapter {
        padding: 25px;
      }
      
      .chapter-title {
        font-size: 1.6rem;
      }
      
      .story-text {
        font-size: 1rem;
      }
    }

    .document-container {
      max-width: 900px;
      margin: 0 auto;
      padding: 20px;
      border-radius: 8px;
      background-color: #fff;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .document-title {
      text-align: center;
      margin-bottom: 30px;
      color: #1B396A;
      font-weight: bold;
    }

    .container {
        padding-bottom: 50px;
    }
    
    .goog-te-banner-frame {
      display: none !important;
    }

    .goog-logo-link {
      display: none !important;
    }
  
    #google_translate_element {
      z-index: 9999 !important;
      position: fixed !important;
      top: 0;
      right: 0;
      margin-top: 80px;
    }
    
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
                if (isset($user_data['id'])) {
                    $id_usuario_actual = $user_data['id'];
                    include('../includes/notificaciones.php');
                }
              ?>
              </li>
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

        <div class="d-flex justify-content-end">
          <a href="../logout.php" class="btn btn-danger ms-2">Cerrar sesión</a>
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
  <div class="container story-container" style="border: 2px solid #400eca;  /* Borde gris claro */">
    <!-- Encabezado de la historia -->
    <div class="story-header floating-element">
      <h1 class="story-title">Rancho Xamantún</h1>
      <p class="story-subtitle">Donde la tradición y la conservación se encuentran</p>
    </div>
    
    <!-- Capítulo 1: Orígenes -->
    <div class="chapter">
      <h2 class="chapter-title">Los Primeros Días</h2>
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="story-text">
            En las tierras fértiles de Campeche, cerca de la pintoresca localidad de Chiná, nació el Rancho Xamantún. 
            Su nombre, que en lengua maya significa "lugar del jaguar oculto", ya anunciaba el profundo respeto por la 
            naturaleza que caracterizaría este lugar para siempre.
          </p>
          <p class="story-text">
            Los primeros dueños llegaron con sueños de prosperidad, pero también con una comprensión ancestral de que 
            la tierra no es un recurso para explotar, sino un legado para proteger. Desde el principio, establecieron 
            una relación especial con el entorno, aprendiendo a convivir con la rica biodiversidad que habitaba estos 
            parajes.
          </p>
        </div>
        <div class="col-md-6">
          <img src="../img2/maps.png" alt="Orígenes del Rancho Xamantún" class="story-image">
        </div>
      </div>
    </div>
    
    <!-- Cita inspiradora -->
    <div class="quote">
      "Cada amanecer en Xamantún nos recuerda que somos parte de algo más grande, que nuestra verdadera riqueza 
      está en la vida que nos rodea y en el legado que dejaremos a las futuras generaciones."
    </div>
    
    <!-- Capítulo 2: Transformación -->
    <div class="chapter">
      <h2 class="chapter-title">Un Cambio de Conciencia</h2>
      <div class="row align-items-center">
        <div class="col-md-6">
          <img src="../img2/especie5.jpg" alt="Transformación del rancho" class="story-image">
        </div>
        <div class="col-md-6">
          <p class="story-text">
            Con el paso del tiempo, los propietarios del rancho comenzaron a notar cambios sutiles pero significativos 
            en el ecosistema. Los cantos de ciertas aves se hacían menos frecuentes, algunos animales que antes eran 
            comunes ahora eran rarezas, y el equilibrio natural parecía estar en peligro.
          </p>
          <p class="story-text">
            Fue entonces cuando tomaron una decisión valiente: transformar el rancho de una explotación tradicional 
            en un santuario de conservación. Redujeron las áreas de pastoreo, implementaron prácticas agrícolas 
            sostenibles y comenzaron a restaurar los espacios naturales que habían sido alterados.
          </p>
        </div>
      </div>
    </div>
    
    <!-- Capítulo 3: Conservación -->
    <div class="chapter">
      <h2 class="chapter-title">Nacimiento de un Santuario</h2>
      <p class="story-text">
        El proceso de transformación no fue fácil, pero cada pequeño avance traía consigo recompensas inesperadas. 
        Poco a poco, la vida silvestre comenzó a regresar.
      </p>
      
      <div class="row gallery-container">
        <div class="col-md-4">
          <img src="../img2/jaguar.jfif" alt="Jaguar en Xamantún" class="gallery-img">
        </div>
        <div class="col-md-4">
          <img src="../img2/venado.jpg" alt="Aves del rancho" class="gallery-img">
        </div>
        <div class="col-md-4">
          <img src="../img2/especie.jpg" alt="Biodiversidad" class="gallery-img">
        </div>
      </div>
      
      <p class="story-text">
        El reconocimiento como Unidad de Manejo Ambiental (UMA) marcó un hito importante. No solo validó los esfuerzos 
        de conservación, sino que abrió las puertas a nuevas posibilidades: programas de educación ambiental, 
        investigación científica y ecoturismo responsable. El rancho se convirtió en un modelo de cómo la actividad 
        humana y la conservación pueden coexistir en armonía.
      </p>
    </div>
    
    <!-- Sección de legado -->
    <div class="legacy-section">
      <h2 class="legacy-title">Nuestro Legado Viviente</h2>
      <p class="story-text" style="color: white; text-align: center; font-size: 1.2rem;">
        Hoy, el Rancho Xamantún es más que una propiedad; es un testimonio vivo de que es posible construir 
        un futuro donde los seres humanos y la naturaleza prosperen juntos.
      </p>
      
      <div class="feature-grid">
        <div class="feature-card">
          <i class="bi bi-tree feature-icon"></i>
          <h4>150 Hectáreas</h4>
          <p>De ecosistemas protegidos y restaurados</p>
        </div>
        <div class="feature-card">
          <i class="bi bi-flower3 feature-icon"></i>
          <h4>30+ Especies</h4>
          <p>De fauna silvestre en conservación</p>
        </div>
        <div class="feature-card">
          <i class="bi bi-people feature-icon"></i>
          <h4>Comunidad</h4>
          <p>Programas de educación y conciencia ambiental</p>
        </div>
        <div class="feature-card">
          <i class="bi bi-award feature-icon"></i>
          <h4>Reconocimiento</h4>
          <p>UMA certificada por SEMARNAT</p>
        </div>
      </div>
    </div>
    
    <!-- Capítulo final -->
    <div class="chapter">
      <h2 class="chapter-title">Mirando al Futuro</h2>
      <p class="story-text">
        La historia del Rancho Xamantún continúa escribiéndose cada día. Cada planta que crece, cada animal que 
        encuentra refugio, cada visitante que se maravilla con la belleza natural de este lugar, añade una nueva 
        página a esta narrativa de esperanza y renovación.
      </p>
      <p class="story-text">
        Seguimos comprometidos con la visión original: ser guardianes responsables de este pedazo de paraíso 
        campechano, demostrando que el desarrollo y la conservación no son conceptos opuestos, sino complementarios 
        en la búsqueda de un mundo más equilibrado y sostenible.
      </p>
      
      <div class="text-center mt-4">
        <img src="../img2/rancho2.jpg" alt="Futuro del rancho" class="story-image" style="max-width: 600px;">
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