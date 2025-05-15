<?php
// index.php - Página principal del Dashboard Generator
$page_title = 'Dashboard Generator - Soporte Técnico';
include 'includes/header.php';
?>

<div class="container">
    <!-- Sección de Configuración General -->
    <div class="form-section">
        <h2 class="form-title">Configuración del Dashboard</h2>
        <div class="form-group">
            <label class="form-label">Período del Reporte:</label>
            <input type="text" id="reportPeriod" class="form-control" placeholder="ej: Primera Quincena Mayo 2025">
        </div>
        <div class="form-group">
            <label class="form-label">Título del Dashboard:</label>
            <input type="text" id="dashboardTitle" class="form-control" value="SOPORTE TÉCNICO E INFRAESTRUCTURA">
        </div>
    </div>

    <!-- Constructor de Secciones -->
    <div class="section-builder">
        <h2 class="form-title">Crear Secciones del Dashboard</h2>
        <p>Agrega las diferentes secciones que conformarán tu dashboard. Puedes crear tablas, gráficos o combinaciones de ambos.</p>
        
        <div class="section-type-buttons">
            <button id="addSectionBtn" class="btn btn-primary">+ Agregar Nueva Sección</button>
            <button id="clearAllBtn" class="btn btn-danger">🗑️ Limpiar Todo</button>
        </div>
        
        <!-- Contenedor para las secciones -->
        <div id="sectionsContainer">
            <!-- Las secciones se generarán dinámicamente aquí -->
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="form-section">
        <h2 class="form-title">Generar Dashboard</h2>
        <div class="section-type-buttons">
            <button id="generateBtn" class="btn btn-success btn-block">🔄 Generar Vista Previa</button>
            <button id="exportPdfBtn" class="btn btn-primary btn-block">📄 Exportar a PDF</button>
        </div>
    </div>

    <!-- Vista Previa del Dashboard -->
    <div class="dashboard-preview" id="dashboardPreview" style="display: none;">
        <h2 class="preview-title">Vista Previa del Dashboard</h2>
        <!-- El HTML generado aparecerá aquí -->
    </div>
</div>

<!-- Información de Ayuda -->
<div class="container">
    <div class="form-section">
        <h2 class="form-title">ℹ️ Instrucciones de Uso</h2>
        <div style="background-color: rgba(255, 255, 255, 0.1); padding: 1rem; border-radius: 4px;">
            <h4>Pasos para crear tu dashboard:</h4>
            <ol style="margin: 1rem 0; padding-left: 2rem; color: rgba(255, 255, 255, 0.9);">
                <li><strong>Configurar período:</strong> Ingresa el período del reporte (ej: "Primera Quincena Mayo 2025")</li>
                <li><strong>Agregar secciones:</strong> Click en "Agregar Nueva Sección" y elige el tipo</li>
                <li><strong>Llenar datos:</strong> Ingresa los datos de tus tablas manualmente</li>
                <li><strong>Configurar gráficos:</strong> Los gráficos se actualizan automáticamente</li>
                <li><strong>Vista previa:</strong> Click "Generar Vista Previa" para ver resultado</li>
                <li><strong>Exportar:</strong> Click "Exportar a PDF" para generar el archivo final</li>
            </ol>
            
            <h4>Tips:</h4>
            <ul style="margin: 1rem 0; padding-left: 2rem; color: rgba(255, 255, 255, 0.9);">
                <li>Los datos se guardan automáticamente en tu navegador</li>
                <li>Puedes crear múltiples secciones con diferentes tipos de gráficos</li>
                <li>El PDF se optimiza automáticamente para impresión</li>
                <li>Usa "Limpiar Todo" para empezar de nuevo</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
