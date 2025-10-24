<?php
// models/generar_pdf_avistamiento.php

require('../lib/fpdf/fpdf.php'); // Incluir FPDF (asegúrate de que la ruta sea correcta)
include 'conexion_bd.php'; // Incluir la conexión a la base de datos

// Evitar que se imprima salida no deseada
ob_start();

// Verificar si el ID del avistamiento está presente en la URL
if (!isset($_GET['id_avistamiento'])) {
    die('ID de avistamiento no proporcionado.');
}

// Obtener el ID del avistamiento desde la URL
$id_avistamiento = base64_decode($_GET['id_avistamiento']);

// Verificar que el ID sea un número válido
if (!is_numeric($id_avistamiento)) {
    die('ID de avistamiento no válido.');
}

try {
    // Consulta para obtener los datos del avistamiento
    $query = "
        SELECT av.*, esp.nombre_comun AS especie 
        FROM avistamientos_animales av
        LEFT JOIN especies_animales esp ON av.id_avistamiento_especie = esp.id_especie
        WHERE av.id_avistamiento = ?
    ";
    $stmt = $conn->prepare($query);

    // Verificar si la preparación de la consulta fue exitosa
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->errorInfo()[2]);
    }

    // Vincular el parámetro y ejecutar la consulta
    $stmt->bindParam(1, $id_avistamiento, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener los datos del avistamiento
    $avistamiento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el avistamiento
    if (!$avistamiento) {
        die('Avistamiento no encontrado.');
    }
} catch (Exception $e) {
    die('Error en la consulta: ' . $e->getMessage());
}

// Función para obtener la respuesta de la IA
function obtenerRespuestaIA($especie, $fechaAvistamiento, $latitud, $longitud, $descripcion) {
    $apiKey = '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';
    $url = 'https://api.openai.com/v1/chat/completions';

    // Crear el prompt con los datos del avistamiento
    $prompt = "Dame información complementaria sobre este avistamiento:\n";
    $prompt .= "Especie: $especie\n";
    $prompt .= "Fecha de avistamiento: $fechaAvistamiento\n";
    $prompt .= "Ubicación (latitud, longitud): $latitud, $longitud\n";
    $prompt .= "Descripción: $descripcion\n";
    $prompt .= "Proporciona detalles adicionales sobre la especie y su hábitat.";

    $data = [
        'model' => 'gpt-4o-mini', // Cambia a un modelo válido
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens' => 500
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n" .
                        "Authorization: Bearer $apiKey\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);

    // Intentar obtener la respuesta de la API
    try {
        $response = @file_get_contents($url, false, $context); // Suprimir advertencias
        if ($response === FALSE) {
            return "Error de texto: No se pudo obtener una respuesta de la IA.";
        }

        $responseData = json_decode($response, true);
        return $responseData['choices'][0]['message']['content'] ?? "Error de texto: Respuesta no válida de la IA.";
    } catch (Exception $e) {
        return "Error de texto: " . $e->getMessage();
    }
}

// Obtener la respuesta de la IA
$respuestaIA = obtenerRespuestaIA(
    $avistamiento['especie'],
    $avistamiento['fecha_avistamiento'],
    $avistamiento['latitud'],
    $avistamiento['longitud'],
    $avistamiento['descripcion']
);

// Crear el PDF
class PDF extends FPDF
{
    // Cabecera del PDF
    function Header()
    {
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Rancho Xamantun - Reporte de Avistamiento'), 0, 1, 'C');
        $this->Ln(5); // Espacio después del título
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Posición a 1.5 cm desde el final
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C'); // Número de página
    }
}

// Instanciar el PDF
$pdf = new PDF();
$pdf->AddPage(); // Primera página
$pdf->SetFont('Arial', '', 12); // Reducir el tamaño de la fuente para ahorrar espacio

// Encabezado verde
$pdf->SetFillColor(144, 238, 144); // Color verde claro
$pdf->SetTextColor(0, 0, 0); // Texto negro
$pdf->Cell(0, 10, utf8_decode('Información del Avistamiento'), 1, 1, 'C', true);
$pdf->Ln(5); // Espacio después del encabezado

// Función para escribir títulos en negritas y texto justificado sin sangrías
function writeBoldTitle($pdf, $title, $content) {
    $pdf->SetFont('Arial', 'B', 12); // Fuente en negrita (tamaño reducido)
    $pdf->Cell(50, 8, utf8_decode($title) . ' ', 0, 0); // Título en negrita con espacio adicional
    $pdf->SetFont('Arial', '', 12); // Fuente normal (tamaño reducido)
    $pdf->MultiCell(0, 8, utf8_decode($content), 0, 'J', false); // Texto justificado sin sangrías
}

// Posición inicial para los detalles del avistamiento
$pdf->SetY(40); // Ajusta la posición vertical inicial

// Escribir el nombre de la especie
writeBoldTitle($pdf, 'Especie Avistada:', $avistamiento['especie']);

// --- AJUSTE AQUÍ: Aumentar la posición X (ej: 140 en lugar de 120) ---
$posicion_x_imagen = 140; // Más a la derecha (antes era 120)

// Agregar la imagen al PDF (si existe)
if (!empty($avistamiento['ruta_imagen'])) {
    $ruta_imagen = '../' . $avistamiento['ruta_imagen'];

    if (file_exists($ruta_imagen)) {
        if (strtolower(pathinfo($ruta_imagen, PATHINFO_EXTENSION)) === 'jfif') {
            $imagen = imagecreatefromjpeg($ruta_imagen);
            $ruta_temporal = tempnam(sys_get_temp_dir(), 'avistamiento') . '.jpg';
            imagejpeg($imagen, $ruta_temporal, 90);
            imagedestroy($imagen);

            // Usar $posicion_x_imagen en lugar de 120
            $pdf->Image($ruta_temporal, $posicion_x_imagen, $pdf->GetY() - 8, 50, 30);
            unlink($ruta_temporal);
        } else {
            // Usar $posicion_x_imagen en lugar de 120
            $pdf->Image($ruta_imagen, $posicion_x_imagen, $pdf->GetY() - 8, 50, 30);
        }
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY($posicion_x_imagen, $pdf->GetY());
        $pdf->Cell(0, 10, utf8_decode('Imagen no disponible'), 0, 1);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetXY($posicion_x_imagen, $pdf->GetY());
    $pdf->Cell(0, 10, utf8_decode('Imagen no disponible'), 0, 1);
}

// Continuar con el resto de los datos
writeBoldTitle($pdf, 'Fecha de Avistamiento:', $avistamiento['fecha_avistamiento']);
writeBoldTitle($pdf, 'Latitud:', $avistamiento['latitud']);
writeBoldTitle($pdf, 'Longitud:', $avistamiento['longitud']);
writeBoldTitle($pdf, 'Descripción:', $avistamiento['descripcion']);


// Agregar la información de la IA
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Información Complementaria Generada por OpenAI:'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode($respuestaIA), 0, 'J', false);

// Limpiar el buffer de salida
ob_end_clean();

// Salida del PDF
$nombre_archivo = 'reporte_avistamiento_' . $avistamiento['especie'] . '_' . $avistamiento['fecha_avistamiento'] . '.pdf';
$pdf->Output('D', $nombre_archivo); // Descargar con nombre personalizado
?>

