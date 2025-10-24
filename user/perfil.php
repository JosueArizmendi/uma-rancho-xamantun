<?php
session_start();
include('../model/conexion_bd.php');

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
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

if (!$user_data) {
    echo "Usuario no encontrado.";
    exit;
}

// Verifica si existe el mensaje de éxito en la URL
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    echo "<div class='alert alert-success text-center'>$message</div>";
}

// Obtener notificaciones no leídas
$id_usuario_actual = $user_data['id'];
$query_notificaciones = "SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario = ? AND leida = 0";
$stmt_notificaciones = $conn->prepare($query_notificaciones);
$stmt_notificaciones->execute([$id_usuario_actual]);
$notif_count = $stmt_notificaciones->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - UMA Rancho Xamantún</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Tu propio CSS -->
    <link rel="stylesheet" href="../CSS/custom-styles.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-bottom: 100px; /* Para evitar que el footer cubra contenido */
        }

        .profile-card {
            margin-top: 100px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            background-color: #fff;
            border: 2px solid #ccc;
        }

        .profile-header {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .profile-header img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #1B396A;
        }

        .btn-edit {
            background-color: #28a745;
            color: white;
            border-radius: 50px;
            padding: 10px 20px;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .image-preview {
            margin-top: 20px;
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 50%;
            display: none; /* Ocultar inicialmente */
        }

        /* Estilos para el traductor */
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

        /* Estilos para notificaciones */
        .notificacion-no-leida {
            background-color: rgba(13, 110, 253, 0.1);
            border-left: 3px solid #0d6efd;
        }

        .dropdown-notificaciones {
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
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
    <!-- Barra de navegación -->
    <header>
        <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
            <div class="me-3 ms-2">
                <img src="../img2/rancho.png" class="rounded" width="60" height="56">
            </div>
            <div class="container-fluid">
                <a class="navbar-brand fs-3 text-white" href="template2.php">Inicio</a>
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
                                  <li><a class="dropdown-item" href="acerca_de.php">Acerca del Rancho Xamantún</a></li>
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
                                <a class="nav-link dropdown-toggle text-dark fs-3 d-none d-md-block" href="#" data-bs-toggle="dropdown">Opciones</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="avistamiento_faunaR.php">Registros avistamientos Fauna</a></li>
                                    <li><a class="dropdown-item" href="avistamiento_floraR.php">Registros avistamientos Flora</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark fs-3" href="blogs.php">Blogs</a>
                            </li>
                            
                            <!-- Notificaciones -->
                            <div class="position-relative me-3">
                                <button id="campana-notificaciones" class="btn btn-link text-white position-relative">
                                    <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                                    <?php if ($notif_count['total'] > 0): ?>
                                    <span id="contador-notificaciones" class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-light" style="font-size: 0.6rem;">
                                        <?= $notif_count['total'] ?>
                                    </span>
                                    <?php endif; ?>
                                </button>
                                <div id="dropdown-notificaciones" class="dropdown-menu dropdown-menu-end p-2 dropdown-notificaciones" style="display: none;">
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
                            
                            <!-- Perfil de usuario -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-dark fs-3" href="#" data-bs-toggle="dropdown">
                                    <img src="<?= !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg' ?>" alt="Perfil" class="rounded-circle" width="30" height="30">
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="perfil.php">Ver Perfil</a></li>
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

    <!-- Traductor de Google -->
    <div class="container mt-2">
        <div id="google_translate_element" style="text-align: right;"></div>
    </div>

    <!-- Contenido principal -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="profile-card">
                    <!-- Cabecera de perfil -->
                    <div class="profile-header">
                        <?php
                        // Mostrar la imagen de perfil (si existe)
                        $imagenPerfil = !empty($user_data['imagen_perfil']) ? $user_data['imagen_perfil'] : '../imagenes_perfil/vacio.jpg';
                        ?>
                        <img src="<?= $imagenPerfil ?>" alt="Imagen de perfil" class="rounded-circle">
                        <h2 class="text-center"><?= htmlspecialchars($user_data['nom_usuario']) ?></h2>
                        <p class="text-muted text-center">Tipo de Usuario: <?= htmlspecialchars($user_data['rol']) ?></p>
                        
                        <!-- Vista previa de la imagen seleccionada -->
                        <img id="imagePreview" class="image-preview" src="" alt="Vista previa de la imagen">
                        
                        <!-- Reemplaza el formulario existente con este: -->
                        <form action="../model2/subir_imagen.php" method="POST" enctype="multipart/form-data" class="text-center mt-3" id="formImagenPerfil">
                            <div class="d-flex justify-content-center gap-2">
                                <label for="imagenPerfil" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Seleccionar imagen
                                </label>
                                <input type="file" id="imagenPerfil" name="imagenPerfil" accept="image/jpeg, image/png, image/gif" style="display: none;" onchange="previewImage(event)">
                                <button type="submit" class="btn btn-success" id="btnGuardar" disabled>
                                    <i class="bi bi-save"></i> Guardar
                                </button>
                            </div>
                            <small class="text-muted d-block mt-2">Formatos permitidos: JPG, PNG, GIF (Máx. 2MB)</small>
                            <div id="uploadProgress" class="progress mt-2" style="display: none; height: 5px;">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                        </form>
                        
                        <!-- Botón para editar perfil -->
                        <div class="text-center mt-4">
                            <a href="editar_usuario.php?id=<?= base64_encode($user_data['id']) ?>" class="btn btn-edit">
                                <i class="bi bi-pencil-square"></i> Editar Perfil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Footer -->
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para el traductor -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                includedLanguages: 'es,yua,en,fr,de',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
    <!-- Scripts personalizados -->
    <script>
        // Vista previa de imagen
        function previewImage(event) {
            const reader = new FileReader();
            const preview = document.getElementById('imagePreview');
            const btnGuardar = document.getElementById('btnGuardar');
            
            reader.onload = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
                btnGuardar.disabled = false;
            }
            
            if (event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
        
        // Manejo de notificaciones
        document.addEventListener('DOMContentLoaded', function() {
            const campana = document.getElementById('campana-notificaciones');
            const dropdown = document.getElementById('dropdown-notificaciones');
            
            if (campana && dropdown) {
                campana.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                    
                    if (dropdown.style.display === 'block') {
                        marcarNotificacionesLeidas();
                    }
                });
                
                document.addEventListener('click', function() {
                    dropdown.style.display = 'none';
                });
                
                // Marcar todas como leídas
                document.getElementById('marcar-todas-leidas')?.addEventListener('click', function(e) {
                    e.stopPropagation();
                    marcarNotificacionesLeidas();
                });
                
                // Eliminar todas las notificaciones
                document.getElementById('eliminar-todas-notificaciones')?.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (confirm('¿Estás seguro de que quieres eliminar todas las notificaciones?')) {
                        eliminarNotificaciones();
                    }
                });
            }
            
            function marcarNotificacionesLeidas() {
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
            
            function eliminarNotificaciones() {
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
                        
                        const lista = document.getElementById('lista-notificaciones');
                        if (lista) {
                            lista.innerHTML = '<div class="text-center py-3 text-muted">No hay notificaciones</div>';
                        }
                    }
                });
            }
        });
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
</body>
</html>