<?php
// process.php - Procesamiento de datos y generación de HTML

// Headers para respuesta JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Verificar que es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Obtener acción
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'generate':
        generateDashboard();
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        break;
}

function generateDashboard() {
    try {
        // Obtener datos de las secciones
        $sectionsJson = $_POST['sections'] ?? '';
        $sections = json_decode($sectionsJson, true);
        
        if (!$sections || !is_array($sections)) {
            throw new Exception('No se recibieron secciones válidas');
        }
        
        // Generar HTML del dashboard
        $dashboardHtml = createDashboardHTML($sections);
        
        // Guardar HTML generado (opcional)
        $filename = 'dashboard_' . date('Y-m-d_H-i-s') . '.html';
        $filePath = 'generated/html/' . $filename;
        
        // Crear directorio si no existe
        if (!file_exists('generated/html/')) {
            mkdir('generated/html/', 0755, true);
        }
        
        file_put_contents($filePath, $dashboardHtml);
        
        // Retornar respuesta exitosa
        echo json_encode([
            'success' => true,
            'html' => $dashboardHtml,
            'filename' => $filename,
            'filepath' => $filePath
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function createDashboardHTML($sections) {
    // Obtener período del dashboard (asumimos que se envía posteriormente)
    $period = $_POST['period'] ?? 'Primera Quincena Mayo 2025';
    $title = $_POST['dashboard_title'] ?? 'SOPORTE TÉCNICO E INFRAESTRUCTURA';
    
    // Inicio del HTML
    $html = generateDashboardHeader($title, $period);
    
    // Procesar cada sección
    foreach ($sections as $index => $section) {
        $html .= generateSectionHTML($section, $index);
    }
    
    // Fin del HTML
    $html .= generateDashboardFooter();
    
    return $html;
}

function generateDashboardFooter() {
    return <<<HTML
    </div>
</body>
</html>
HTML;
}

function generateDashboardHeader($title, $period) {
    return <<<HTML
<!DOCTYPE html>
<html lang="es">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        /* Variables de color */
        :root {
            --primary-color: #1e3a5c;
            --secondary-color: #f6ba3c;
            --tertiary-color: #5bc2c1;
            --light-color: #f7f9fc;
            --text-color: #fff;
            --dark-text: #1e3a5c;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --light-gray: #e9e9e9;
            --app-color: #795548;
            --correo-color: #e91e63;
            --wfm-color: var(--tertiary-color);
            --usuario-color: var(--warning-color);
            --internet-color: #9c27b0;
            --medica-color: var(--success-color);
            --vpn-color: #5a6acf;
            --portal-color: #3f51b5;
            --terceria-color: #009688;
            --dms-color: #607d8b;
            --citas-color: #ff5722;
            --beneficiario-color: #2196f3;
            --historial-color: #673ab7;
            --recetas-color: #8bc34a;
            --videollamada-color: #00bcd4;
        }
        
        /* Estilos generales para formato de página */
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--primary-color);
            color: var(--text-color);
            font-size: 11px;
            line-height: 1.3;
        }
        
        .container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 15px;
            box-sizing: border-box;
            min-height: 100vh;
        }
        
        /* Estilos del encabezado */
        .header {
            background-color: var(--secondary-color);
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 8px 0 0;
            color: var(--primary-color);
            font-size: 16px;
            font-weight: normal;
        }
        
        /* Secciones principales */
        .section {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            color: white;
            page-break-inside: avoid;
        }
        
        .section-title {
            color: var(--secondary-color);
            font-size: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        /* Fila de gráficos/contenido */
        .chart-row {
            display: flex;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        /* Contenedores de gráficos */
        .chart-container {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 10px;
            flex: 1;
            min-width: 280px;
        }
        
        .chart-title {
            font-size: 14px;
            margin: 0 0 10px 0;
            text-align: center;
            color: white;
            font-weight: bold;
        }
        
        /* Tablas */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        .data-table th {
            background-color: var(--light-gray);
            color: var(--dark-text);
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }
        
        .data-table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .data-table td {
            padding: 5px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 9px;
        }
        
        .data-table td:last-child {
            font-weight: bold;
            color: var(--secondary-color);
        }
        
        /* Gráficos de donut */
        .donut-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 5px 0;
            min-height: 250px;
        }
        
        .donut-chart {
            width: 150px;
            height: 150px;
            position: relative;
            margin: 0 auto 15px;
            border-radius: 50%;
        }
        
        .donut-hole {
            position: absolute;
            width: 90px;
            height: 90px;
            background-color: var(--primary-color);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: white;
            font-size: 18px;
        }
        
        /* Leyenda */
        .legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 8px;
            font-size: 9px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            margin: 0 8px 6px 0;
        }
        
        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            margin-right: 4px;
        }
        
        /* Gráficos de barras */
        .bar-chart-container {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            min-height: 300px;
            position: relative;
        }
        
        .bar-chart {
            height: 250px;
            display: flex;
            justify-content: space-around;
            padding: 30px 20px 50px 50px;
            position: relative;
            width: 100%;
        }
        
        .bar-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
            max-width: 100px;
            height: 170px; /* Altura fija para el área de barras */
        }
        
        .bar {
            width: 50px;
            border-radius: 4px 4px 0 0;
            position: absolute;
            bottom: 0; /* Anclar al bottom del contenedor */
            opacity: 0.9;
            transition: opacity 0.3s ease;
            min-height: 5px;
        }
        
        .bar:hover {
            opacity: 1;
        }
        
        .bar-value {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: bold;
            color: var(--secondary-color);
            white-space: nowrap;
        }
        
        .bar-label {
            position: absolute;
            bottom: -40px;
            font-size: 11px;
            text-align: center;
            color: white;
            max-width: 80px;
            word-wrap: break-word;
            line-height: 1.2;
        }
        
        .grid-line {
            position: absolute;
            left: 50px;
            right: 20px;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .grid-line-label {
            position: absolute;
            left: -45px;
            top: -8px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.8);
            text-align: right;
            width: 40px;
        }
            position: absolute;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .grid-line-label {
            position: absolute;
            left: -35px;
            top: -8px;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Estilos para impresión */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>{$title}</h1>
            <h2>{$period}</h2>
        </div>
HTML;
}

