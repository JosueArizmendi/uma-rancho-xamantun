<?php
// Incluir archivo de conexión a la base de datos
include('../model/conexion_bd.php');
session_start();

// Lógica para crear un usuario
if (isset($_POST['create_user'])) {
    try {
        // Validar campos requeridos
        $requiredFields = [
            'nombre_s' => 'Nombre',
            'primer_apellido' => 'Primer apellido',
            'nom_usuario' => 'Nombre de usuario',
            'contraseña' => 'Contraseña'
        ];
        
        $missingFields = [];
        foreach ($requiredFields as $field => $name) {
            if (empty($_POST[$field])) {
                $missingFields[] = $name;
            }
        }
        
        if (!empty($missingFields)) {
            throw new Exception("Faltan campos obligatorios: " . implode(', ', $missingFields));
        }

        // Sanitizar datos
        $nombre = filter_input(INPUT_POST, "nombre_s", FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido1 = filter_input(INPUT_POST, "primer_apellido", FILTER_SANITIZE_SPECIAL_CHARS);
        $apellido2 = filter_input(INPUT_POST, "segundo_apellido", FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $usuario = filter_input(INPUT_POST, "nom_usuario", FILTER_SANITIZE_SPECIAL_CHARS);
        $rol = filter_input(INPUT_POST, "rol", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST,"contraseña", FILTER_SANITIZE_NUMBER_INT); // Encriptar contraseña
        
        // Verificar si el nombre de usuario ya existe
        $query = "SELECT id FROM usuarios WHERE nom_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$usuario]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("El nombre de usuario ya está registrado");
        }

        // Insertar nuevo usuario
        // Debe ser:
        $query = "INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, nom_usuario, contraseña, rol) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if ($stmt->execute([$nombre, $apellido1, $apellido2, $usuario, $password, $rol])) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'title' => 'Usuario creado',
                'message' => 'El usuario se ha registrado correctamente'
            ];
            header("Location: usuarios.php");
            exit;
        } else {
            throw new Exception("Error al crear el usuario en la base de datos");
        }

    } catch (PDOException $e) {
        error_log("Error en crear_usuario: " . $e->getMessage());
        $_SESSION['notification'] = [
            'type' => 'danger',
            'title' => 'Error de base de datos',
            'message' => 'Ocurrió un error al procesar la solicitud'
        ];
    } catch (Exception $e) {
        $_SESSION['notification'] = [
            'type' => 'warning',
            'title' => 'Error de validación',
            'message' => $e->getMessage()
        ];
    }
}

// Obtener datos del usuario actual
$usuario = $_SESSION['usuario'];
$query = "SELECT * FROM usuarios WHERE nom_usuario = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$usuario]);
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
  <title>Crear Usuario</title>

  <style>
      /* Estilos personalizados */
    .form-container {
      max-width: 300px; /* Redefinimos el tamaño máximo del formulario */
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
    /* Aseguramos que los botones se alineen correctamente */
    .btn-container {
      display: flex;
      justify-content: space-between; /* Los botones se alinean en ambos extremos */
      gap: 10px; /* Espacio entre los botones */
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

  <!-- Modal de éxito o error -->
  <div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="responseModalLabel">Resultado de la operación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="responseMessage"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  
        <div class="container">
        <div class="form-container">
          <form method="POST">
            <h4 class="text-center">Crear Nuevo Usuario</h4>

            <!-- Campos del formulario -->
            <div class="mb-3">
              <label for="nombre_s" class="form-label">Nombre</label>
              <input type="text" name="nombre_s" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="primer_apellido" class="form-label">Primer Apellido</label>
              <input type="text" name="primer_apellido" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
              <input type="text" name="segundo_apellido" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="nom_usuario" class="form-label">Nombre de Usuario</label>
              <input type="text" name="nom_usuario" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Tipo de Usuario</label>
                <select name="rol" class="form-control" required>
                    <option value="usuario">Usuario Normal</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <div class="mb-3">
              <label for="contraseña" class="form-label">Contraseña</label>
              <input type="password" name="contraseña" class="form-control" required>
            </div>

            <div class="btn-container">
              <button type="submit" name="create_user" class="btn btn-success" style="width: 210px;">Crear Usuario</button>
              <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>

  <br>
    <div style="padding-bottom:0.5cm" class="align-items-center">
        <div class="col text-center">
            <a href="usuarios.php" class="btn btn-success bi bi-arrow-return-left"></a>
        </div>
    </div>
    
    <br><br>

  <script>
    // Mostrar el modal con el mensaje según el resultado
    <?php if (isset($message)): ?>
      const modal = new bootstrap.Modal(document.getElementById('responseModal'));
      const responseMessage = document.getElementById('responseMessage');
      
      // Modificar el mensaje según el tipo
      responseMessage.innerHTML = "<?php echo $message; ?>";
      if ("<?php echo $messageType; ?>" === "error") {
        responseMessage.style.color = "red";
      } else {
        responseMessage.style.color = "green";
      }

      modal.show();
    <?php endif; ?>
  </script>
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
