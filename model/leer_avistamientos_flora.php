<?php
include 'conexion_bd.php';

try {
    // Obtener el término de búsqueda
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // Si hay un término de búsqueda, modificamos la consulta para filtrar los resultados
    if ($search != '') {
        $stmt = $conn->prepare("SELECT avistamientos_flora.*, especies_flora.nombre_cientifico FROM avistamientos_flora 
        INNER JOIN especies_flora ON avistamientos_flora.id_avistamiento_especie = especies_flora.id_especie
        WHERE avistamientos_flora.activo = 1 
        AND (especies_flora.nombre_cientifico LIKE :search OR avistamientos_flora.descripcion LIKE :search)
        ORDER BY avistamientos_flora.id_avistamiento_especie ASC;");
        $stmt->bindValue(':search', '%' . $search . '%');
    } else {
        // Si no hay búsqueda, traemos todos los avistamientos
        $stmt = $conn->prepare("SELECT avistamientos_flora.*, especies_flora.nombre_cientifico FROM avistamientos_flora 
        INNER JOIN especies_flora ON avistamientos_flora.id_avistamiento_especie = especies_flora.id_especie
        WHERE avistamientos_flora.activo = 1
        ORDER BY avistamientos_flora.id_avistamiento_especie ASC;");
    }

    // Ejecutamos la consulta
    $stmt->execute();

    // Recuperamos los resultados
    $avistamientos = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Ha ocurrido un error: " . $e->getMessage();
}
?>
