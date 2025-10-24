<?php
include 'conexion_bd.php';

try {
    // Comprobar si hay un término de búsqueda
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Si hay un término de búsqueda, se modifica la consulta para buscar por especie o descripción
    if ($search != '') {
        $stmt = $conn->prepare("SELECT avistamientos_animales.*, especies_animales.nombre_cientifico FROM avistamientos_animales 
        INNER JOIN especies_animales ON avistamientos_animales.id_avistamiento_especie = especies_animales.id_especie
        WHERE avistamientos_animales.activo = 1 
        AND (especies_animales.nombre_cientifico LIKE :search OR avistamientos_animales.descripcion LIKE :search)
        ORDER BY avistamientos_animales.id_avistamiento_especie ASC;");
        $stmt->bindValue(':search', '%' . $search . '%'); // Parametrización para evitar inyecciones SQL
    } else {
        // Si no hay búsqueda, traer todos los avistamientos
        $stmt = $conn->prepare("SELECT avistamientos_animales.*, especies_animales.nombre_cientifico FROM avistamientos_animales 
        INNER JOIN especies_animales ON avistamientos_animales.id_avistamiento_especie = especies_animales.id_especie 
        WHERE avistamientos_animales.activo = 1 ORDER BY avistamientos_animales.id_avistamiento_especie ASC;");
    }

    // Ejecución de la consulta
    $stmt->execute();

    // Recuperar los datos
    $avistamientos = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}
?>
