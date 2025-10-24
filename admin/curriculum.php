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
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <title>Curriculum - Gerardo Avilés</title>

  <style>
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
        padding-top: 100px;
        padding-bottom: 100px;
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
    
    /* Estilos específicos para el currículum */
    .cv-header {
      background: linear-gradient(135deg, #1B396A 0%, #2c5282 100%);
      color: white;
      padding: 40px 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .cv-header::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: rgba(255,255,255,0.1);
      transform: rotate(45deg);
    }
    
    .profile-img-container {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      overflow: hidden;
      margin: 0 auto 20px;
      border: 5px solid white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      position: relative;
      z-index: 1;
    }
    
    .profile-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1B396A;
      font-size: 3rem;
    }
    
    .cv-name {
      font-size: 2.2rem;
      margin-bottom: 5px;
      font-weight: 700;
    }
    
    .cv-title {
      font-size: 1.4rem;
      margin-bottom: 15px;
      opacity: 0.9;
    }
    
    .cv-section {
      margin-bottom: 30px;
      padding: 20px;
      border-radius: 8px;
      background-color: #f8f9fa;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .section-title {
      color: #1B396A;
      border-bottom: 2px solid #1B396A;
      padding-bottom: 8px;
      margin-bottom: 15px;
      font-weight: 600;
    }
    
    .cv-item {
      margin-bottom: 15px;
    }
    
    .cv-item-title {
      font-weight: 600;
      color: #2c5282;
      margin-bottom: 5px;
    }
    
    .cv-item-meta {
      color: #6c757d;
      font-size: 0.9rem;
      margin-bottom: 5px;
    }
    
    .cv-skills {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    
    .skill-tag {
      background-color: #1B396A;
      color: white;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
    }
    
    .publication-item {
      margin-bottom: 10px;
      padding-left: 15px;
      border-left: 3px solid #1B396A;
    }
    
    @media (max-width: 768px) {
      .container {
        padding-top: 80px;
      }
      
      .cv-name {
        font-size: 1.8rem;
      }
      
      .cv-title {
        font-size: 1.2rem;
      }
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

  <div class="container">
    <div class="document-container" style="border: 2px solid #c1c4bf;">
      <h2 class="document-title">Curriculum Vitae</h2>
      
      <div class="cv-header">
        <div class="profile-img-container"><img src="../img2/gerardo.jpg" width="150px" height="170px">
          <div class="profile-img">
            <i class="bi bi-person-fill"></i>
          </div>
        </div>
        <h1 class="cv-name">Dr (C). Gerardo Alfonso Avilés Ramírez</h1>
        <h2 class="cv-title">Profesor Titular "C" Investigador Consultor Ambiental</h2>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Perfil Profesional</h3>
        <p>Especialista en Biología de la Conservación, Biología Marina y Ecología, con más de 25 años de experiencia en investigación científica, docencia superior y consultoría técnica en biodiversidad, impacto ambiental y gestión ecológica.</p>
        
        <div class="cv-skills">
          <span class="skill-tag">Biología de la Conservación</span>
          <span class="skill-tag">Biología Marina</span>
          <span class="skill-tag">Ecología</span>
          <span class="skill-tag">Gestión Ambiental</span>
          <span class="skill-tag">Consultoría Técnica</span>
        </div>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Experiencia Académica</h3>
        <div class="cv-item">
          <div class="cv-item-title">Instituto Tecnológico de Chiná</div>
          <div class="cv-item-meta">Desde 2008 - Secretaría de Educación Pública</div>
          <p>Impartición de más de 20 asignaturas, asesoría de tesis y coordinación de actividades académicas y de divulgación.</p>
        </div>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Proyectos de Investigación</h3>
        <div class="cv-item">
          <div class="cv-item-title">Participación en proyectos financiados</div>
          <p>CONACYT, CONABIO y TECNM, enfocados en:</p>
          <ul>
            <li>Arrecifes artificiales</li>
            <li>Sanidad acuícola</li>
            <li>Monitoreo de especies</li>
            <li>Restauración ecológica</li>
          </ul>
        </div>
        
        <div class="cv-item">
          <div class="cv-item-title">Registros oficiales</div>
          <p>Prestador de servicios ambientales ante SEMARNAT y responsable técnico en vida silvestre.</p>
          <p>Más de 100 estudios elaborados en materia de impacto ambiental, manejo forestal y conservación.</p>
        </div>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Publicaciones</h3>
        <div class="cv-item">
          <div class="publication-item">
            Autor de libros, capítulos y artículos científicos sobre biodiversidad, medicina tradicional y ecotecnologías.
          </div>
        </div>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Participación en Asociaciones</h3>
        <div class="cv-item">
          <p>Miembro activo de asociaciones científicas y consultivas:</p>
          <ul>
            <li>Ha presidido el Colegio de Biólogos de México en Campeche</li>
            <li>Ha representado al sector académico en congresos nacionales e internacionales</li>
          </ul>
        </div>
      </div>
      
      <div class="cv-section">
        <h3 class="section-title">Enfoque Profesional</h3>
        <p>Su enfoque interdisciplinario y compromiso institucional lo posicionan como referente en:</p>
        <div class="cv-skills">
          <span class="skill-tag">Educación ambiental</span>
          <span class="skill-tag">Bioética</span>
          <span class="skill-tag">Gestión de comités técnicos</span>
          <span class="skill-tag">Cumplimiento normativo</span>
        </div>
        <p class="mt-3">Especialmente en la región sureste de México.</p>
      </div>
    </div>
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