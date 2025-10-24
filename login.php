<?php include 'model/sesion.php' ;

// Verifica si hay un parámetro 'error' en la URL
if (isset($_GET['error']) && $_GET['error'] == 'session_expired') {
  echo "<div class='alert alert-warning'>Tu sesión ha expirado, por favor inicia sesión de nuevo.</div>";
}
?>;

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="CSS/custom-styles.css">
  <link rel="stylesheet" href="./CSS/style.css">
  <link rel='icon' type='image/png' href='https://app-0ebd6199-e8db-44c3-bb90-48aa593f5a75.cleverapps.io/logo2.ico' />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <title>Iniciar Sesión</title>
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
   /* Estilo para los modales personalizados */
   .modal-content {
      border-radius: 8px;
  }
  
  /* Estilo para los formularios (login y register) */
  .card {
    border: 2px solid #ccc;  /* Borde gris claro */
  }

 </style>
</head>

<body>
  <!-- Navbar -->
  <header>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <div class="me-3 ms-2">
        <img src="./img2/rancho.png" class="rounded" width="60" height="56">
      </div>
      <div class="container-fluid">
        <a class="navbar-brand fs-2 text-white" href="#">UMA del Rancho Xamantún</a>
        <div class="sign">
          <a href="#" class="btn btn-info ms-2" onclick="mostrarFormulario('login')">Iniciar Sesión</a>
          <a href="#" class="btn btn-success ms-2" onclick="mostrarFormulario('register')">Registrarse</a>
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
  <main class="fixed-top-offset mt-3">
    <div class="container">
      <!-- Formulario Iniciar Sesión -->
      <!-- Formulario Iniciar Sesión -->
      <div id="login-form" class="form-container d-none">
        <div class="card card-default border:3 shadow p-3 mb-5">
          <h4>Iniciar Sesión</h4>
          <img src="Login.jpg" class="user-image">
          <form method="POST" action="login.php">
            <div class="mb-2">
                <label for="nom_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control form-control-sm" id="nom_usuario" name="nom_usuario" placeholder="Ingresa tu Nombre Usuario">
            </div>
            <div class="mb-2">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" class="form-control form-control-sm" id="contraseña" name="contraseña" placeholder="Ingresa tu Contraseña">
            </div>
            <div class="mt-4">
            <center><button type="submit" class="btn btn-primary btn-lg py-1 px-2" name="login">Entrar</button></center>
            </div>
           </form>
          <!-- Enlace al formulario de registro -->
          <div class="mt-1 text-center">
            <p>¿No estás registrado? <a href="#" onclick="mostrarFormulario('register')">¡Regístrate aquí!</a></p>
          </div>
        </div>
     </div>


      <!-- Formulario Registrarse -->
      <!-- Formulario Registrarse -->
    <br>
    <div id="register-form" class="form-container d-none">
    <div class="card card-default border:3 shadow p-4 mb-6">
        <center><h4>Registrarse</h4></center>
        <form method="POST" action="login.php">
            <div class="mb-2">
                <label for="nombre_s" class="form-label">Nombre:</label>
                <input type="text" class="form-control form-control-sm" id="nombre_s" name="nombre_s" placeholder="Ingresa tu Nombre / S">
            </div>
            <div class="mb-2">
                <label for="primer_apellido" class="form-label">Primer Apellido:</label>
                <input type="text" class="form-control form-control-sm" id="primer_apellido" name="primer_apellido" placeholder="Ingresa tu Apellido Paterno">
            </div>
            <div class="mb-2">
                <label for="segundo_apellido" class="form-label">Segundo Apellido:</label>
                <input type="text" class="form-control form-control-sm" id="segundo_apellido" name="segundo_apellido" placeholder="Ingresa tu Apellido Materno">
            </div>
            <div class="mb-2">
                <label for="nom_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control form-control-sm" id="nom_usuario" name="nom_usuario" placeholder="Ingresa un Nombre de Usuario">
                <input type="text" class="form-control form-control-sm" id="nom_usuario" name="nom_usuario" placeholder="Ingresa un Nombre de Usuario o Email">
            </div>
            <div class="mb-2">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" class="form-control form-control-sm" id="contraseña" name="contraseña" placeholder="Ingresa una Contraseña">
            </div>
            <div class="mt-4">
            <center><button type="submit" class="btn btn-success btn-lg py-1 px-2" name="register">Registrarse</button></center>
            </div>
        </form>
        <!-- Enlace al formulario de login -->
        <div class="mt-1 text-center">
            <p>¿Ya tienes una cuenta? <a href="#" onclick="mostrarFormulario('login')">¡Entra aquí!</a></p>
        </div>
    </div>
  </div>

  </main>
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
   <!-- Modal para mostrar mensajes -->
   <?php if (isset($error_message)): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="errorModalLabel">Error</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?= $error_message ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php if (isset($success_message)): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="successModalLabel">Éxito</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?= $success_message ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mostrar el modal si hay un mensaje de error o éxito
    <?php if (isset($error_message)): ?>
      var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
      myModal.show();
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
      var myModal = new bootstrap.Modal(document.getElementById('successModal'));
      myModal.show();
    <?php endif; ?>

    // Función para mostrar formularios
    function mostrarFormulario(tipo) {
      document.getElementById('login-form').classList.add('d-none');
      document.getElementById('register-form').classList.add('d-none');
      
      if (tipo === 'login') {
        document.getElementById('login-form').classList.remove('d-none');
      } else if (tipo === 'register') {
        document.getElementById('register-form').classList.remove('d-none');
      }
    }
  </script>
  <br>
</body>
</html>

