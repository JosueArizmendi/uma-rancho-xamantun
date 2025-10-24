<?php include '../model/leer_avistamientos_fauna.php';

session_start(); // Inicia la sesión
// Verifica si el usuario está logueado (si la sesión está activa)
if (!isset($_SESSION['usuario'])) {
  // Si no está logueado, redirige al login.php con el parámetro 'error'
  header("Location: ../login.php?error=session_expired");
  exit;
}

// Obtener datos del usuario
$usuario = $_SESSION['usuario'];
$query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $usuario, PDO::PARAM_STR);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>;
<!DOCTYPE html>
<html lang="en">

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
  #searchResults {
    list-style-type: none;
    margin: 0;
    padding: 0;
    position: absolute;
    background-color: white;
    width: 100%;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    display: none;
    top: 100%; /* Esto asegura que la lista se coloque justo debajo del input */
    left: 0;
  }

  #searchResults li {
    padding: 10px;
    cursor: pointer;
    width: 110%;
  }
  
  #searchResults li:hover {
    background-color: #f0f0f0;
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

  .card {
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
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"   qdata-bs-target="#offcanvasNavbar"
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
              <!-- Perfil de usuario con imagen -->
              <li class="nav-item dropdown">
                <!-- Este es el cambio en el formulario de búsqueda -->
                <form method="GET" action="avistamientos_fauna.php" class="d-flex ms-1 align-items-center" style="max-width: 250px;">
                  <div class="input-group">
                    <input type="text" name="search" id="searchInput" class="form-control form-control-sm-2" placeholder="Buscar Avistamiento" 
                      style="height: 31px;" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" onkeyup="searchAvistamientos()">
                    <button type="submit" class="btn btn-primary btn-sm ms" style="height: 31px;">
                      <i class="bi bi-search"></i>
                    </button>
                  </div>
                  <ul id="searchResults" class="list-group mt-1"></ul> <!-- Aquí se mostrará la lista -->
                </form>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Aquí agregamos los botones en la parte superior, justo después de la navbar -->
        <div class="d-flex align-items-center">
          <!-- Botón Cerrar Sesión -->
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
  <script>
  // Array con los avistamientos
  const avistamientos = [
    <?php foreach ($avistamientos as $avistamiento): ?>,
      {
        "nombre_cientifico": "<?= $avistamiento['nombre_cientifico']; ?>",
        "imagen": "<?= $avistamiento['ruta_imagen']; ?>",
        "id_avistamiento": "<?= $avistamiento['id_avistamiento']; ?>"
        //codificarlo cuando se haya codificado
      },
    <?php endforeach; ?>
  ];

  // Aquí es donde define la función
  function searchAvistamientos() {
    let input = document.getElementById('searchInput').value.toLowerCase();
    let resultsContainer = document.getElementById('searchResults');
    resultsContainer.innerHTML = ''; // Limpiar los resultados anteriores

    if (input.length > 0) {
      let filteredResults = avistamientos.filter(avistamientoItem =>
        avistamientoItem.nombre_cientifico.toLowerCase().includes(input)
      );
      
      if (filteredResults.length > 0) {
        resultsContainer.style.display = 'block';
        filteredResults.forEach(result => {
          let listItem = document.createElement('li');
          listItem.className = 'list-group-item d-flex align-items-center';
          listItem.innerHTML = `
            <img src="../${result.imagen}" alt="${result.nombre_cientifico}" style="max-width: 70px; height: 50px;" class="me-3">
            <strong><i>${result.nombre_cientifico}</i></strong>
          `;
          listItem.onclick = function() {
            // Redirige a la página con el avistamiento seleccionado
            window.location.href = 'avistamientos_fauna.php?search=' + encodeURIComponent(result.nombre_cientifico);
          };
          resultsContainer.appendChild(listItem);
        });
      } else {
        resultsContainer.style.display = 'none'; // No mostrar resultados si no hay coincidencias
      }
    } else {
      resultsContainer.style.display = 'none'; // No mostrar resultados si el campo está vacío
    }
  }

  // Cierra la lista de resultados cuando el usuario haga clic fuera del input
  document.addEventListener('click', function(e) {
    if (!document.getElementById('searchInput').contains(e.target)) {
      document.getElementById('searchResults').style.display = 'none';
    }
  });
</script>
  <main class="fixed-top-offset">
    <div class="container">
      <h4 class="text-center py-3">Listado de Avistamientos de Fauna</h4>
      <div class="row row-cols-5 grid gap-2">
        <?php
        // Filtrar los avistamientos si hay un término de búsqueda
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
        $filteredAvistamientos = $avistamientos;

        if ($searchTerm) {
          $filteredAvistamientos = array_filter($avistamientos, function ($avistamiento) use ($searchTerm) {
            return stripos($avistamiento['nombre_cientifico'], $searchTerm) !== false ||
                   stripos($avistamiento['descripcion'], $searchTerm) !== false;
          });
        }
        foreach ($filteredAvistamientos as $avistamiento): ?>
          <div style="width: 16rem;" class="card card-default border:3 p-3 mb-5">
           <img src="<?= '../' . $avistamiento['ruta_imagen']; ?>" class="card-img-top" style="max-width: 220px; height: 160px;">
            <div class="card-body">
              <h5 class="card-title"><i><strong><?= $avistamiento['nombre_cientifico']; ?></strong></i></h5>
              <p style="max-width: 190px; height: 50px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" class="card-text">
                  <?= strlen($avistamiento['descripcion']) > 24 ? substr($avistamiento['descripcion'], 0, 24) . '...' : $avistamiento['descripcion']; ?>
              </p>
              <p class="card-text"><?= $avistamiento['fecha_avistamiento']; ?></p>
              <?php
                // Codificar el ID del avistamiento en Base64
                $encoded_id = base64_encode($avistamiento['id_avistamiento']);
              ?>
              <a href="avistamiento_fauna_ficha.php?id_avistamiento=<?= $encoded_id ?>" class="btn btn-primary">Más información</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>
  <br>
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
