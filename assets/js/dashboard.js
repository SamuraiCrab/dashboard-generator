// dashboard.js - Lógica principal del dashboard

// Configuración global
const DashboardApp = {
    sections: [],
    chartInstances: {},
    
    // Inicialización
    init() {
        this.setupEventListeners();
        this.loadSavedData();
    },
    
    // Event listeners
    setupEventListeners() {
        // Botón agregar sección
        document.getElementById('addSectionBtn')?.addEventListener('click', () => {
            this.showSectionModal();
        });
        
        // Botón generar dashboard
        document.getElementById('generateBtn')?.addEventListener('click', () => {
            this.generateDashboard();
        });
        
        // Botón exportar PDF
        document.getElementById('exportPdfBtn')?.addEventListener('click', () => {
            this.exportToPdf();
        });
        
        // Botón limpiar todo
        document.getElementById('clearAllBtn')?.addEventListener('click', () => {
            this.clearAll();
        });
    },
    
    // Mostrar modal para agregar sección
    showSectionModal() {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <h3>Agregar Nueva Sección</h3>
                <div class="form-group">
                    <label>Tipo de Sección:</label>
                    <select id="sectionType" class="form-control">
                        <option value="table">Solo Tabla</option>
                        <option value="chart">Solo Gráfico</option>
                        <option value="table-chart">Tabla + Gráfico</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Título de la Sección:</label>
                    <input type="text" id="sectionTitle" class="form-control" placeholder="ej: JIRA Service Management">
                </div>
                <div class="modal-actions">
                    <button class="btn btn-primary" onclick="DashboardApp.addSection()">Agregar</button>
                    <button class="btn" onclick="DashboardApp.closeModal()">Cancelar</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    },
    
    // Agregar nueva sección
    addSection() {
        const type = document.getElementById('sectionType').value;
        const title = document.getElementById('sectionTitle').value;
        
        if (!title.trim()) {
            alert('Por favor ingresa un título para la sección');
            return;
        }
        
        const sectionId = 'section_' + Date.now();
        const section = {
            id: sectionId,
            type: type,
            title: title,
            data: []
        };
        
        this.sections.push(section);
        this.renderSection(section);
        this.closeModal();
        this.saveData();
    },
    
    // Renderizar sección
    renderSection(section) {
        const container = document.getElementById('sectionsContainer');
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'section-container';
        sectionDiv.id = section.id;
        
        let html = `
            <div class="section-header">
                <h3 class="section-title">${section.title}</h3>
                <div class="section-controls">
                    <button class="btn btn-small" onclick="DashboardApp.editSection('${section.id}')">Editar</button>
                    <button class="btn btn-small btn-danger" onclick="DashboardApp.removeSection('${section.id}')">Eliminar</button>
                </div>
            </div>
        `;
        
        if (section.type === 'table' || section.type === 'table-chart') {
            html += this.renderTableBuilder(section);
        }
        
        if (section.type === 'chart' || section.type === 'table-chart') {
            html += this.renderChartBuilder(section);
        }
        
        sectionDiv.innerHTML = html;
        container.appendChild(sectionDiv);
        
        // Setup table builder for this section
        this.setupTableBuilder(section.id);
    },
    
    // Renderizar constructor de tabla
    renderTableBuilder(section) {
        return `
            <div class="table-builder" id="tableBuilder_${section.id}">
                <h4>Configurar Tabla</h4>
                <div class="form-group">
                    <label>Título de la Tabla:</label>
                    <input type="text" class="form-control" id="tableTitle_${section.id}" 
                           value="${section.tableTitle || ''}" placeholder="ej: Promedios de atención por estados">
                </div>
                <div class="form-group">
                    <label>Columnas:</label>
                    <div class="columns-setup" id="columnsSetup_${section.id}">
                        <div class="table-row">
                            <input type="text" class="table-input" placeholder="Estado" value="Estado">
                            <input type="text" class="table-input" placeholder="Cantidad" value="Cantidad">
                            <input type="text" class="table-input" placeholder="Tiempo Promedio" value="Tiempo Promedio">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Datos:</label>
                    <div class="table-data" id="tableData_${section.id}">
                        <!-- Los datos se cargarán aquí -->
                    </div>
                    <button class="btn btn-small" onclick="DashboardApp.addTableRow('${section.id}')">+ Agregar Fila</button>
                </div>
            </div>
        `;
    },
    
    // Renderizar constructor de gráfico
    renderChartBuilder(section) {
        return `
            <div class="chart-builder" id="chartBuilder_${section.id}">
                <h4>Configurar Gráfico</h4>
                <div class="form-group">
                    <label>Tipo de Gráfico:</label>
                    <select class="form-control" id="chartType_${section.id}" onchange="DashboardApp.updateChart('${section.id}')">
                        <option value="doughnut">Gráfico de Dona</option>
                        <option value="bar">Gráfico de Barras</option>
                        <option value="pie">Gráfico Circular</option>
                    </select>
                </div>
                
                <!-- Entrada de datos para gráfico solo -->
                <div class="form-group">
                    <label>Datos del Gráfico:</label>
                    <div class="table-data" id="chartData_${section.id}">
                        <!-- Los datos se cargarán aquí -->
                    </div>
                    <button class="btn btn-small" onclick="DashboardApp.addChartDataRow('${section.id}')">+ Agregar Dato</button>
                </div>
                
                <div class="chart-preview" id="chartPreview_${section.id}">
                    <canvas id="chart_${section.id}" width="400" height="400"></canvas>
                </div>
            </div>
        `;
    },
    
    // Setup table builder
    setupTableBuilder(sectionId) {
        // Agregar fila inicial si no existe para tabla
        const tableContainer = document.getElementById(`tableData_${sectionId}`);
        if (tableContainer && tableContainer.children.length === 0) {
            this.addTableRow(sectionId);
        }
        
        // Agregar fila inicial si no existe para gráfico
        const chartContainer = document.getElementById(`chartData_${sectionId}`);
        if (chartContainer && chartContainer.children.length === 0) {
            this.addChartDataRow(sectionId);
        }
    },
    
    // Agregar fila a tabla
    addTableRow(sectionId) {
        const container = document.getElementById(`tableData_${sectionId}`);
        const rowIndex = container.children.length;
        
        const rowDiv = document.createElement('div');
        rowDiv.className = 'table-row';
        rowDiv.innerHTML = `
            <input type="text" class="table-input" placeholder="Estado ${rowIndex + 1}" 
                   data-column="0" onchange="DashboardApp.updateChart('${sectionId}')">
            <input type="number" class="table-input" placeholder="0" 
                   data-column="1" onchange="DashboardApp.updateChart('${sectionId}')">
            <input type="number" class="table-input" placeholder="0.0" step="0.01" 
                   data-column="2" onchange="DashboardApp.updateChart('${sectionId}')">
            <button class="btn btn-small btn-danger" onclick="DashboardApp.removeTableRow(this)">-</button>
        `;
        
        container.appendChild(rowDiv);
        this.updateChart(sectionId);
    },
    
    // Agregar fila para datos de gráfico solo
    addChartDataRow(sectionId) {
        const container = document.getElementById(`chartData_${sectionId}`);
        const rowIndex = container.children.length;
        
        const rowDiv = document.createElement('div');
        rowDiv.className = 'table-row';
        rowDiv.innerHTML = `
            <input type="text" class="table-input" placeholder="Categoría ${rowIndex + 1}" 
                   data-column="0" onchange="DashboardApp.updateChart('${sectionId}')">
            <input type="number" class="table-input" placeholder="0" 
                   data-column="1" onchange="DashboardApp.updateChart('${sectionId}')">
            <button class="btn btn-small btn-danger" onclick="DashboardApp.removeChartDataRow(this)">-</button>
        `;
        
        container.appendChild(rowDiv);
        this.updateChart(sectionId);
    },
    
    // Eliminar fila de datos de gráfico
    removeChartDataRow(button) {
        const row = button.parentElement;
        const sectionId = row.closest('.section-container').id;
        row.remove();
        this.updateChart(sectionId);
    },
    
    // Eliminar fila de tabla
    removeTableRow(button) {
        const row = button.parentElement;
        const sectionId = row.closest('.section-container').id;
        row.remove();
        this.updateChart(sectionId);
    },
    
    // Actualizar gráfico basado en datos de tabla
    updateChart(sectionId) {
        const tableData = this.getTableData(sectionId);
        if (tableData.length === 0) return;
        
        const chartType = document.getElementById(`chartType_${sectionId}`)?.value || 'doughnut';
        const canvas = document.getElementById(`chart_${sectionId}`);
        if (!canvas) return;
        
        // Destruir gráfico existente
        if (this.chartInstances[sectionId]) {
            this.chartInstances[sectionId].destroy();
        }
        
        // Crear nuevo gráfico
        this.createChart(sectionId, chartType, tableData);
    },
    
    // Obtener datos de tabla
    getTableData(sectionId) {
        // Primero buscar en tableData, luego en chartData
        let container = document.getElementById(`tableData_${sectionId}`);
        if (!container) {
            container = document.getElementById(`chartData_${sectionId}`);
        }
        
        if (!container) return [];
        
        const rows = container.querySelectorAll('.table-row');
        const data = [];
        
        rows.forEach(row => {
            const inputs = row.querySelectorAll('.table-input');
            if (inputs.length >= 2 && inputs[0].value.trim()) {
                data.push({
                    label: inputs[0].value.trim(),
                    count: parseInt(inputs[1].value) || 0,
                    time: parseFloat(inputs[2]?.value) || 0
                });
            }
        });
        
        return data;
    },
    
    // Crear gráfico
    createChart(sectionId, type, data) {
        const canvas = document.getElementById(`chart_${sectionId}`);
        const ctx = canvas.getContext('2d');
        
        // Colores predefinidos
        const colors = [
            '#3f51b5', '#ff9800', '#5bc2c1', '#009688', '#4caf50',
            '#795548', '#e91e63', '#5a6acf', '#9c27b0', '#ff5722'
        ];
        
        const chartData = {
            labels: data.map(item => item.label),
            datasets: [{
                data: data.map(item => item.count),
                backgroundColor: colors.slice(0, data.length),
                borderWidth: 2,
                borderColor: '#1e3a5c'
            }]
        };
        
        const config = {
            type: type,
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#ffffff',
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        };
        
        // Configuraciones específicas por tipo de gráfico
        if (type === 'doughnut') {
            config.options.cutout = '60%';
        }
        
        if (type === 'bar') {
            config.options.scales = {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    }
                },
                x: {
                    ticks: {
                        color: '#ffffff'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)'
                    }
                }
            };
        }
        
        this.chartInstances[sectionId] = new Chart(ctx, config);
    },
    
    // Cerrar modal
    closeModal() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            modal.remove();
        }
    },
    
    // Remover sección
    removeSection(sectionId) {
        if (confirm('¿Estás seguro de que quieres eliminar esta sección?')) {
            // Destruir gráfico si existe
            if (this.chartInstances[sectionId]) {
                this.chartInstances[sectionId].destroy();
                delete this.chartInstances[sectionId];
            }
            
            // Remover del DOM
            document.getElementById(sectionId).remove();
            
            // Remover de array
            this.sections = this.sections.filter(section => section.id !== sectionId);
            
            this.saveData();
        }
    },
    
    // Generar dashboard
    generateDashboard() {
        // Obtener configuración del dashboard
        const period = document.getElementById('reportPeriod')?.value || 'Primera Quincena Mayo 2025';
        const title = document.getElementById('dashboardTitle')?.value || 'SOPORTE TÉCNICO E INFRAESTRUCTURA';
        
        // Recopilar datos de todas las secciones
        this.sections.forEach(section => {
            section.data = this.getTableData(section.id);
            section.tableTitle = document.getElementById(`tableTitle_${section.id}`)?.value || '';
            
            // IMPORTANTE: Obtener el chartType correctamente
            const chartTypeElement = document.getElementById(`chartType_${section.id}`);
            section.chartType = chartTypeElement ? chartTypeElement.value : 'doughnut';
            
            console.log('Sección:', section.title, 'Tipo:', section.chartType, 'Datos:', section.data);
        });
        
        // Enviar datos al servidor para generar HTML
        const formData = new FormData();
        formData.append('action', 'generate');
        formData.append('sections', JSON.stringify(this.sections));
        formData.append('period', period);
        formData.append('dashboard_title', title);
        
        fetch('process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar preview del dashboard
                this.showDashboardPreview(data.html);
            } else {
                alert('Error al generar dashboard: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    },
    
    // Mostrar preview del dashboard
    showDashboardPreview(html) {
        const previewContainer = document.getElementById('dashboardPreview');
        previewContainer.innerHTML = html;
        previewContainer.style.display = 'block';
        
        // Scroll to preview
        previewContainer.scrollIntoView({ behavior: 'smooth' });
    },
    
    // Exportar a PDF
    exportToPdf() {
        if (this.sections.length === 0) {
            alert('Debes crear al menos una sección antes de exportar');
            return;
        }
        
        // Generar primero
        this.generateDashboard();
        
        // Después de un breve delay, abrir ventana de impresión
        setTimeout(() => {
            // Crear ventana temporal solo con el dashboard
            const printWindow = window.open('', '_blank');
            const dashboardContent = document.getElementById('dashboardPreview').innerHTML;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Dashboard - Soporte Técnico</title>
                    <link rel="stylesheet" href="assets/css/style.css">
                    <style>
                        body { background: white; color: black; }
                        .main-content { padding: 0; }
                    </style>
                </head>
                <body>
                    ${dashboardContent}
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.print();
        }, 1000);
    },
    
    // Limpiar todo
    clearAll() {
        if (confirm('¿Estás seguro de que quieres eliminar todas las secciones?')) {
            // Destruir todos los gráficos
            Object.values(this.chartInstances).forEach(chart => chart.destroy());
            this.chartInstances = {};
            
            // Limpiar arrays y DOM
            this.sections = [];
            document.getElementById('sectionsContainer').innerHTML = '';
            document.getElementById('dashboardPreview').innerHTML = '';
            document.getElementById('dashboardPreview').style.display = 'none';
            
            this.saveData();
        }
    },
    
    // Guardar datos en localStorage
    saveData() {
        localStorage.setItem('dashboardData', JSON.stringify({
            sections: this.sections,
            timestamp: Date.now()
        }));
    },
    
    // Cargar datos guardados
    loadSavedData() {
        const saved = localStorage.getItem('dashboardData');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                this.sections = data.sections || [];
                
                // Renderizar secciones guardadas
                this.sections.forEach(section => {
                    this.renderSection(section);
                    this.loadSectionData(section);
                });
            } catch (error) {
                console.error('Error loading saved data:', error);
            }
        }
    },
    
    // Cargar datos de sección guardada
    loadSectionData(section) {
        // Cargar título de tabla
        const tableTitleInput = document.getElementById(`tableTitle_${section.id}`);
        if (tableTitleInput && section.tableTitle) {
            tableTitleInput.value = section.tableTitle;
        }
        
        // Cargar tipo de gráfico
        const chartTypeSelect = document.getElementById(`chartType_${section.id}`);
        if (chartTypeSelect && section.chartType) {
            chartTypeSelect.value = section.chartType;
        }
        
        // Cargar datos de tabla
        if (section.data && section.data.length > 0) {
            const tableContainer = document.getElementById(`tableData_${section.id}`);
            const chartContainer = document.getElementById(`chartData_${section.id}`);
            
            if (tableContainer) {
                tableContainer.innerHTML = ''; // Limpiar filas existentes
                
                section.data.forEach(item => {
                    this.addTableRow(section.id);
                    const lastRow = tableContainer.lastElementChild;
                    const inputs = lastRow.querySelectorAll('.table-input');
                    inputs[0].value = item.label;
                    inputs[1].value = item.count;
                    if (inputs[2]) inputs[2].value = item.time;
                });
            }
            
            if (chartContainer) {
                chartContainer.innerHTML = ''; // Limpiar filas existentes
                
                section.data.forEach(item => {
                    this.addChartDataRow(section.id);
                    const lastRow = chartContainer.lastElementChild;
                    const inputs = lastRow.querySelectorAll('.table-input');
                    inputs[0].value = item.label;
                    inputs[1].value = item.count;
                });
            }
            
            this.updateChart(section.id);
        }
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    DashboardApp.init();
});
