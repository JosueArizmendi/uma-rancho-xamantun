<?php
// Incluir el archivo de conexión
session_start(); // Inicia la sesión


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

if (isset($_SESSION['notification'])) {
    $noti = $_SESSION['notification'];
    echo "<div class='alert alert-{$noti['type']}'>
            <strong>{$noti['title']}:</strong> {$noti['message']}
          </div>";
    unset($_SESSION['notification']); // Eliminar después de mostrarla
}

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

// Verificar si hay un término de búsqueda
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Construir la consulta de búsqueda
// Consulta inicial: solo usuarios activos
$query = "SELECT id, nombre, primer_apellido, segundo_apellido, nom_usuario, contraseña, rol FROM usuarios WHERE activo = 1";

// Si hay un término de búsqueda, agregar cláusula WHERE adicional
if ($search) {
    $query .= " AND (nombre LIKE :search OR primer_apellido LIKE :search OR segundo_apellido LIKE :search OR nom_usuario LIKE :search)";
}


// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query);

// Si hay término de búsqueda, enlazamos el parámetro
if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

$stmt->execute();

// Obtener todos los resultados
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica si hay usuarios en la base de datos
if (empty($usuarios)) {
    echo "No se encontraron usuarios.";
}
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
    <title>Lista de Usuarios</title>
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
       left: 0; /* Lo coloca a la derecha */
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

    .card {
    border: 1px solid #ccc;  /* Borde gris claro */
    }

    /* Posiciona el toast en la parte inferior derecha con margen */
    .toast-container {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        z-index: 1055; /* Asegura que se vea encima de otros elementos */
    }

    /* Opcional: modifica el tamaño o aspecto */
    .toast {
        min-width: 300px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        margin-top: 75px;
    }

    .toast-body {
        padding: 1rem;
    }

  </style>
</head>

<body>
 <!-- Barra de navegación -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
            <div class="me-3 ms-2">
                <img src="../img2/rancho.png" class="rounded" width="60" height="56">
            </div>
            <div class="container-fluid">
                <a class="navbar-brand fs-3 text-white" href="template.php">Inicio</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasNavbar">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Opciones</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
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
                                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" data-bs-toggle="dropdown">Fauna</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="especies_fauna.php">Especies</a></li>
                                    <li><a class="dropdown-item" href="avistamientos_fauna.php">Avistamientos</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" data-bs-toggle="dropdown">Flora</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="especies_flora.php">Especies</a></li>
                                    <li><a class="dropdown-item" href="avistamientos_flora.php">Avistamientos</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" data-bs-toggle="dropdown">Administración</a>
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
                            <!-- Perfil de usuario -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" data-bs-toggle="dropdown">
                                    <img src="<?= !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg' ?>" alt="Perfil" class="rounded-circle" width="30" height="30">
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
                                    <li><a class="dropdown-item" href="acerca2_de.php">Acerca de</a></li>
                                    <li><a class="dropdown-item" href="comentarios_blogR.php">Opiniones</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Botón de cerrar sesión -->
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
        <h4 class="text-center py-2">Listado de Usuarios</h4>

        <div class="card card-default border:3 shadow p-3 mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <a class="btn btn-success shadow-sm" href="crear_usuario.php">Nuevo Usuario</a>

                <form method="GET" action="" class="d-flex w-auto">
                    <!-- Lista desplegable para buscar por nombre de usuario -->
                    <select name="search" class="form-control form-control-sm custom-select" onchange="this.form.submit()" style="height: 40px;">
                        <option value="" disabled selected>Buscar usuario...</option>
                        <option value="">Inicio</option> <!-- Opción para mostrar todos los usuarios -->
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario['nom_usuario']); ?>" <?= isset($_GET['search']) && $_GET['search'] == $usuario['nom_usuario'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($usuario['nom_usuario']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn btn-primary btn-sm ms-2" style="height: 40px;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped text-center align-middle">
                    <thead>
                    <tr>
                        <th hidden scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Primer Apellido</th>
                        <th scope="col">Segundo Apellido</th>
                        <th scope="col">Nombre de Usuario</th>
                        <th scope="col">Contraseña</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td hidden><?= htmlspecialchars($usuario['id']); ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?= htmlspecialchars($usuario['primer_apellido']); ?></td>
                            <td><?= htmlspecialchars($usuario['segundo_apellido']); ?></td>
                            <td><?= htmlspecialchars($usuario['nom_usuario']); ?></td>
                            <td><?= htmlspecialchars(strlen($usuario['contraseña']) > 0 ? "****" : ""); ?></td>
                            <td><?= htmlspecialchars($usuario['rol']); ?></td>
                            <td>
                            <a class="bi bi-pencil-square btn btn-sm btn-primary" href="editar_usuario.php?id=<?= base64_encode($usuario['id']) ?>"> Editar</a>
                            <a class="bi bi-trash3-fill btn btn-sm btn-danger"
                              href="eliminar_usuario.php?id=<?= base64_encode($usuario['id']) ?>"
                              onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');"> Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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
<?php if ($showNotification): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('notificationToast');
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    });
</script>
<?php endif; ?>
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
