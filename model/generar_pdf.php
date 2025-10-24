<?php
// models/generar_pdf.php

require('../lib/fpdf/fpdf.php'); // Incluir FPDF (asegúrate de que la ruta sea correcta)
include 'conexion_bd.php'; // Incluir la conexión a la base de datos

// Obtener el ID de la especie desde la URL
$id_especie = base64_decode($_GET['id_especie']);

// Consulta para obtener los datos de la especie y la imagen del avistamiento
$query = "
    SELECT esp.*, av.ruta_imagen 
    FROM especies_animales esp
    LEFT JOIN avistamientos_animales av ON esp.id_especie = av.id_avistamiento_especie
    WHERE esp.id_especie = ?
    LIMIT 1
";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $id_especie, PDO::PARAM_INT);
$stmt->execute();
$especie = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar si se encontró la especie
if (!$especie) {
    die('Especie no encontrada.');
}

// Función para obtener la respuesta de la IA
function obtenerRespuestaIA($nombreCientifico, $nombreComun, $habitat) {
    $apiKey = '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';
    $apiKey = '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';
    $url = 'https://api.openai.com/v1/chat/completions';

    $prompt = "Dame las características de esta especie de fauna:\n";
    $prompt .= "Nombre científico: $nombreCientifico\n";
    $prompt .= "Nombre común: $nombreComun\n";
    $prompt .= "Hábitat: $habitat";

    $data = [
        'model' => 'gpt-4o-mini', // Asegúrate de usar un modelo válido
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
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "Error de texto: No se pudo obtener una respuesta de la IA.";
    }

    $responseData = json_decode($response, true);
    return $responseData['choices'][0]['message']['content'] ?? "Error de texto: Respuesta no válida de la IA.";
}

// Obtener la respuesta de la IA
$respuestaIA = obtenerRespuestaIA(
    $especie['nombre_cientifico'],
    $especie['nombre_comun'],
    $especie['habitat']
);

// Crear el PDF
class PDF extends FPDF
{
    // Cabecera del PDF
    function Header()
    {
        // Título
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, utf8_decode('Rancho Xamantun - Reporte de Especie'), 0, 1, 'C');
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
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12); // Reducir el tamaño de la fuente para ahorrar espacio

// Encabezado verde
$pdf->SetFillColor(144, 238, 144); // Color verde claro
$pdf->SetTextColor(0, 0, 0); // Texto negro
$pdf->Cell(0, 10, utf8_decode('Información de la Especie'), 1, 1, 'C', true);
$pdf->Ln(5); // Espacio después del encabezado

// Función para escribir títulos en negritas y texto justificado sin sangrías
function writeBoldTitle($pdf, $title, $content) {
    $pdf->SetFont('Arial', 'B', 12); // Fuente en negrita (tamaño reducido)
    $pdf->Cell(50, 8, utf8_decode($title) . ' ', 0, 0); // Título en negrita con espacio adicional
    $pdf->SetFont('Arial', '', 12); // Fuente normal (tamaño reducido)
    $pdf->MultiCell(0, 8, utf8_decode($content), 0, 'J', false); // Texto justificado sin sangrías
}

// Posición inicial para los detalles de la especie
$pdf->SetY(40); // Ajusta la posición vertical inicial

// Escribir el nombre científico
writeBoldTitle($pdf, 'Nombre Científico:', $especie['nombre_cientifico']);

// Agregar la imagen de la especie (si existe)
if (!empty($especie['ruta_imagen'])) {
    $ruta_imagen = '../' . $especie['ruta_imagen']; // Ajusta la ruta según tu estructura de archivos

    // Verificar si la imagen existe
    if (file_exists($ruta_imagen)) {
        // Verificar si la imagen es JFIF
        if (strtolower(pathinfo($ruta_imagen, PATHINFO_EXTENSION)) === 'jfif') {
            // Convertir JFIF a JPEG
            $imagen = imagecreatefromjpeg($ruta_imagen); // Soporta JFIF
            $ruta_temporal = tempnam(sys_get_temp_dir(), 'especie') . '.jpg'; // Ruta temporal
            imagejpeg($imagen, $ruta_temporal, 90); // Guardar como JPEG con calidad del 90%
            imagedestroy($imagen); // Liberar memoria

            // Insertar la imagen convertida en el PDF (parte derecha)
            $pdf->Image($ruta_temporal, 120, $pdf->GetY() - 8, 80, 60); // Posición X: 120, Y: actual - 8
            unlink($ruta_temporal); // Eliminar el archivo temporal
        } else {
            // Si no es JFIF, insertar la imagen directamente (parte derecha)
            $pdf->Image($ruta_imagen, 120, $pdf->GetY() - 8, 80, 60); // Posición X: 120, Y: actual - 8
        }
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetXY(120, $pdf->GetY());
        $pdf->Cell(0, 10, utf8_decode('Imagen no disponible'), 0, 1);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetXY(120, $pdf->GetY());
    $pdf->Cell(0, 10, utf8_decode('Imagen no disponible'), 0, 1);
}

// Continuar con el resto de los datos
writeBoldTitle($pdf, 'Especie:', $especie['especie']);
writeBoldTitle($pdf, 'Reino:', $especie['reino']);
writeBoldTitle($pdf, 'Filo:', $especie['filo']);
writeBoldTitle($pdf, 'Clase:', $especie['clase']);
writeBoldTitle($pdf, 'Orden:', $especie['orden']);
writeBoldTitle($pdf, 'Familia:', $especie['familia']);
writeBoldTitle($pdf, 'Género:', $especie['genero']);
writeBoldTitle($pdf, 'Nombre Común:', $especie['nombre_comun']);
writeBoldTitle($pdf, 'Descripción Física:', $especie['descripcion_fisica']);
writeBoldTitle($pdf, 'Hábitat:', $especie['habitat']);
writeBoldTitle($pdf, 'Usos Trad. y Med:', $especie['usos']);
writeBoldTitle($pdf, 'Estado de Conservación:', $especie['estado_conservacion']);

// Agregar una nueva página para la información de la IA
$pdf->AddPage(); // Segunda página
// Agregar la información de la IA
$pdf->Ln(1); // Espacio antes de la sección de IA
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Información Complementaria Generada por OpenAI:'), 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 8, utf8_decode($respuestaIA), 0, 'J', false);

// Salida del PDF
$nombre_archivo = 'reporte_especie_' . $especie['nombre_cientifico'] . '.pdf';
$pdf->Output('D', $nombre_archivo); // Descargar con nombre personalizado
?>
