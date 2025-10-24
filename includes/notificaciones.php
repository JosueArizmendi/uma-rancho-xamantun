<?php
// includes/notificaciones.php
if (!isset($conn)) {
    include('../model/conexion_bd.php');
}

if (!isset($id_usuario_actual) && isset($_SESSION['usuario'])) {
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nom_usuario = ?");
    $stmt->execute([$_SESSION['usuario']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_usuario_actual = $user['id'];
}

if (isset($id_usuario_actual)) {
    // Obtener conteo de notificaciones no leídas
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM notificaciones WHERE id_usuario = ? AND leida = 0");
    $stmt->execute([$id_usuario_actual]);
    $notif_count = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener listado de notificaciones
    $stmt = $conn->prepare("SELECT n.*, b.titulo as blog_titulo 
                          FROM notificaciones n
                          LEFT JOIN blogs b ON n.id_blog = b.id_blog
                          WHERE n.id_usuario = ?
                          ORDER BY n.fecha DESC
                          LIMIT 15");
    $stmt->execute([$id_usuario_actual]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="position-relative me-3">
    <button id="campana-notificaciones" class="btn btn-link text-white position-relative">
        <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
        <?php if (isset($notif_count) && $notif_count['total'] > 0): ?>
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
            <?php if (isset($notificaciones) && count($notificaciones) > 0): ?>
                <?php foreach ($notificaciones as $notif): ?>
                    <a href="ver_blog.php?id_blog=<?= base64_encode($notif['id_blog']) ?><?= $notif['id_comentario'] ? '#comentario-'.$notif['id_comentario'] : '' ?>" 
                       class="list-group-item list-group-item-action <?= $notif['leida'] ? '' : 'notificacion-no-leida' ?>">
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

<script>
// JavaScript para manejar notificaciones
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
        
        const marcarLeidasBtn = document.getElementById('marcar-todas-leidas');
        const eliminarBtn = document.getElementById('eliminar-todas-notificaciones');
        
        if (marcarLeidasBtn) {
            marcarLeidasBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                marcarNotificacionesLeidas();
            });
        }
        
        if (eliminarBtn) {
            eliminarBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (confirm('¿Estás seguro de que quieres eliminar todas las notificaciones?')) {
                    eliminarNotificaciones();
                }
            });
        }
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