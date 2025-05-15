<?php
// includes/functions.php - Funciones auxiliares del proyecto

// Función para sanitizar texto
function sanitizeText($text) {
    return htmlspecialchars(trim($text), ENT_QUOTES, 'UTF-8');
}

// Función para validar números
function validateNumber($value, $default = 0) {
    return is_numeric($value) ? floatval($value) : $default;
}

// Función para generar ID único
function generateUniqueId($prefix = 'item') {
    return $prefix . '_' . uniqid() . '_' . rand(1000, 9999);
}

// Función para formatear período de fecha
function formatPeriod($period) {
    if (empty($period)) {
        return date('F Y');
    }
    return sanitizeText($period);
}

// Función para crear directorio si no existe
function ensureDirectoryExists($path) {
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
    return $path;
}

// Función para obtener colores de gráficos
function getChartColors() {
    return [
        '#3f51b5', // Portal color
        '#ff9800', // Usuario color
        '#5bc2c1', // WFM color
        '#009688', // Tercería color
        '#4caf50', // Médica color
        '#795548', // App color
        '#e91e63', // Correo color
        '#5a6acf', // VPN color
        '#9c27b0', // Internet color
        '#ff5722', // Citas color
        '#2196f3', // Beneficiario color
        '#673ab7', // Historial color
        '#8bc34a', // Recetas color
        '#00bcd4'  // Videollamada color
    ];
}

// Función para calcular porcentajes
function calculatePercentages($data) {
    $total = array_sum(array_column($data, 'count'));
    if ($total == 0) return $data;
    
    foreach ($data as &$item) {
        $item['percentage'] = ($item['count'] / $total) * 100;
    }
    
    return $data;
}

// Función para validar estructura de datos de sección
function validateSectionStructure($section) {
    $required = ['id', 'type', 'title'];
    
    foreach ($required as $field) {
        if (!isset($section[$field]) || empty($section[$field])) {
            return false;
        }
    }
    
    // Validar tipo
    $validTypes = ['table', 'chart', 'table-chart'];
    if (!in_array($section['type'], $validTypes)) {
        return false;
    }
    
    return true;
}

// Función para limpiar y validar datos de tabla
function cleanTableData($data) {
    if (!is_array($data)) return [];
    
    $cleaned = [];
    foreach ($data as $row) {
        if (isset($row['label']) && !empty(trim($row['label']))) {
            $cleaned[] = [
                'label' => sanitizeText($row['label']),
                'count' => validateNumber($row['count']),
                'time' => validateNumber($row['time'])
            ];
        }
    }
    
    return $cleaned;
}

// Función para generar nombre de archivo seguro
function generateSafeFilename($title, $extension = 'html') {
    $safe = preg_replace('/[^a-zA-Z0-9-_]/', '_', $title);
    $safe = preg_replace('/_+/', '_', $safe);
    $safe = trim($safe, '_');
    $timestamp = date('Y-m-d_H-i-s');
    
    return strtolower($safe) . '_' . $timestamp . '.' . $extension;
}

// Función para registrar logs (simple)
function logActivity($message, $level = 'INFO') {
    $logFile = 'logs/activity.log';
    ensureDirectoryExists('logs');
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$level}: {$message}" . PHP_EOL;
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Función para validar y limpiar JSON
function parseJsonSafely($json) {
    $decoded = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON inválido: ' . json_last_error_msg());
    }
    
    return $decoded;
}

// Función para convertir datos a formato de exportación
function prepareDataForExport($sections) {
    $exportData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0',
        'sections' => []
    ];
    
    foreach ($sections as $section) {
        if (validateSectionStructure($section)) {
            $exportData['sections'][] = [
                'title' => sanitizeText($section['title']),
                'type' => $section['type'],
                'tableTitle' => sanitizeText($section['tableTitle'] ?? ''),
                'chartType' => $section['chartType'] ?? 'doughnut',
                'data' => cleanTableData($section['data'] ?? [])
            ];
        }
    }
    
    return $exportData;
}

// Función para verificar permisos de directorio
function checkDirectoryPermissions($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    return is_writable($path) && is_readable($path);
}

// Función para obtener información del sistema
function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s'),
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'upload_max_filesize' => ini_get('upload_max_filesize')
    ];
}

// Función para formatear tiempo en formato legible
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        return round($seconds / 60, 1) . 'm';
    } else {
        return round($seconds / 3600, 1) . 'h';
    }
}

// Función para escapar CSS
function escapeCss($value) {
    return preg_replace('/[^a-zA-Z0-9-_#]/', '', $value);
}

// Función para validar color hexadecimal
function isValidHexColor($color) {
    return preg_match('/^#[a-fA-F0-9]{6}$/', $color);
}
?>
