<?php include '../model/leer_avistamientos_flora.php';

session_start(); // Inicia la sesión
// Verifica si el usuario está logueado (si la sesión está activa)
if (!isset($_SESSION['usuario'])) {
  // Si no está logueado, redirige al login.php con el parámetro 'error'
  header("Location: ../login.php?error=session_expired");
  exit;
}

// Mostrar notificaciones
$showNotification = false;
$notificationType = '';
$notificationTitle = '';
$notificationMessage = '';

if (isset($_SESSION['notification'])) {
    $showNotification = true;
    $notificationType = $_SESSION['notification']['type'];
    $notificationTitle = $_SESSION['notification']['title'];
    $notificationMessage = $_SESSION['notification']['message'];
    unset($_SESSION['notification']);
}

// Obtener datos del usuario
$usuario = $_SESSION['usuario'];
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
   /* Estilo para el select del formulario de búsqueda */
   .custom-select {
    color: #000; /* Texto en negro */
    border-radius: 5px; /* Bordes redondeados */
    padding: 8px; /* Espaciado dentro del select */
    width: auto; /* Ajustar el ancho */
    height: 30px; /* Altura consistente */
  }

  /* Estilo para el botón de búsqueda */
  form button {
    height: 35px;
  }

  .card {
    border: 1px solid #ccc;  /* Borde gris claro */
  }

  /* Estilos para la barra de progreso */
  .barra-progreso {
      width: 100%;
      height: 20px;
      background-color: #e0e0e0;
      border-radius: 10px;
      margin-top: 10px;
  }

  .barra-progreso-interior {
      width: 0%;
      height: 100%;
      background-color: #007bff;
      border-radius: 10px;
      transition: width 2s;
  }

  .toast {
    min-width: 300px;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.3s ease-in-out;
    margin-top: 75px;
  }

  .toast-body {
      padding: 1rem;
  }

  @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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
                <a class="nav-link text-dark fs-3" href="blogs.php">Blogs</a>
              </li>
              <?php 
                // Asegúrate de que $id_usuario_actual esté disponible
                if (isset($user_data['id'])) {
                    $id_usuario_actual = $user_data['id'];
                    include('../includes/notificaciones.php');
                }
              ?>
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

        <!-- Botones de Cerrar sesión y Traducir a Maya en el encabezado -->
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
    <div class="container">
      <h4 class="text-center py-1">Listado de Avistamientos de Flora</h4>

      <!-- Sección de Nuevo Registro y Buscador -->
      <div class="card card-default border:3 shadow p-3 mb-5">
        <div class="d-flex justify-content-between align-items-center">
          <!-- Botón de Nuevo Registro -->
          <div class="form-group">
            <a class="btn btn-success shadow-sm" href="avistamiento_floraC.php">Nuevo avistamiento</a>
          </div>

          <!-- Formulario de Búsqueda dentro del mismo contenedor -->
          <!-- Primer Formulario de Búsqueda -->
          <form method="GET" action="" class="d-flex w-auto">
          <!-- Lista desplegable de especies avistadas -->
          <select name="search" class="form-control form-control-sm custom-select"  onchange="this.form.submit()" style="height: 40px;">
            <option value="" disabled selected>Buscar especie avistada...</option>
            <option style="font-size:16px; background-color: #caffef"  value="">Inicio</option> <!-- Opción para regresar a la lista completa -->
            <?php
            // Aquí se llenan las opciones con las especies avistadas
            $especies = array_unique(array_column($avistamientos, 'nombre_cientifico')); // Obtener un array de especies únicas
            foreach ($especies as $especie): ?>
              <option style="font-size:16px; background-color: #caffef"  value="<?= $especie; ?>" <?= isset($_GET['search']) && $_GET['search'] == $especie ? 'selected' : ''; ?>>
                <?= $especie; ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="btn btn-primary btn-sm ms-2" style="height: 40px;">
            <i class="bi bi-search"></i>
          </button>
        </form>
       </div>

        <!-- Tabla de avistamientos -->
        <div class="row w-100 align-items-center table-responsive-md justify-content-center">
          <div class="col text-center table-responsive">
            <table class="table table-striped text-center align-middle">
              <thead>
                <tr>
                  <th hidden scope="col">ID</th>
                  <th scope="col">Especie avistada</th>
                  <th scope="col">Fecha de avistamiento</th>
                  <th scope="col">Latitud</th>
                  <th scope="col">Longitud</th>
                  <th scope="col">Descripción</th>
                  <th scope="col">Fotografía</th>
                  <th scope="col">Descargar PDF</th> <!-- Nueva columna -->
                </tr>
              </thead>
              <tbody>
                <?php 
                // Obtener el término de búsqueda (especie seleccionada)
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                // Filtrar los avistamientos si se seleccionó una especie
                $filteredAvistamientos = $avistamientos;
                if ($searchTerm) {
                  $filteredAvistamientos = array_filter($avistamientos, function ($avistamiento) use ($searchTerm) {
                    return stripos($avistamiento['nombre_cientifico'], $searchTerm) !== false;
                  });
                }
                foreach ($filteredAvistamientos as $avistamiento): ?>
                  <tr>
                    <td hidden><?= $avistamiento['id_avistamiento']; ?></td>
                    <td><?= $avistamiento['nombre_cientifico']; ?></td>
                    <td><?= $avistamiento['fecha_avistamiento']; ?></td>
                    <td><?= $avistamiento['latitud']; ?></td>
                    <td><?= $avistamiento['longitud']; ?></td>
                    <td><?= strlen($avistamiento['descripcion']) > 10 ? substr($avistamiento['descripcion'], 0, 10) . '...' : $avistamiento['descripcion']; ?>
                    </td>
                    <td><img src="<?= '../' . $avistamiento['ruta_imagen']; ?>" style="max-width: 100px; height: auto;"></td>
                    <td>
                        <a class="btn btn-success shadow-sm" href="../model/generar_pdf_avistamiento2.php?id_avistamiento=<?= base64_encode($avistamiento['id_avistamiento']); ?>" onclick="mostrarBarraProgreso()">
                          <i class="bi bi-file-earmark-pdf"></i>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

    <!-- Notificación Toast -->
  <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
      <div id="notificationToast" class="toast align-items-center text-white bg-<?= $notificationType ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
              <div class="toast-body">
                  <strong><?= $notificationTitle ?></strong><br>
                  <?= $notificationMessage ?>
              </div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
      </div>
  </div>

  <script>
  function mostrarBarraProgreso() {
      // Crear el contenedor de la ventana emergente
      var ventana = document.createElement('div');
      ventana.style.position = 'fixed';
      ventana.style.top = '50%';
      ventana.style.left = '50%';
      ventana.style.transform = 'translate(-50%, -50%)';
      ventana.style.backgroundColor = 'white';
      ventana.style.padding = '20px';
      ventana.style.border = '1px solid #ccc';
      ventana.style.borderRadius = '10px';
      ventana.style.boxShadow = '0 0 10px rgba(0, 0, 0, 0.1)';
      ventana.style.zIndex = '1000';

      // Crear el mensaje de "Descargando PDF"
      var mensaje = document.createElement('p');
      mensaje.textContent = 'Descargando PDF...';
      mensaje.style.textAlign = 'center';

      // Crear la barra de progreso
      var barraProgreso = document.createElement('div');
      barraProgreso.style.width = '100%';
      barraProgreso.style.height = '20px';
      barraProgreso.style.backgroundColor = '#e0e0e0';
      barraProgreso.style.borderRadius = '10px';
      barraProgreso.style.marginTop = '10px';

      var barraProgresoInterior = document.createElement('div');
      barraProgresoInterior.style.width = '0%';
      barraProgresoInterior.style.height = '100%';
      barraProgresoInterior.style.backgroundColor = '#007bff';
      barraProgresoInterior.style.borderRadius = '10px';
      barraProgresoInterior.style.transition = 'width 2s';

      barraProgreso.appendChild(barraProgresoInterior);
      ventana.appendChild(mensaje);
      ventana.appendChild(barraProgreso);

      // Agregar la ventana emergente al cuerpo del documento
      document.body.appendChild(ventana);

      // Simular la progresión de la barra de progreso
      setTimeout(function() {
          barraProgresoInterior.style.width = '100%';
      }, 100);

      // Ocultar la ventana emergente después de un tiempo
      setTimeout(function() {
          document.body.removeChild(ventana);
      }, 9000); // Ajusta el tiempo según sea necesario
    }
  </script>
  <?php if ($showNotification): ?>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          var toastEl = document.getElementById('notificationToast');
          var toast = new bootstrap.Toast(toastEl, {
              delay: 5000 // 5 segundos
          });
          toast.show();
      });
  </script>
  <?php endif; ?>
  <br><br><br>
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
