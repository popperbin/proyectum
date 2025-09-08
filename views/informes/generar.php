<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['accion'] ?? '') === 'crear') {
    require_once __DIR__ . "/../../controllers/InformeController.php";
    $controller = new InformeController();
    $controller->crear($_POST);
    exit;
}


require_once __DIR__ . "/../../config/auth.php"; 
requireRole(["administrador", "gestor"]);

// Cargar controladores necesarios
require_once __DIR__ . "/../../controllers/ProyectoController.php";

$proyectoController = new ProyectoController();
$usuario = $_SESSION['usuario'];

// Obtener proyecto específico si se pasa por parámetro
$proyecto_id = $_GET['proyecto_id'] ?? $_GET['id'] ?? null;
$proyecto_seleccionado = null;

if ($proyecto_id) {
    $proyecto_seleccionado = $proyectoController->obtenerPorId($proyecto_id);
}

// Obtener todos los proyectos para el selector
$proyectos = $proyectoController->listar();
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📊 Crear Nuevo Informe</h2>
            <p class="text-muted">Genera un informe detallado del proyecto</p>
        </div>
        <div>
            <a href="router.php?page=informes/listar&proyecto_id=<?= $proyecto_id ?>" 
            class="btn btn-outline-secondary me-2">
                ⬅️ Volver a Informes
            </a>

            <a href="router.php?page=proyectos/listar" class="btn btn-outline-primary">
                📋 Ver Proyectos
            </a>
        </div>
    </div>

    <!-- Información del proyecto seleccionado -->
    <?php if ($proyecto_seleccionado): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    📋 Proyecto: <?= htmlspecialchars($proyecto_seleccionado['nombre']) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1">
                            <strong>Descripción:</strong> 
                            <?= htmlspecialchars($proyecto_seleccionado['descripcion'] ?? 'Sin descripción') ?>
                        </p>
                        <p class="mb-0">
                            <strong>Periodo:</strong>
                            <?= date('d/m/Y', strtotime($proyecto_seleccionado['fecha_inicio'])) ?>
                            <?php if ($proyecto_seleccionado['fecha_fin']): ?>
                                - <?= date('d/m/Y', strtotime($proyecto_seleccionado['fecha_fin'])) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-<?= 
                            $proyecto_seleccionado['estado'] === 'completado' ? 'success' : 
                            ($proyecto_seleccionado['estado'] === 'activo' ? 'primary' : 'warning') 
                        ?> fs-6">
                            <?= ucfirst($proyecto_seleccionado['estado']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulario de creación -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">📄 Información del Informe</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="router.php?page=informes/acciones&accion=crear" id="formInforme">
                <div class="row">
                    <!-- Información básica del informe -->
                    <div class="col-md-6">
                        <!-- Selector de proyecto -->
                        <div class="mb-3">
                            <label for="proyecto_id" class="form-label">
                                <strong>📋 Proyecto *</strong>
                            </label>
                            <select class="form-select" 
                                    id="proyecto_id" 
                                    name="proyecto_id" 
                                    required
                                    <?= $proyecto_seleccionado ? 'disabled' : '' ?>>
                                <?php if ($proyecto_seleccionado): ?>
                                    <option value="<?= $proyecto_seleccionado['id'] ?>" selected>
                                        <?= htmlspecialchars($proyecto_seleccionado['nombre']) ?>
                                    </option>
                                    <input type="hidden" name="proyecto_id" value="<?= $proyecto_seleccionado['id'] ?>">
                                <?php else: ?>
                                    <option value="">-- Seleccionar Proyecto --</option>
                                    <?php foreach ($proyectos as $proyecto): ?>
                                        <option value="<?= $proyecto['id'] ?>">
                                            <?= htmlspecialchars($proyecto['nombre']) ?>
                                            (<?= ucfirst($proyecto['estado']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Título del informe -->
                        <div class="mb-3">
                            <label for="titulo" class="form-label">
                                <strong>📄 Título del Informe *</strong>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="titulo" 
                                   name="titulo" 
                                   placeholder="Ej: Informe de progreso - Semana 1" 
                                   required>
                        </div>

                        <!-- Tipo de informe -->
                        <div class="mb-3">
                            <label for="tipo" class="form-label">
                                <strong>📊 Tipo de Informe *</strong>
                            </label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="progreso">📈 Progreso</option>
                                <option value="final">✅ Final</option>
                                <option value="riesgos">⚠️ Riesgos</option>
                                <option value="personalizado">🔧 Personalizado</option>
                            </select>
                            <div class="form-text">
                                El tipo determina el formato y contenido del PDF generado
                            </div>
                        </div>

                        <!-- Contenido principal -->
                        <div class="mb-3">
                            <label for="contenido" class="form-label">
                                <strong>📝 Contenido Principal *</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="contenido" 
                                      name="contenido" 
                                      rows="6"
                                      placeholder="Describe el contenido principal del informe..."
                                      required></textarea>
                            <div class="form-text">
                                Detalla los puntos principales que debe incluir el informe
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="col-md-6">
                        <!-- Comentarios -->
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">
                                <strong>💬 Comentarios</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="comentarios" 
                                      name="comentarios" 
                                      rows="4"
                                      placeholder="Comentarios adicionales sobre el proyecto..."></textarea>
                            <div class="form-text">
                                Observaciones generales o comentarios del periodo
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">
                                <strong>🔍 Observaciones</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="observaciones" 
                                      name="observaciones" 
                                      rows="4"
                                      placeholder="Observaciones técnicas, recomendaciones, próximos pasos..."></textarea>
                            <div class="form-text">
                                Notas técnicas, recomendaciones o puntos de mejora
                            </div>
                        </div>

                        <!-- Opciones avanzadas -->
                        <div class="card border-secondary">
                            <div class="card-header">
                                <h6 class="mb-0">⚙️ Opciones de Generación</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="incluir_estadisticas" 
                                           id="incluir_estadisticas" 
                                           value="1" 
                                           checked>
                                    <label class="form-check-label" for="incluir_estadisticas">
                                        📊 Incluir estadísticas del proyecto
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="incluir_tareas" 
                                           id="incluir_tareas" 
                                           value="1"
                                           checked>
                                    <label class="form-check-label" for="incluir_tareas">
                                        ✅ Incluir estado de tareas
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="router.php?page=informes/listar<?= $proyecto_id ? '&proyecto_id=' . $proyecto_id : '' ?>" 
                       class="btn btn-secondary">
                        ❌ Cancelar
                    </a>
                    <div>
                        <button type="button" class="btn btn-info me-2" onclick="previsualizarInforme()">
                            👁️ Vista Previa
                        </button>
                        <button type="submit" class="btn btn-success">
                            📄 Generar PDF y Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Plantillas por tipo de informe -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-header">
                    <h6 class="mb-0">💡 Guías por Tipo de Informe</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>📈 Progreso:</strong>
                            <ul class="small">
                                <li>Estado actual del proyecto</li>
                                <li>Tareas completadas</li>
                                <li>Problemas encontrados</li>
                                <li>Próximos pasos</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <strong>✅ Final:</strong>
                            <ul class="small">
                                <li>Resumen ejecutivo</li>
                                <li>Objetivos alcanzados</li>
                                <li>Entregables finales</li>
                                <li>Lecciones aprendidas</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <strong>⚠️ Riesgos:</strong>
                            <ul class="small">
                                <li>Identificación de riesgos</li>
                                <li>Evaluación de impacto</li>
                                <li>Medidas de mitigación</li>
                                <li>Plan de contingencia</li>
                            </ul>
                        </div>
                        <div class="col-md-3">
                            <strong>🔧 Personalizado:</strong>
                            <ul class="small">
                                <li>Formato libre</li>
                                <li>Contenido específico</li>
                                <li>Análisis particular</li>
                                <li>Reporte a medida</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botones de plantillas rápidas (insertar después del campo "tipo") -->
<div class="mb-3">
    <label class="form-label">
        <strong>🚀 Plantillas Rápidas</strong>
    </label>
    <div class="btn-group w-100" role="group">
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="aplicarPlantilla('progreso')">
            📈 Progreso
        </button>
        <button type="button" class="btn btn-outline-success btn-sm" onclick="aplicarPlantilla('final')">
            ✅ Final  
        </button>
        <button type="button" class="btn btn-outline-warning btn-sm" onclick="aplicarPlantilla('riesgos')">
            ⚠️ Riesgos
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="aplicarPlantilla('personalizado')">
            🔧 Personalizado
        </button>
    </div>
    <div class="form-text">
        Haz clic en una plantilla para cargar contenido sugerido y cambiar el tipo automáticamente
    </div>
</div>

<!-- Modal de vista previa -->
<div class="modal fade" id="modalPreview" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">👁️ Vista Previa del Informe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Contenido de vista previa se carga aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="document.getElementById('formInforme').submit()">
                    📄 Generar Informe
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para previsualizar el informe
function previsualizarInforme() {
    const form = document.getElementById('formInforme');
    const formData = new FormData(form);
    
    // Validar campos requeridos
    const proyecto_id = formData.get('proyecto_id');
    const titulo = formData.get('titulo');
    const contenido = formData.get('contenido');
    const tipo = formData.get('tipo');
    
    if (!proyecto_id || !titulo || !contenido) {
        alert('⚠️ Por favor completa todos los campos requeridos antes de la vista previa');
        return;
    }
    
    // Generar contenido de vista previa
    const proyectoNombre = document.querySelector('#proyecto_id option:checked').textContent;
    const tipoTexto = document.querySelector('#tipo option:checked').textContent;
    
    let previewHTML = `
        <div class="preview-container">
            <div class="text-center mb-4">
                <h3>📄 Vista Previa del Informe</h3>
                <hr>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>📋 Proyecto:</strong> ${proyectoNombre}
                </div>
                <div class="col-md-6">
                    <strong>📊 Tipo:</strong> ${tipoTexto}
                </div>
            </div>
            
            <div class="mb-3">
                <strong>📄 Título:</strong>
                <h4>${titulo}</h4>
            </div>
            
            <div class="mb-3">
                <strong>📝 Contenido Principal:</strong>
                <div class="border p-3 bg-light">
                    ${contenido.replace(/\n/g, '<br>')}
                </div>
            </div>
    `;
    
    // Agregar comentarios si existen
    const comentarios = formData.get('comentarios');
    if (comentarios && comentarios.trim()) {
        previewHTML += `
            <div class="mb-3">
                <strong>💬 Comentarios:</strong>
                <div class="border p-3 bg-light">
                    ${comentarios.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    }
    
    // Agregar observaciones si existen
    const observaciones = formData.get('observaciones');
    if (observaciones && observaciones.trim()) {
        previewHTML += `
            <div class="mb-3">
                <strong>🔍 Observaciones:</strong>
                <div class="border p-3 bg-light">
                    ${observaciones.replace(/\n/g, '<br>')}
                </div>
            </div>
        `;
    }
    
    // Agregar opciones seleccionadas
    const opciones = [];
    if (formData.get('incluir_estadisticas')) opciones.push('📊 Estadísticas del proyecto');
    if (formData.get('incluir_tareas')) opciones.push('✅ Estado de tareas');
    if (formData.get('incluir_timeline')) opciones.push('📅 Cronograma');
    
    if (opciones.length > 0) {
        previewHTML += `
            <div class="mb-3">
                <strong>⚙️ Incluir en el PDF:</strong>
                <ul class="mt-2">
                    ${opciones.map(opcion => `<li>${opcion}</li>`).join('')}
                </ul>
            </div>
        `;
    }
    
    previewHTML += `
            <div class="mt-4 p-3 bg-info bg-opacity-10 border border-info rounded">
                <small>
                    <strong>ℹ️ Información:</strong> Esta es una vista previa del contenido. 
                    El PDF final incluirá formato profesional, logos y estructura completa.
                </small>
            </div>
        </div>
    `;
    
    // Mostrar en el modal
    document.getElementById('previewContent').innerHTML = previewHTML;
    new bootstrap.Modal(document.getElementById('modalPreview')).show();
}

// Validaciones y mejoras del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formInforme');
    const tipoSelect = document.getElementById('tipo');
    const tituloInput = document.getElementById('titulo');
    const contenidoTextarea = document.getElementById('contenido');
    const proyectoSelect = document.getElementById('proyecto_id');
    
    // Auto-generar título basado en el tipo y proyecto
    function actualizarTituloSugerido() {
        const proyectoTexto = proyectoSelect.options[proyectoSelect.selectedIndex]?.text || '';
        const tipoTexto = tipoSelect.options[tipoSelect.selectedIndex]?.text || '';
        const fecha = new Date().toLocaleDateString('es-ES');
        
        if (proyectoTexto && tipoTexto && !tituloInput.value) {
            const proyectoNombre = proyectoTexto.split('(')[0].trim();
            tituloInput.placeholder = `Informe ${tipoTexto} - ${proyectoNombre} - ${fecha}`;
        }
    }
    
    // Plantillas de contenido según el tipo
    const plantillas = {
        'progreso': `ESTADO ACTUAL DEL PROYECTO:
- Porcentaje de avance: 
- Tareas completadas esta semana:
- Próximas actividades:

PROBLEMAS Y DESAFÍOS:
- Obstáculos encontrados:
- Soluciones implementadas:

RECURSOS Y EQUIPO:
- Estado del equipo de trabajo:
- Recursos utilizados:`,
        
        'final': `RESUMEN EJECUTIVO:
- Objetivos del proyecto:
- Resultados alcanzados:

ENTREGABLES FINALES:
- Lista de entregables completados:
- Calidad y especificaciones cumplidas:

EVALUACIÓN GENERAL:
- Éxitos del proyecto:
- Lecciones aprendidas:
- Recomendaciones futuras:`,
        
        'riesgos': `IDENTIFICACIÓN DE RIESGOS:
- Riesgos técnicos:
- Riesgos de cronograma:
- Riesgos de recursos:

EVALUACIÓN DE IMPACTO:
- Probabilidad de ocurrencia:
- Impacto en el proyecto:

MEDIDAS DE MITIGACIÓN:
- Acciones preventivas:
- Planes de contingencia:`,
        
        'personalizado': `CONTENIDO PERSONALIZADO:
- Objetivo específico del informe:
- Análisis detallado:
- Conclusiones y recomendaciones:`
    };
    
    // Actualizar plantilla cuando cambie el tipo
    tipoSelect.addEventListener('change', function() {
        actualizarTituloSugerido();
        
        if (!contenidoTextarea.value.trim()) {
            contenidoTextarea.value = plantillas[this.value] || '';
            contenidoTextarea.style.height = 'auto';
            contenidoTextarea.style.height = contenidoTextarea.scrollHeight + 'px';
        }
    });
    
    proyectoSelect.addEventListener('change', actualizarTituloSugerido);
    
    // Auto-resize de textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Validación del formulario antes de envío
    form.addEventListener('submit', function(e) {
        const requiredFields = ['proyecto_id', 'titulo', 'contenido', 'tipo'];
        let hasErrors = false;
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                hasErrors = true;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('⚠️ Por favor completa todos los campos requeridos');
            return false;
        }
        
        // Mostrar indicador de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '🔄 Generando PDF...';
        
        // Restaurar botón si hay error (opcional)
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 10000);
    });
    
    // Cargar plantilla inicial
    if (tipoSelect.value) {
        const event = new Event('change');
        tipoSelect.dispatchEvent(event);
    }
    
    // Contador de caracteres para áreas de texto grandes
    const addCharacterCounter = (textarea, maxChars = 1000) => {
        const counter = document.createElement('div');
        counter.className = 'form-text text-end';
        textarea.parentNode.appendChild(counter);
        
        const updateCounter = () => {
            const current = textarea.value.length;
            counter.textContent = `${current}/${maxChars} caracteres`;
            counter.className = `form-text text-end ${current > maxChars ? 'text-danger' : ''}`;
        };
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    };
    
    // Aplicar contador a contenido principal
    addCharacterCounter(contenidoTextarea, 2000);
    
    // Función para limpiar formulario
    window.limpiarFormulario = function() {
        if (confirm('¿Estás seguro de limpiar todos los campos?')) {
            form.reset();
            textareas.forEach(textarea => {
                textarea.style.height = 'auto';
            });
        }
    };
});

// Función para aplicar plantilla específica
function aplicarPlantilla(tipo) {
    const contenidoTextarea = document.getElementById('contenido');
    const tipoSelect = document.getElementById('tipo');
    
    tipoSelect.value = tipo;
    const event = new Event('change');
    tipoSelect.dispatchEvent(event);
}
</script>

<!-- Estilos adicionales -->
<style>
.preview-container {
    max-height: 70vh;
    overflow-y: auto;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.character-counter {
    font-size: 0.875em;
    color: #6c757d;
}

.character-counter.over-limit {
    color: #dc3545;
    font-weight: bold;
}

@media (max-width: 768px) {
    .preview-container {
        max-height: 50vh;
    }
}
</style>