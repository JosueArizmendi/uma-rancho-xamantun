<?php 
include('../model/conexion_bd.php');

session_start(); // Inicia la sesión

// Verifica si el usuario está logueado (si la sesión está activa)
if (!isset($_SESSION['usuario'])) {
    // Si no está logueado, redirige al login.php con el parámetro 'error'
    header("Location: ../login.php?error=session_expired");
    exit;
}
  
$usuario = $_SESSION['usuario'];
$query = "SELECT * FROM usuarios WHERE nom_usuario = :usuario";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC); // No veo que estés capturando el resultado

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
                    <!-- Botón Cerrar Sesión (rojo) -->
                    <a href="../logout.php" class="btn btn-danger text-white mx-2">Cerrar Sesión</a>
                    <!-- Botón Traducir a Maya (amarillo) -->
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
            <h4 class="text-center py-2">Formulario de Registro de Especie</h4>
            <div class="card card-default border:3 shadow p-3 mb-5">
                <form action="../model/alta_especie_flora.php" method="POST" class="needs-validation" novalidate>
                    <div class="row row-cols-3">
                        <div class="mb-3">
                            <label class="form-label" for="input_nombrecientifico">Nombre Científico</label>
                            <input type="text" class="form-control" id="input_nombrecientifico"
                                name="input_nombrecientifico" placeholder="Nombre Científico de la especie" required>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_nombrecomun">Nombre Común</label>
                            <input type="text" class="form-control" id="input_nombrecomun"
                                name="input_nombrecomun" placeholder="Nombre Científico de la especie" required>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_reino">Reino</label>
                            <select class="form-control" id="input_reino" name="input_reino" required>
                                <option value="">Selecciona un reino</option>
                                <option value="Animalia">Animalia</option>
                                <option value="Plantae">Plantae</option>
                                <option value="Fungi">Fungi</option>
                            </select>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Selecciona un reino</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_filo">Filo</label>
                            <select class="form-control" id="input_filo" name="input_filo" required>
                                <option value="">Selecciona un filo</option>
                                <option value="Magnoliophyta">Magnoliophyta</option>
                                <option value="Pinophyta">Pinophyta</option>
                                <option value="Pteridophyta">Pteridophyta</option>
                                <option value="Fanerógama">Fanerógama</option>
                                <option value="Angiosperms">Angiosperms</option>
                                <option value="Tracheophyta">Tracheophyta</option>
                            </select>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Selecciona un filo</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_clase">Clase</label>
                            <select class="form-control" id="input_clase" name="input_clase" required>
                                <option value="">Selecciona una clase</option>
                                <option value="Magnoliopsida">Magnoliopsida (Dicotiledóneas)</option>
                                <option value="Liliopsida">Liliopsida (Monocotiledóneas)</option>
                                <option value="Pteridopsida">Pteridopsida (Helechos)</option>
                                <option value="Eudicots">Eudicots</option>
                            </select>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Selecciona una clase</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_orden">Orden</label>
                            <select class="form-control" id="input_orden" name="input_orden" required>
                                <option value="">Selecciona un orden</option>
                                <option value="Rosales">Rosales</option>
                                <option value="Solanales">Solanales</option>
                                <option value="Poales">Poales</option>
                                <option value="Brassicales">Brassicales</option>
                                <option value="Sapindales">Sapindales</option>
                                <option value="Malpighiales">Malpighiales</option>
                                <option value="Fabales">Fabales</option>
                                <option value="Malvales">Malvales</option>
                                <option value="Ericales">Ericales</option>
                                <option value="Arecales">Arecales</option>
                            </select>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Selecciona un orden</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_familia">Familia</label>
                            <input type="text" class="form-control" id="input_familia"
                                name="input_familia" placeholder="Familia a la que pertenece" required>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_genero">Género</label>
                            <input type="text" class="form-control" id="input_genero"
                                name="input_genero" placeholder="Genero al que pertenece" required>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                            <div class="mb-3">
                                <label class="form-label" for="input_especie">Especie</label>
                                <input type="text" class="form-control" id="input_especie"
                                    name="input_especie" placeholder="Especie" required>
                                <div class="valid-feedback">Campo correcto!</div>
                                <div class="invalid-feedback">Rellena el campo correctamente</div>
                            </div>
                        </div>
                        <div class="row row-cols-2">
                            <div class="mb-3">
                                <label class="form-label" for="input_descripcion">Descripción Física</label>
                                <textarea type="text" class="form-control" name="input_descripcion" id="input_descripcion"
                                    placeholder="Descripción física de la especie" required></textarea>
                                <div class="valid-feedback">Campo correcto!</div>
                                <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_habitat">Hábitat</label>
                            <textarea type="text" class="form-control" id="input_habitat"
                                name="input_habitat" placeholder="Hábitat de la especie" required></textarea>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="input_usos">Usos Tradicionales y Medicinales</label>
                            <textarea type="text" class="form-control" id="input_usos"
                                name="input_usos" placeholder="Usos de la especie"></textarea>
                            <div class="valid-feedback">Campo correcto!</div>
                            <div class="invalid-feedback">Rellena el campo correctamente</div>
                        </div>
                        <div class="row row-cols-3">
                            <div class="mb-3">
                                <label class="form-label" for="input_conservacion">Estado de conservación</label>
                                <input type="text" class="form-control" id="input_conservacion"
                                    name="input_conservacion" placeholder="Estado de conservación de la especie" required>
                                <div class="valid-feedback">Campo correcto!</div>
                                <div class="invalid-feedback">Rellena el campo correctamente</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <button type="submit" class="btn btn-success" name="submit">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            <div style="padding-bottom:0.5cm" class="row w-100 align-items-center">
                <div class="col text-center">
                    <a href="especie_floraR.php" class="btn btn-success bi bi-arrow-return-left"></a>
                </div>
            </div>
        </div>
        <script src="../JS/bootstrap_validation.js"></script>
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
