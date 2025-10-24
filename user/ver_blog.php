<?php 
include '../model/leer_datos_blog.php';
include '../model/conexion_bd.php';

function decodificar($id){
    return base64_decode($id);
}
  
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php?error=session_expired");
    exit;
}

  
// Obtener datos del usuario actual
$usuario_actual = $_SESSION['usuario'];
$query_usuario = "SELECT id, nom_usuario FROM usuarios WHERE nom_usuario = ?";
$stmt_usuario = $conn->prepare($query_usuario);
$stmt_usuario->bindParam(1, $usuario_actual, PDO::PARAM_STR);
$stmt_usuario->execute();
$user_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);
$id_usuario_actual = $user_data['id'];
  
// Obtener el ID del blog desde la URL
$id_blog = isset($_GET['id_blog']) ? decodificar($_GET['id_blog']) : 0;
  
// Buscar el blog específico
$blog_actual = null;
foreach ($blogs as $blog) {
    if ($blog['id_blog'] == $id_blog) {
        $blog_actual = $blog;
        break;
    }
}
  
if (!$blog_actual) {
    header("Location: blogs.php?error=blog_not_found");
    exit;
}
  
// Procesar nuevo comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Procesar nuevo comentario principal
    if (isset($_POST['comentario'])) {
        $comentario = $_POST['comentario'];
        
        try {
            $stmt = $conn->prepare("INSERT INTO comentarios_blog (id_blog, id_usuario, comentario) VALUES (?, ?, ?)");
            $stmt->execute([$id_blog, $id_usuario_actual, $comentario]);
            
            header("Location: ver_blog.php?id_blog=".base64_encode($id_blog));
            exit;
        } catch(PDOException $e) {
            $error_comentario = "Error al publicar el comentario: " . $e->getMessage();
        }
    }
    
    // Procesar nueva respuesta a comentario
    if (isset($_POST['respuesta'])) {
        $respuesta = $_POST['respuesta'];
        $id_comentario_padre = $_POST['id_comentario_padre'];
        
        try {
            // Insertar la respuesta
            $stmt = $conn->prepare("INSERT INTO respuestas_comentarios (id_comentario_padre, id_usuario, respuesta) VALUES (?, ?, ?)");
            $stmt->execute([$id_comentario_padre, $id_usuario_actual, $respuesta]);
            $id_respuesta = $conn->lastInsertId();
            
            // Obtener información del comentario padre para la notificación
            $stmt_padre = $conn->prepare("SELECT id_usuario FROM comentarios_blog WHERE id_comentario = ?");
            $stmt_padre->execute([$id_comentario_padre]);
            $comentario_padre = $stmt_padre->fetch(PDO::FETCH_ASSOC);
            
            // Solo notificar si el comentario padre es de otro usuario
            if ($comentario_padre && $comentario_padre['id_usuario'] != $id_usuario_actual) {
                $stmt_notif = $conn->prepare("INSERT INTO notificaciones 
                    (id_usuario, tipo, id_blog, id_comentario, id_respuesta, mensaje) 
                    VALUES (?, 'respuesta', ?, ?, ?, 'Nueva respuesta a tu comentario')");
                $stmt_notif->execute([
                    $comentario_padre['id_usuario'],
                    $id_blog,
                    $id_comentario_padre,
                    $id_respuesta
                ]);
            }
            
            header("Location: ver_blog.php?id_blog=".base64_encode($id_blog)."#comentario-".$id_comentario_padre);
            exit;
        } catch(PDOException $e) {
            $error_respuesta = "Error al publicar la respuesta: " . $e->getMessage();
        }
    }
}

