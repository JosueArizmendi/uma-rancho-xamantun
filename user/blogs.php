<?php 
include '../model/leer_datos_blog.php';

session_start(); // Inicia la sesión
// Verifica si el usuario está logueado (si la sesión está activa)
if (!isset($_SESSION['usuario'])) {
  // Si no está logueado, redirige al login.php con el parámetro 'error'
  header("Location: ../login.php?error=session_expired");
  exit;
}
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <title>Blogs - UMA Rancho Xamantún</title>
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
    
    /* Estilos para las tarjetas de blog */
    .blog-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    }
    
    .blog-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .blog-card-img {
      height: 200px;
      object-fit: cover;
      width: 100%;
    }
    
    .blog-card-body {
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }
    
    .blog-card-btn {
      margin-top: auto;
    }

    .card {
      border: 2px solid #ccc;  /* Borde gris claro */
    }
    
    /* Estilos para la página de blog completo */
    .blog-header {
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    
    .blog-author {
      font-weight: 600;
      color: #555;
    }
    
    .blog-date {
      color: #777;
    }
    
    .blog-main-image {
      max-height: 400px;
      width: 100%;
      object-fit: cover;
      margin-bottom: 20px;
      border-radius: 5px;
    }
    
    .blog-secondary-images {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }
    
    .blog-secondary-images img {
      max-height: 250px;
      width: 100%;
      object-fit: cover;
      border-radius: 5px;
    }
    
    .blog-content {
      line-height: 1.8;
      font-size: 1.1rem;
    }
    
    /* Estilos mejorados para el buscador */
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


    #searchResults .card {
        border: none;
        border-bottom: 1px solid #eee;
        border-radius: 0;
        transition: all 0.2s;
    }

    #searchResults .card:hover {
        background-color: #f8f9fa;
        transform: translateX(2px);
    }

    #searchResults .card-body {
        padding: 0.5rem;
    }

    #searchResults .card-title {
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #searchResults .text-muted {
        font-size: 0.7rem;
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
        <a class="navbar-brand fs-3 text-white" href="template2.php" id="navbarTitle">Inicio</a>
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
                  data-bs-toggle="dropdown" id="faunaText">Fauna</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_fauna.php" id="especiesFaunaText">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_fauna.php" id="avistamientosFaunaText">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                  data-bs-toggle="dropdown" id="floraText">Flora</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="especies_flora.php" id="especiesFloraText">Especies</a></li>
                  <li><a class="dropdown-item" href="avistamientos_flora.php" id="avistamientosFloraText">Avistamientos</a></li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" role="button"
                  data-bs-toggle="dropdown" id="optionsText">Opciones</a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="avistamiento_faunaR.php" id="registroFaunaText">Registros avistamientos Fauna</a></li>
                  <li><a class="dropdown-item" href="avistamiento_floraR.php" id="registroFloraText">Registros avistamientos Flora</a></li>
                </ul>
              </li>
              <li class="nav-item">
                <a class="nav-link text-dark fs-3" href="blogs.php">Blogs</a>
              </li>
              <li class="nav-item dropdown">
                <!-- Este es el cambio en el formulario de búsqueda -->
                <form method="GET" class="d-flex ms-3 align-items-center" style="max-width: 250px;">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm-2" 
                            id="searchInput" placeholder="Buscar Blogs..." 
                            style="height: 31px;" 
                            onkeyup="searchBlogs()"
                            autocomplete="off">
                        <button type="button" class="btn btn-primary btn-sm" style="height: 31px;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <ul id="searchResults" class="list-group position-absolute w-100" style="z-index: 1000; display: none;"></ul>
                </form>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Contenedor para los botones -->
        <div class="d-flex align-items-center">
          <!-- Botón Cerrar Sesión -->
          <div class="d-flex justify-content-end">
            <a href="../logout.php" class="btn btn-danger ms-2" id="logoutBtn">Cerrar sesión</a>
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
    <div class="container py-2">
      <h4 class="text-center mb-2">Blogs sobre la UMA Rancho Xamantún</h4>
      
      
      <!-- Listado de blogs en tarjetas -->
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
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
          <div class="col py-3">
            <div class="card blog-card h-100">
              <?php if ($blog['imagen1']): ?>
                <img src="<?= $blog['imagen1'] ?>" width="352px" height="280px"  alt="<?= htmlspecialchars($blog['titulo']) ?>">
              <?php else: ?>
                <img src="../imagenes/default-blog.jpg" class="card-img-top blog-card-img" alt="Imagen predeterminada">
              <?php endif; ?>
              
              <div class="card-body blog-card-body">
                <h5 class="card-title"><?= htmlspecialchars($blog['titulo']) ?></h5>
                <p class="card-text"><?= strlen($blog['descripcion']) > 50 ? substr($blog['descripcion'], 0, 50) . '...' : $blog['descripcion']; ?></p>
                <div class="d-flex justify-content-between align-items-center mt-auto">
                  <small class="text-muted">Publicado: <?= $fecha ?></small>
                  <a href="ver_blog.php?id_blog=<?= codificar($blog['id_blog']) ?>" class="btn btn-primary blog-card-btn">Leer....</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        
        <?php if (empty($filteredBlogs)): ?>
          <div class="col-12">
            <div class="alert alert-info text-center">
              No se encontraron blogs. Intenta con otra búsqueda.
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

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
  <script>
    // Array con los blogs generado desde PHP
    const blogsData = [
        <?php foreach ($blogs as $blog): ?>,
            {
                "titulo": "<?= addslashes($blog['titulo']) ?>",
                "imagen": "<?= $blog['imagen1'] ?>",
                "id_blog": "<?= codificar($blog['id_blog']) ?>"
            },
        <?php endforeach; ?>
    ];

    function searchBlogs() {
        let input = document.getElementById('searchInput').value.toLowerCase();
        let resultsContainer = document.getElementById('searchResults');
        resultsContainer.innerHTML = '';

        if (input.length > 0) {
            let filteredResults = blogsData.filter(blog =>
                blog.titulo.toLowerCase().includes(input)
            );

            if (filteredResults.length > 0) {
                resultsContainer.style.display = 'block';
                filteredResults.forEach(result => {
                    let listItem = document.createElement('li');
                    listItem.className = 'list-group-item d-flex align-items-center';
                    listItem.innerHTML = `
                        <img src="${result.imagen}" alt="${result.titulo}" 
                             style="max-width: 70px; height: 50px;" class="me-3">
                        <strong>${result.titulo}</strong>
                    `;
                    listItem.onclick = function() {
                        window.location.href = 'ver_blog.php?id_blog=' + result.id_blog;
                    };
                    resultsContainer.appendChild(listItem);
                });
            } else {
                resultsContainer.style.display = 'none';
            }
        } else {
            resultsContainer.style.display = 'none';
        }
    }

    document.addEventListener('click', function(e) {
        if (!document.getElementById('searchInput').contains(e.target)) {
            document.getElementById('searchResults').style.display = 'none';
        }
    });
</script>
<br><br><br>
</body>
</html>
