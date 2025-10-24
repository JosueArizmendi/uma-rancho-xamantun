<?php 
include '../model/leer_datos_blog.php';
       '../model/borrar_blog.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}
  
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
    <title>Administración de Blogs</title>
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
       position: fixed !important;
       top: 0;
       right: 0;
       margin-top: 80px;
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
        border: 1px solid #ccc;
    }
    
    .imagen-blog {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
    }
    
    .texto-corto {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
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

                <!-- Contenedor para los botones -->
                <div class="d-flex align-items-center">
                    <!-- Botón Cerrar Sesión -->
                    <div class="d-flex">
                        <a href="../logout.php" class="btn btn-danger ms-2">Cerrar sesión</a>
                    </div>
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

    <br>

    <main class="fixed-top-offset">
        <div class="container">
            <h4 class="text-center py-2">Administración de Blogs</h4>

            <!-- Sección de Nuevo Registro y Buscador -->
            <div class="card card-default border:3 shadow p-3 mb-5">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Botón de Nuevo Registro -->
                    <div class="form-group">
                        <a class="btn btn-success shadow-sm" href="blogC.php">Nuevo Blog</a>
                    </div>

                    <!-- Formulario de búsqueda -->
                    <form method="GET" action="" class="d-flex w-auto">
                        <input type="text" name="search" class="form-control form-control-sm" style="height: 30px;" 
                               placeholder="Buscar blog..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                        <button type="submit" class="btn btn-primary btn-sm ms-2" style="height: 30px;">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Mensajes de éxito/error -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success mt-3">
                        <?php 
                        switch($_GET['success']) {
                            case 'blog_created': echo 'Blog creado exitosamente'; break;
                            case 'blog_updated': echo 'Blog actualizado exitosamente'; break;
                            case 'blog_deleted': echo 'Blog eliminado exitosamente'; break;
                        }
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger mt-3">
                        <?php 
                        switch($_GET['error']) {
                            case 'create_failed': echo 'Error al crear el blog'; break;
                            case 'update_failed': echo 'Error al actualizar el blog'; break;
                            case 'delete_failed': echo 'Error al eliminar el blog'; break;
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="row w-100 align-items-center table-responsive-md justify-content-center">
                    <div class="col text-center table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Título</th>
                                    <th scope="col">Autor</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Artículo</th>
                                    <th scope="col">Primer Imagen</th>
                                    <th scope="col">Segunda Imagen</th>
                                    <th scope="col">Tercera Imagen</th>
                                    <th scope="col">Editar</th>
                                    <th scope="col">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Filtrar blogs si hay búsqueda
                                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                                $filteredBlogs = $blogs;
                                
                                if ($searchTerm) {
                                    $filteredBlogs = array_filter($blogs, function ($blog) use ($searchTerm) {
                                        return stripos($blog['titulo'], $searchTerm) !== false || 
                                               stripos($blog['descripcion'], $searchTerm) !== false ||
                                               stripos($blog['nombre_autor'], $searchTerm) !== false;
                                    });
                                }
                                
                                foreach ($filteredBlogs as $blog): 
                                    $fecha = date('d/m/Y', strtotime($blog['fecha_publicacion']));
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($blog['titulo']) ?></td>
                                        <td><?= htmlspecialchars($blog['nombre_autor']) ?></td>
                                        <td><?= $fecha ?></td>
                                        <td><?= strlen($blog['descripcion']) > 10 ? substr($blog['descripcion'], 0, 10) . '...' : $blog['descripcion']; ?></td>
                                        <td>
                                            <?php if ($blog['imagen1']): ?>
                                                <img src="<?= $blog['imagen1'] ?>" class="imagen-blog" alt="Imagen 1">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($blog['imagen2']): ?>
                                                <img src="<?= $blog['imagen2'] ?>" class="imagen-blog" alt="Imagen 2">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($blog['imagen3']): ?>
                                                <img src="<?= $blog['imagen3'] ?>" class="imagen-blog" alt="Imagen 3">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a class="bi bi-pencil-square btn btn-primary shadow-sm"
                                            href="blogU.php?id_blog=<?php echo codificar($blog['id_blog']) ?>"></a>
                                        </td>
                                        <td>
                                            <a class="bi bi-trash3-fill btn btn-danger shadow-sm"
                                                href="../model/borrar_blog.php?id_blog=<?php echo codificar($blog['id_blog']) ?>"
                                                onclick="return confirm('¿Estás seguro de eliminar este blog?')"></a>
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
    <br><br><br>
</body>
</html>