// Procesar "me gusta" a comentario
if (isset($_GET['like_comentario'])) {
    $id_comentario = $_GET['like_comentario'];
    
    // Verificar si el usuario ya dio like a este comentario
    $stmt_check = $conn->prepare("SELECT id_like FROM likes_comentarios WHERE id_comentario = ? AND id_usuario = ?");
    $stmt_check->execute([$id_comentario, $id_usuario_actual]);
    
    if ($stmt_check->rowCount() == 0) {
        // Insertar el like
        $stmt_like = $conn->prepare("INSERT INTO likes_comentarios (id_comentario, id_usuario) VALUES (?, ?)");
        $stmt_like->execute([$id_comentario, $id_usuario_actual]);
        
        // Obtener información del comentario para la notificación
        $stmt_com = $conn->prepare("SELECT id_usuario FROM comentarios_blog WHERE id_comentario = ?");
        $stmt_com->execute([$id_comentario]);
        $comentario = $stmt_com->fetch(PDO::FETCH_ASSOC);
        
        // Solo notificar si el comentario es de otro usuario
        if ($comentario && $comentario['id_usuario'] != $id_usuario_actual) {
            $stmt_notif = $conn->prepare("INSERT INTO notificaciones 
                (id_usuario, tipo, id_blog, id_comentario, mensaje) 
                VALUES (?, 'like', ?, ?, 'A alguien le gusta tu comentario')");
            $stmt_notif->execute([
                $comentario['id_usuario'],
                $id_blog,
                $id_comentario
            ]);
        }
    } else {
        // Si ya dio like, quitarlo
        $stmt_unlike = $conn->prepare("DELETE FROM likes_comentarios WHERE id_comentario = ? AND id_usuario = ?");
        $stmt_unlike->execute([$id_comentario, $id_usuario_actual]);
    }
    
    header("Location: ver_blog.php?id_blog=".base64_encode($id_blog)."#comentario-".$id_comentario);
    exit;
}
// Obtener comentarios del blog
$query_comentarios = "SELECT c.*, u.nom_usuario 
                   FROM comentarios_blog c 
                   JOIN usuarios u ON c.id_usuario = u.id 
                   WHERE c.id_blog = ? AND c.activo = 1 
                   ORDER BY c.fecha_publicacion DESC";
$stmt_comentarios = $conn->prepare($query_comentarios);
$stmt_comentarios->execute([$id_blog]);
$comentarios = $stmt_comentarios->fetchAll(PDO::FETCH_ASSOC);

// Para cada comentario, obtener sus likes y respuestas
foreach ($comentarios as &$comentario) {
    // Obtener cantidad de likes
    // Dentro del foreach que procesa los comentarios ($comentarios as &$comentario)
    // Reemplaza la consulta actual de likes por esta:
    $stmt_likes = $conn->prepare("SELECT l.*, u.nom_usuario 
    FROM likes_comentarios l
    JOIN usuarios u ON l.id_usuario = u.id
    WHERE l.id_comentario = ?");
    $stmt_likes->execute([$comentario['id_comentario']]);
    $comentario['likes'] = $stmt_likes->fetchAll(PDO::FETCH_ASSOC);
    $comentario['total_likes'] = count($comentario['likes']);
    
    // Verificar si el usuario actual dio like
    $stmt_user_like = $conn->prepare("SELECT id_like FROM likes_comentarios WHERE id_comentario = ? AND id_usuario = ?");
    $stmt_user_like->execute([$comentario['id_comentario'], $id_usuario_actual]);
    $comentario['user_like'] = ($stmt_user_like->rowCount() > 0);
    
    // Resto del código...
    
    // Obtener respuestas
    $stmt_respuestas = $conn->prepare("SELECT r.*, u.nom_usuario 
                                     FROM respuestas_comentarios r
                                     JOIN usuarios u ON r.id_usuario = u.id
                                     WHERE r.id_comentario_padre = ? AND r.activo = 1
                                     ORDER BY r.fecha_publicacion ASC");
    $stmt_respuestas->execute([$comentario['id_comentario']]);
    $comentario['respuestas'] = $stmt_respuestas->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener notificaciones no leídas para el usuario actual
$query_notificaciones = "SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario = ? AND leida = 0";
$stmt_notificaciones = $conn->prepare($query_notificaciones);
$stmt_notificaciones->execute([$id_usuario_actual]);
$notif_count = $stmt_notificaciones->fetch(PDO::FETCH_ASSOC);

$fecha = date('d/m/Y', strtotime($blog_actual['fecha_publicacion']));
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
  <title><?= htmlspecialchars($blog_actual['titulo']) ?> - UMA Rancho Xamantún</title>
  <style>
    .comentario-item {
        position: relative;
    }
    
    .user-liked {
        color: #dc3545 !important;
    }
    
    .reply-form {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .notificacion-no-leida {
        background-color: rgba(13, 110, 253, 0.1);
        border-left: 3px solid #0d6efd;
    }
    
    @keyframes heartBeat {
        0% { transform: scale(1); }
        14% { transform: scale(1.3); }
        28% { transform: scale(1); }
        42% { transform: scale(1.3); }
        70% { transform: scale(1); }
    }
    
    .heart-beat {
        animation: heartBeat 1s;
    }

    .card {
      border: 2px solid #ccc;  /* Borde gris claro */
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

    .text-justify {
        text-align: justify !important;
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
              <div class="position-relative me-3">
              <button id="campana-notificaciones" class="btn btn-link text-white position-relative">
                    <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                    <?php if ($notif_count['total'] > 0): ?>
                    <span id="contador-notificaciones" class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-light" style="font-size: 0.6rem;">
                        <?= $notif_count['total'] ?>
                    </span>
                    <?php endif; ?>
              </button>
                <div id="dropdown-notificaciones" class="dropdown-menu dropdown-menu-end p-2" style="display: none; width: 350px;">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <h6 class="m-0">Notificaciones</h6>
                    <div>
                    <button id="marcar-todas-leidas" class="btn btn-sm btn-link">Marcar como leídas</button>
                    <button id="eliminar-todas-notificaciones" class="btn btn-sm btn-link text-danger">
                        <i class="bi bi-trash"></i> Eliminar todas
                    </button>
                    </div>
                </div>
                <div id="lista-notificaciones" class="list-group" style="max-height: 400px; overflow-y: auto;">
                    <?php
                    $query_notif = "SELECT n.*, b.titulo as blog_titulo 
                                  FROM notificaciones n
                                  LEFT JOIN blogs b ON n.id_blog = b.id_blog
                                  WHERE n.id_usuario = ?
                                  ORDER BY n.fecha DESC
                                  LIMIT 15";
                    $stmt_notif = $conn->prepare($query_notif);
                    $stmt_notif->execute([$id_usuario_actual]);
                    $notificaciones = $stmt_notif->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($notificaciones) > 0): 
                        foreach ($notificaciones as $notif): 
                            $clase_notif = $notif['leida'] ? '' : 'notificacion-no-leida';
                    ?>
                        <a href="ver_blog.php?id_blog=<?= base64_encode($notif['id_blog']) ?><?= $notif['id_comentario'] ? '#comentario-'.$notif['id_comentario'] : '' ?>" 
                           class="list-group-item list-group-item-action <?= $clase_notif ?>">
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <?php if ($notif['tipo'] == 'like'): ?>
                                        <i class="bi bi-heart-fill text-danger"></i>
                                    <?php else: ?>
                                        <i class="bi bi-reply-fill text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?= htmlspecialchars($notif['mensaje']) ?></div>
                                    <small class="text-muted"><?= $notif['blog_titulo'] ?></small>
                                    <div class="text-end"><small><?= date('d/m/Y H:i', strtotime($notif['fecha'])) ?></small></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted">No hay notificaciones</div>
                    <?php endif; ?>
                </div>
                </div>
              </div>
            </ul>
          </div>
        </div>
        
        <div class="d-flex align-items-center">
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

  <main class="fixed-top-offset">
    <div class="container py-4">
        <a href="blogs.php" class="btn btn-secondary back-btn">
        <i class="bi bi-arrow-left"></i> Volver a Blogs
        </a>
        
        <div class="card shadow-lg m-4">
        <div class="card-body">
        <div class="blog-header mb-4">
            <h1 class="blog-title card-title"><?= htmlspecialchars($blog_actual['titulo']) ?></h1>
            <div class="blog-meta d-flex justify-content-between align-items-center mt-3 w-100 px-3">
                <span class="blog-author text-start pe-3"><font size="3">Por: <?= htmlspecialchars($blog_actual['nombre_autor']) ?></font></span>
                <span class="blog-date text-end ps-3"><font size="3">Publicado: <?= $fecha ?></font></span>
            </div>
        </div>

            <?php if ($blog_actual['imagen1']): ?>
            <div class="text-center mb-4">
                <img src="<?= $blog_actual['imagen1'] ?>" class="blog-main-image img-fluid rounded-3" alt="<?= htmlspecialchars($blog_actual['titulo']) ?>">
            </div>
            <?php endif; ?>

            <div class="blog-content card-text mb-5 text-justify">
            <?= nl2br(htmlspecialchars($blog_actual['descripcion'])) ?>
            </div>

            <?php if ($blog_actual['imagen2'] || $blog_actual['imagen3']): ?>
            <div class="row g-3 mb-4">
                <?php if ($blog_actual['imagen2']): ?>
                <div class="col-md-6">
                    <img src="<?= $blog_actual['imagen2'] ?>" class="img-fluid rounded-3 shadow-sm" alt="Imagen 2 del blog">
                </div>
                <?php endif; ?>
                
                <?php if ($blog_actual['imagen3']): ?>
                <div class="col-md-6">
                    <img src="<?= $blog_actual['imagen3'] ?>" class="img-fluid rounded-3 shadow-sm" alt="Imagen 3 del blog">
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <div class="card shadow-lg m-4">
        <div class="card-body comentarios-section">
            <h3 class="mb-4 text-primary"><i class="bi bi-chat-square-text"></i> Comentarios</h3>
            
            <?php if(isset($error_comentario)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error_comentario ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="mb-5">
                <div class="mb-3">
                    <label for="comentario" class="form-label">Deja tu comentario:</label>
                    <textarea class="form-control" id="comentario" name="comentario" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publicar Comentario</button>
            </form>
            
            <div class="mt-4">
                <?php if(count($comentarios) > 0): ?>
                    <?php foreach($comentarios as $comentarios): ?>
                        <div class="comentario-item p-3 mb-4 bg-light rounded" id="comentario-<?= $comentarios['id_comentario'] ?>">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($comentarios['nom_usuario'], 0, 1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($comentarios['nom_usuario']) ?></strong>
                                </div>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($comentarios['fecha_publicacion'])) ?></small>
                            </div>
                            <p><?= nl2br(htmlspecialchars($comentarios['comentario'])) ?></p>
                            
                            <div class="d-flex gap-2">
                            <a href="ver_blog.php?id_blog=<?= base64_encode($id_blog) ?>&like_comentario=<?= $comentarios['id_comentario'] ?>#comentario-<?= $comentarios['id_comentario'] ?>" 
                            class="btn btn-sm btn-outline-danger <?= $comentarios['user_like'] ? 'user-liked' : '' ?>"
                            data-bs-toggle="tooltip" 
                            data-bs-html="true"
                            title="<?= $comentarios['total_likes'] > 0 ? 
                                'Les gusta a:<br>' . 
                                implode('<br>', array_map(function($like) {
                                    return htmlspecialchars($like['nom_usuario']);
                                }, $comentarios['likes'])) : 
                                'Nadie ha reaccionado aún' ?>">
                                <i class="bi bi-heart<?= $comentarios['user_like'] ? '-fill' : '' ?>"></i> 
                                <span class="like-count"><?= $comentarios['total_likes'] ?></span>
                            </a>
                                <button class="btn btn-sm btn-outline-secondary reply-btn"
                                        data-comentario-id="<?= $comentarios['id_comentario'] ?>">
                                    <i class="bi bi-reply"></i> Responder
                                </button>
                            </div>
                            
                            <div class="reply-form mt-3" id="reply-form-<?= $comentarios['id_comentario'] ?>" style="display: none;">
                                <form method="POST">
                                    <input type="hidden" name="id_comentario_padre" value="<?= $comentarios['id_comentario'] ?>">
                                    <textarea class="form-control mb-2" name="respuesta" placeholder="Escribe tu respuesta..." required></textarea>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply">Cancelar</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Enviar</button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="respuestas mt-3">
                                <?php foreach($comentarios['respuestas'] as $respuesta): ?>
                                    <div class="respuesta-item p-3 mb-2 bg-white rounded">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <strong><?= htmlspecialchars($respuesta['nom_usuario']) ?></strong>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($respuesta['fecha_publicacion'])) ?></small>
                                        </div>
                                        <p><?= nl2br(htmlspecialchars($respuesta['respuesta'])) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-chat-square-text text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted fs-5 mt-2">No hay comentarios aún. ¡Sé el primero en comentar!</p>
                    </div>
                <?php endif; ?>
            </div>
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

  <br><br><br>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const campana = document.getElementById('campana-notificaciones');
        const dropdown = document.getElementById('dropdown-notificaciones');
        
        campana.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            
            if (dropdown.style.display === 'block') {
                fetch('../model/marcar_notificaciones_leidas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id_usuario=<?= $id_usuario_actual ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const contador = document.getElementById('contador-notificaciones');
                        if (contador) contador.style.display = 'none';
                        
                        document.querySelectorAll('.notificacion-no-leida').forEach(el => {
                            el.classList.remove('notificacion-no-leida');
                        });
                    }
                });
            }
        });
        
        document.addEventListener('click', function() {
            dropdown.style.display = 'none';
        });
        
        document.getElementById('marcar-todas-leidas').addEventListener('click', function(e) {
            e.stopPropagation();
            fetch('../model/marcar_notificaciones_leidas.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id_usuario=<?= $id_usuario_actual ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contador = document.getElementById('contador-notificaciones');
                    if (contador) contador.style.display = 'none';
                    
                    document.querySelectorAll('.notificacion-no-leida').forEach(el => {
                        el.classList.remove('notificacion-no-leida');
                    });
                }
            });
        });
        
        document.getElementById('eliminar-todas-notificaciones').addEventListener('click', function(e) {
            e.stopPropagation();
            if(confirm('¿Estás seguro de que quieres eliminar todas las notificaciones?')) {
                fetch('../model/eliminar_notificaciones.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id_usuario=<?= $id_usuario_actual ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const contador = document.getElementById('contador-notificaciones');
                        if (contador) contador.style.display = 'none';
                        
                        document.getElementById('lista-notificaciones').innerHTML = 
                            '<div class="text-center py-3 text-muted">No hay notificaciones</div>';
                    }
                });
            }
        });
        
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const comentarioId = this.dataset.comentarioId;
                const form = document.getElementById(`reply-form-${comentarioId}`);
                
                document.querySelectorAll('.reply-form').forEach(f => {
                    if (f.id !== `reply-form-${comentarioId}`) {
                        f.style.display = 'none';
                    }
                });
                
                form.style.display = form.style.display === 'block' ? 'none' : 'block';
            });
        });
        
        document.querySelectorAll('.cancel-reply').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.reply-form').style.display = 'none';
            });
        });

        // Inicializar tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover focus'
            });
        });
        
        // Animación para nuevos likes
        if (window.location.hash.includes('comentario-')) {
            const comentarioId = window.location.hash.split('-')[1];
            const likeBtn = document.querySelector(`#comentario-${comentarioId} .btn-outline-danger`);
            
            if (likeBtn && window.location.search.includes('like_comentario')) {
                likeBtn.classList.add('heart-beat');
                setTimeout(() => {
                    likeBtn.classList.remove('heart-beat');
                    // Actualizar el estado del like después de la animación
                    const icon = likeBtn.querySelector('i');
                    const isLiked = icon.classList.contains('bi-heart-fill');
                    if (isLiked) {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                        likeBtn.classList.remove('user-liked');
                    } else {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                        likeBtn.classList.add('user-liked');
                    }
                }, 1000);
            }
        }
    });
  </script>
</body>
</html>