function generateSectionHTML($section, $index) {
    $sectionHtml = '<div class="section">';
    $sectionHtml .= '<h2 class="section-title">' . htmlspecialchars($section['title']) . '</h2>';
    
    // Determinar el layout de la sección
    if ($section['type'] === 'table') {
        $sectionHtml .= generateTableOnly($section);
    } elseif ($section['type'] === 'chart') {
        $sectionHtml .= generateChartOnly($section);
    } else { // table-chart
        $sectionHtml .= generateTableAndChart($section);
    }
    
    $sectionHtml .= '</div>';
    
    return $sectionHtml;
}

function generateTableOnly($section) {
    return generateTableHTML($section, 'full');
}

function generateChartOnly($section) {
    return generateChartHTML($section, 'full');
}

function generateTableAndChart($section) {
    $html = '<div class="chart-row">';
    $html .= generateTableHTML($section, 'half');
    $html .= generateChartHTML($section, 'half');
    $html .= '</div>';
    return $html;
}

function generateTableHTML($section, $width = 'half') {
    $containerClass = $width === 'full' ? 'chart-container' : 'chart-container';
    
    $html = '<div class="' . $containerClass . '">';
    $html .= '<h3 class="chart-title">' . htmlspecialchars($section['tableTitle'] ?? 'Datos') . '</h3>';
    $html .= '<table class="data-table">';
    
    // Headers de la tabla
    $html .= '<tr>';
    $html .= '<th>#</th>';
    $html .= '<th>Estado</th>';
    $html .= '<th>Cantidad</th>';
    $html .= '<th>Tiempo Promedio (Días)</th>';
    $html .= '</tr>';
    
    // Datos de la tabla
    if (!empty($section['data'])) {
        foreach ($section['data'] as $index => $row) {
            $html .= '<tr>';
            $html .= '<td>' . ($index + 1) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['label']) . '</td>';
            $html .= '<td>' . $row['count'] . '</td>';
            $html .= '<td>' . number_format($row['time'], 2) . '</td>';
            $html .= '</tr>';
        }
    }
    
    $html .= '</table>';
    $html .= '</div>';
    
    return $html;
}

