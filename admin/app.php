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
  <title>Plan de Trabajo</title>

  <style>
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
      padding: 10px;
    }
    
    .document-title {
      text-align: center;
      margin-bottom: 20px;
      color: #1B396A ;
      font-weight: bold;
    }

    .container {
        padding-top: 50px;
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
    
    .modal-map {
      max-width: 95vw;
      width: 95%;
    }
    
    .modal-content-map {
      height: 85vh;
    }
    
    #map {
      height: 100%;
      width: 100%;
      z-index: 1;
    }
    
    .map-controls {
      position: absolute;
      top: 80px;
      right: 10px;
      z-index: 1000;
      background: white;
      padding: 10px;
      border-radius: 4px;
      box-shadow: 0 0 8px rgba(0,0,0,0.2);
      max-height: 70vh;
      overflow-y: auto;
      width: 200px;
    }
    
    .legend {
      position: absolute;
      bottom: 30px;
      left: 10px;
      z-index: 1000;
      background: white;
      padding: 10px;
      border-radius: 4px;
      box-shadow: 0 0 8px rgba(0,0,0,0.2);
      max-width: 200px;
    }
    
    .legend-item {
      display: flex;
      align-items: center;
      margin-bottom: 5px;
    }
    
    .legend-color {
      width: 20px;
      height: 20px;
      margin-right: 5px;
      display: inline-block;
      border-radius: 50%;
    }
    
    .image-popup img {
      max-width: 300px;
      max-height: 200px;
      border-radius: 8px;
      margin: 5px 0;
    }
    
    .info-popup {
      text-align: center;
      padding: 10px;
    }
    
    .info-popup button {
      margin-top: 10px;
    }
    
    .contour-line {
      stroke: #000;
      stroke-width: 2;
      fill: none;
    }
    
    .active-button {
      background-color: #007bff !important;
      color: white !important;
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

  <div class="container mt-5 pt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card" style="border: 2px solid #c1c4bf;  /* Borde gris claro */">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0 text-center">Sistema de Información Geográfica</h3>
          </div>
          <div class="card-body text-center">
            <p class="card-text">Explora las características geológicas y de suelos del Rancho Xamantún y el estado de Campeche. <strong>Haga clic en el Cuadro Rojo del Mapa</strong></p>
            <button type="button" class="btn btn-success btn-lg mt-3" data-bs-toggle="modal" data-bs-target="#mapModal">
              Abrir Mapa Interactivo
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para el mapa -->
  <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-map">
      <div class="modal-content modal-content-map">
        <div class="modal-header">
          <h5 class="modal-title" id="mapModalLabel">Mapa Interactivo - Rancho Xamantún</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div id="map"></div>
          <div class="map-controls">
            <div class="btn-group-vertical" role="group" aria-label="Controles del mapa">
              <button type="button" class="btn btn-outline-primary mb-2" id="btnRocas">Tipos de Rocas</button>
              <button type="button" class="btn btn-outline-success mb-2" id="btnSubprovincia">Subprovincia Fisiográfica</button>
              <button type="button" class="btn btn-outline-secondary mb-2" id="btnSuelos">Tipos de Suelo</button>
              <button type="button" class="btn btn-outline-info mb-2" id="btnVegetacion">Vegetación</button>
              <button type="button" class="btn btn-outline-danger mb-2" id="btnHidrologia">Hidrología</button>
              <button type="button" class="btn btn-outline-warning mb-2" id="btnRelieve">Relieve</button>
              <button type="button" class="btn btn-outline-dark" id="btnReset">Reiniciar Vista</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para mostrar información detallada -->
  <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="infoModalLabel">Información del Rancho Xamantún</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="modal-content">
            <!-- Contenido dinámico se cargará aquí -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
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

  <script>
    // Inicializar el mapa cuando el modal se muestra
    var map, drawnItems;
    var ranchoPolygon;
    var activeButton = null;
    
    // Coordenadas exactas del Rancho Xamantún (convertidas a decimal)
    var coordenadasRancho = [
      [19.7279611, -90.4283222], // Punto A: 19°43'40.66"N 90°25'41.96"W
      [19.7279972, -90.4030972], // Punto B: 19°43'40.79"N 90°24'11.15"W  
      [19.7063278, -90.4031750], // Punto C: 19°42'22.78"N 90°24'11.43"W
      [19.7063972, -90.4284056]  // Punto D: 19°42'23.03"N 90°25'42.26"W
    ];
    
    // Coordenadas para las curvas de nivel exactas como en la imagen
    var contourLines = [
      // Línea central
      [
        
      ],
    ];
    
    // Datos para cada categoría
    var mapData = {
      "rocas": {
        "title": "Tipos de Rocas - Rancho Xamantún",
        "image": "../mapa/rocas.jpg",
        "description": "El Rancho Xamantún se caracteriza por la presencia de rocas sedimentarias, principalmente calizas, típicas de la península de Yucatán. Estas formaciones geológicas son el resultado de procesos de sedimentación marina que ocurrieron hace millones de años."
      },
      "subprovincia": {
        "title": "Subprovincia Fisiográfica",
        "image": "../mapa/subprovicnia fisiografica.jpg",
        "description": "La zona pertenece a la subprovincia fisiográfica de llanuras y lomeríos, caracterizada por terrenos planos con colinas suaves. Esta configuración topográfica influye en el drenaje y la distribución de la vegetación en el rancho."
      },
      "suelos": {
        "title": "Tipos de Suelo",
        "image": "../img2/tipo_suelo.png",
        "description": "Los suelos del Rancho Xamantún presentan características particulares que determinan su capacidad productiva y la distribución de la vegetación natural. Se observan diferentes tipos de suelo según la topografía y la composición geológica."
      },
      "vegetacion": {
        "title": "Vegetación",
        "image": "../img2/vegetacion.png",
        "description": "La vegetación en el Rancho Xamantún es diversa, incluyendo especies características de la región. Se pueden observar diferentes comunidades vegetales que varían según las condiciones del suelo, la topografía y la disponibilidad de agua."
      },
      "hidrologia": {
        "title": "Hidrología",
        "image": "../img2/hidrologia.png",
        "description": "El sistema hidrológico del rancho está influenciado por la geología kárstica de la región, con presencia de cenotes y sistemas de agua subterránea. El patrón de drenaje superficial es limitado debido a la alta permeabilidad del suelo."
      },
      "relieve": {
        "title": "Relieve - Curvas de Nivel",
        "image": "../mapa/curvas de nivel (topografico).jpg",
        "description": "El mapa muestra las curvas de nivel del Rancho Xamantún, que representan las variaciones en la elevación del terreno. Estas líneas conectan puntos de igual altitud y permiten visualizar la topografía del área. Las curvas más cercanas indican pendientes más pronunciadas, mientras que las más separadas representan terrenos más planos."
      }
    };
    
    document.getElementById('mapModal').addEventListener('shown.bs.modal', function() {
      // Inicializar el mapa
      map = L.map('map').setView([19.723, -90.415], 13);
      
      // Añadir capa base de OpenStreetMap
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
      
      // Añadir polígono exacto del Rancho Xamantún
      ranchoPolygon = L.polygon(coordenadasRancho, {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.2,
        weight: 3
      }).addTo(map).bindPopup(`
        <div class="info-popup">
          <strong>Rancho Xamantún</strong><br>
          Polígono exacto del terreno<br>
          <strong>¡HAZ CLIC AQUÍ PARA VER INFORMACIÓN DETALLADA!</strong><br>
          <button class="btn btn-sm btn-primary mt-2" onclick="showInfo()">Ver Información</button>
        </div>
      `);
      
      // Añadir curvas de nivel (líneas negras) exactas
      contourLines.forEach(function(contour, index) {
        L.polyline(contour, {
          color: 'black',
          weight: 1.5,
          opacity: 0.9,
          smoothFactor: 1
        }).addTo(map);
      });
      
      // Añadir puntos de referencia como en la imagen
      var referencePoints = [
        {lat: 19.720, lng: -90.416, name: "Xamantún"},
      ];
      
      referencePoints.forEach(function(point) {
        L.marker([point.lat, point.lng])
          .addTo(map)
          .bindPopup("<strong>" + point.name + "</strong>");
      });
      
      // Centrar el mapa en el polígono
      map.fitBounds(ranchoPolygon.getBounds());
      
      // Ajustar el tamaño del mapa después de que se muestre el modal
      setTimeout(function() {
        map.invalidateSize();
      }, 100);
    });
    
    // Función para mostrar información
    function showInfo() {
      if (!activeButton) {
        // Si no hay botón activo, mostrar información general
        showGeneralInfo();
        return;
      }
      
      var data = mapData[activeButton];
      if (data) {
        var modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = `
          <h4>${data.title}</h4>
          <div class="row mt-3">
            <div class="col-md-6">
              <img src="${data.image}" class="img-fluid rounded" alt="${data.title}">
            </div>
            <div class="col-md-6">
              <p>${data.description}</p>
            </div>
          </div>
        `;
        
        var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
        infoModal.show();
      }
    }
    
    // Función para mostrar información general
    function showGeneralInfo() {
      var modalContent = document.getElementById('modal-content');
      modalContent.innerHTML = `
        <h4>Rancho Xamantún - Información General</h4>
        <div class="row mt-3">
          <div class="col-md-6">
            <img src="../img2/rancho.png" class="img-fluid rounded" alt="Rancho Xamantún">
          </div>
          <div class="col-md-6">
            <p>El Rancho Xamantún es una Unidad de Manejo Ambiental ubicada en el estado de Campeche, México. Se dedica a la conservación de la biodiversidad y al manejo sostenible de los recursos naturales.</p>
            <p><strong>Selecciona una categoría específica en los botones para ver información detallada.</strong></p>
          </div>
        </div>
      `;
      
      var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
      infoModal.show();
    }
    
    // Función para activar un botón
    function activateButton(buttonId) {
      // Remover clase activa de todos los botones
      document.querySelectorAll('.map-controls .btn').forEach(function(btn) {
        btn.classList.remove('active-button');
      });
      
      // Activar el botón seleccionado
      var button = document.getElementById(buttonId);
      button.classList.add('active-button');
      
      // Guardar el botón activo
      activeButton = buttonId.replace('btn', '').toLowerCase();
    }
    
    // Event listeners para los botones
    document.getElementById('btnRocas').addEventListener('click', function() {
      activateButton('btnRocas');
    });
    
    document.getElementById('btnSubprovincia').addEventListener('click', function() {
      activateButton('btnSubprovincia');
    });
    
    document.getElementById('btnSuelos').addEventListener('click', function() {
      activateButton('btnSuelos');
    });
    
    document.getElementById('btnVegetacion').addEventListener('click', function() {
      activateButton('btnVegetacion');
    });
    
    document.getElementById('btnHidrologia').addEventListener('click', function() {
      activateButton('btnHidrologia');
    });
    
    document.getElementById('btnRelieve').addEventListener('click', function() {
      activateButton('btnRelieve');
    });
    
    document.getElementById('btnReset').addEventListener('click', function() {
      // Desactivar todos los botones
      document.querySelectorAll('.map-controls .btn').forEach(function(btn) {
        btn.classList.remove('active-button');
      });
      activeButton = null;
      
      // Volver a la vista inicial (polígono del rancho)
      map.fitBounds(ranchoPolygon.getBounds());
    });
  </script>

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