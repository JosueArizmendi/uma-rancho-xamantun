<?php
require_once '../../model/conexion_bd.php';
header('Content-Type: application/json');
$codigo = isset($_GET['id_avistamiento']) ? intval($_GET['id_avistamiento']) : null;
try {
    // Consulta para obtener latitud y longitud con un ID fijo
    $sql = 'SELECT latitud, longitud FROM avistamientos_flora WHERE id_avistamiento = :codigo';

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    $stmt->bindValue(':codigo', $codigo);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $puntos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si hay datos
    if ($puntos) {
        // Retornar los datos en formato JSON
        echo json_encode($puntos);
    } else {
        // Si no hay resultados, devolver un JSON vacío
        echo json_encode(['message' => 'No se encontraron datos para el ID proporcionado']);
    }
} catch (PDOException $e) {
    // Manejo de errores: devolver un mensaje de error en formato JSON
    echo json_encode(['error' => $e->getMessage()]);
}