function generateChartHTML($section, $width = 'half') {
    if (empty($section['data'])) {
        return '';
    }
    
    $containerClass = $width === 'full' ? 'chart-container' : 'chart-container';
    $chartType = $section['chartType'] ?? 'doughnut';
    
    // Debug: Log del tipo de gráfico
    error_log("Generando gráfico tipo: " . $chartType . " para sección: " . $section['title']);
    
    // Título dinámico basado en el tipo de gráfico
    $chartTitle = 'Distribución por Estados';
    if ($chartType === 'bar') {
        $chartTitle = 'Comparativa por Estados';
    }
    
    $html = '<div class="' . $containerClass . '">';
    $html .= '<h3 class="chart-title">' . $chartTitle . '</h3>';
    
    // Generar gráfico según el tipo seleccionado
    if ($chartType === 'bar') {
        $html .= generateBarChart($section['data']);
    } else {
        // Para doughnut y pie
        $html .= '<div class="donut-container">';
        $total = array_sum(array_column($section['data'], 'count'));
        $html .= generateDonutChart($section['data'], $total);
        $html .= generateLegend($section['data']);
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

function generateDonutChart($data, $total) {
    $colors = [
        '#3f51b5', '#ff9800', '#5bc2c1', '#009688', '#4caf50',
        '#795548', '#e91e63', '#5a6acf', '#9c27b0', '#ff5722'
    ];
    
    $currentAngle = 0;
    $gradientStops = [];
    
    foreach ($data as $index => $item) {
        $percentage = ($item['count'] / $total) * 100;
        $angle = ($percentage / 100) * 360;
        $color = $colors[$index % count($colors)];
        
        $endAngle = $currentAngle + $angle;
        
        $gradientStops[] = $color . ' ' . $currentAngle . 'deg ' . $endAngle . 'deg';
        $currentAngle = $endAngle;
    }
    
    $gradientStyle = 'background: conic-gradient(' . implode(', ', $gradientStops) . ');';
    
    $html = '<div class="donut-chart" style="' . $gradientStyle . '">';
    $html .= '<div class="donut-hole">' . $total . '</div>';
    $html .= '</div>';
    
    return $html;
}

function generateLegend($data) {
    $colors = [
        '#3f51b5', '#ff9800', '#5bc2c1', '#009688', '#4caf50',
        '#795548', '#e91e63', '#5a6acf', '#9c27b0', '#ff5722'
    ];
    
    $html = '<div class="legend">';
    
    foreach ($data as $index => $item) {
        $color = $colors[$index % count($colors)];
        $html .= '<div class="legend-item">';
        $html .= '<div class="legend-color" style="background-color: ' . $color . ';"></div>';
        $html .= '<span>' . htmlspecialchars($item['label']) . ' (' . $item['count'] . ')</span>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

function generateBarChart($data) {
    $colors = [
        '#3f51b5', '#ff9800', '#5bc2c1', '#009688', '#4caf50',
        '#795548', '#e91e63', '#5a6acf', '#9c27b0', '#ff5722'
    ];
    
    $maxValue = max(array_column($data, 'count'));
    $chartHeight = 250;
    $availableHeight = 170; // Altura fija que coincide con CSS .bar-item
    
    $html = '<div class="bar-chart-container">';
    $html .= '<div class="bar-chart" style="height: ' . $chartHeight . 'px;">';
    
    // Generar líneas de grid horizontales (de abajo hacia arriba)
    for ($i = 0; $i <= 5; $i++) {
        $value = round(($maxValue / 5) * $i);
        // Grid lines posicionadas exactamente donde estarán las barras
        $position = 50 + ($availableHeight / 5) * $i;
        
        $html .= '<div class="grid-line" style="bottom: ' . $position . 'px;">';
        $html .= '<div class="grid-line-label">' . $value . '</div>';
        $html .= '</div>';
    }
    
    // Generar barras
    foreach ($data as $index => $item) {
        // Calcular altura exacta usando la misma área que el CSS
        $height = ($item['count'] / $maxValue) * $availableHeight;
        $heightPx = max($height, 5); // Altura mínima de 5px
        
        $color = $colors[$index % count($colors)];
        
        $html .= '<div class="bar-item">';
        $html .= '<div class="bar" style="height: ' . $heightPx . 'px; background-color: ' . $color . ';">';
        $html .= '<div class="bar-value">' . $item['count'] . '</div>';
        $html .= '</div>';
        $html .= '<div class="bar-label">' . htmlspecialchars($item['label']) . '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

// Función auxiliar para validar datos
function validateSectionData($section) {
    if (!isset($section['title']) || empty($section['title'])) {
        throw new Exception('Título de sección requerido');
    }
    
    if (!isset($section['type']) || !in_array($section['type'], ['table', 'chart', 'table-chart'])) {
        throw new Exception('Tipo de sección no válido');
    }
    
    if (empty($section['data']) || !is_array($section['data'])) {
        throw new Exception('Datos de sección requeridos');
    }
    
    return true;
}
?>
