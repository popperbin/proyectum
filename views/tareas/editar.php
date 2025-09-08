<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["gestor", "administrador", "colaborador"]);

require_once __DIR__ . "/../../models/Tarea.php";
require_once __DIR__ . "/../../models/Proyecto.php";
require_once __DIR__ . "/../../models/Comentario.php";

$comentarioModel = new Comentario();
$tareaModel = new Tarea();
$proyectoModel = new Proyecto();

$tarea = $tareaModel->obtenerPorId($_GET['id']);
if (!$tarea) die("Tarea no encontrada");

$proyecto_id = $_GET['proyecto_id'] ?? $tarea['proyecto_id'];
$proyecto = $proyectoModel->obtenerPorId($proyecto_id);
$colaboradores = $proyectoModel->obtenerColaboradores($tarea['proyecto_id']);
$comentarios = $comentarioModel->listarPorTarea($tarea['id']);
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">✏️ Editar Tarea</h2>
            <p class="text-muted">
                Proyecto: <strong><?= htmlspecialchars($proyecto['nombre'] ?? 'Desconocido') ?></strong>
            </p>
        </div>
        <div>
            <a href="router.php?page=tareas/tablero&proyecto_id=<?= $proyecto_id ?>" class="btn btn-info me-2">
                🎯 Volver al Tablero
            </a>
            <a href="router.php?page=tareas/listar&proyecto_id=<?= $proyecto_id ?>" class="btn btn-outline-secondary">
                📋 Ver Lista
            </a>
        </div>
    </div>

    <!-- Navegación -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="router.php?page=proyectos/listar">📁 Proyectos</a>
            </li>
            <li class="breadcrumb-item">
                <a href="router.php?page=tareas/tablero&proyecto_id=<?= $proyecto_id ?>">🎯 Tablero</a>
            </li>
            <li class="breadcrumb-item active">
                ✏️ Editar Tarea
            </li>
        </ol>
    </nav>

    <div class="row">
        <!-- Formulario de edición -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">📝 <?= htmlspecialchars($tarea['nombre']) ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="router.php?page=tareas/acciones&accion=editar">
                        <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
                        <input type="hidden" name="proyecto_id" value="<?= $tarea['proyecto_id'] ?>">
                        <input type="hidden" name="lista_id" value="<?= $tarea['lista_id'] ?>">

                        <!-- Información básica -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="nombre" class="form-label">
                                    <strong>Título de la Tarea *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($tarea['nombre']) ?>" 
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <strong>Descripción</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4"><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
                        </div>

                        <!-- Asignación y estado -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="asignado_a" class="form-label">
                                    <strong>👤 Responsable</strong>
                                </label>
                                <select class="form-select" id="asignado_a" name="asignado_a">
                                    <option value="">-- Sin asignar --</option>
                                    <?php foreach ($colaboradores as $colab): ?>
                                        <option value="<?= $colab['id'] ?>" 
                                                <?= $tarea['asignado_a'] == $colab['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($colab['nombres'] . " " . $colab['apellidos']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label">
                                    <strong>Estado</strong>
                                </label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="pendiente" <?= $tarea['estado'] == "pendiente" ? "selected" : "" ?>>
                                        ⏳ Pendiente
                                    </option>
                                    <option value="en_progreso" <?= $tarea['estado'] == "en_progreso" ? "selected" : "" ?>>
                                        🔄 En Progreso
                                    </option>
                                    <option value="completado" <?= $tarea['estado'] == "completado" ? "selected" : "" ?>>
                                        ✅ Completado
                                    </option>
                                    <option value="cancelado" <?= $tarea['estado'] == "cancelado" ? "selected" : "" ?>>
                                        ❌ Cancelado
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Fechas y prioridad -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="fecha_inicio" class="form-label">
                                    <strong>📅 Fecha Inicio</strong>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="<?= $tarea['fecha_inicio'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_fin" class="form-label">
                                    <strong>📅 Fecha Límite</strong>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="<?= $tarea['fecha_fin'] ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="prioridad" class="form-label">
                                    <strong>Prioridad</strong>
                                </label>
                                <select class="form-select" id="prioridad" name="prioridad">
                                    <option value="baja" <?= $tarea['prioridad'] == "baja" ? "selected" : "" ?>>
                                        🟢 Baja
                                    </option>
                                    <option value="media" <?= $tarea['prioridad'] == "media" ? "selected" : "" ?>>
                                        🟡 Media
                                    </option>
                                    <option value="alta" <?= $tarea['prioridad'] == "alta" ? "selected" : "" ?>>
                                        🟠 Alta
                                    </option>
                                    <option value="urgente" <?= $tarea['prioridad'] == "urgente" ? "selected" : "" ?>>
                                        🔴 Urgente
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <a href="router.php?page=tareas/tablero&proyecto_id=<?= $proyecto_id ?>" 
                               class="btn btn-secondary">
                                ❌ Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                💾 Actualizar Tarea
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de comentarios -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">💬 Comentarios</h5>
                    <span class="badge bg-primary"><?= count($comentarios) ?></span>
                </div>
                
                <!-- Lista de comentarios -->
                <div class="card-body p-0">
                    <div class="comentarios-container" style="max-height: 300px; overflow-y: auto;">
                        <?php if (empty($comentarios)): ?>
                            <div class="text-center py-4 text-muted">
                                <p>💭 No hay comentarios aún</p>
                                <small>Sé el primero en comentar</small>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comentarios as $c): ?>
                                <div class="comentario-item p-3 border-bottom">
                                    <div class="d-flex align-items-start">
                                        <div class="avatar me-2">
                                            <?= strtoupper(substr($c['autor'], 0, 2)) ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold"><?= htmlspecialchars($c['autor']) ?></div>
                                            <div class="comentario-texto">
                                                <?= nl2br(htmlspecialchars($c['comentario'])) ?>
                                            </div>
                                            <small class="text-muted">
                                                <?= isset($c['fecha_creacion']) ? date('d/m/Y H:i', strtotime($c['fecha_creacion'])) : '' ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Formulario para nuevo comentario -->
                <div class="card-footer">
                    <form method="POST" action="router.php?page=tareas/acciones&accion=comentario" class="comentario-form">
                        <input type="hidden" name="tarea_id" value="<?= $tarea['id'] ?>">
                        <input type="hidden" name="proyecto_id" value="<?= $tarea['proyecto_id'] ?>">
                        <div class="input-group">
                            <textarea class="form-control" 
                                      name="comentario" 
                                      placeholder="Escribe un comentario..." 
                                      rows="2"
                                      required></textarea>
                            <button class="btn btn-primary" type="submit">
                                📤
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel de información -->
            <div class="card shadow-sm mt-3">
                <div class="card-header">
                    <h6 class="mb-0">📊 Información de la Tarea</h6>
                </div>
                <div class="card-body">
                    <div class="info-item mb-2">
                        <strong>ID:</strong> #<?= $tarea['id'] ?>
                    </div>
                    <div class="info-item mb-2">
                        <strong>Creada:</strong> 
                        <?= isset($tarea['fecha_creacion']) ? date('d/m/Y', strtotime($tarea['fecha_creacion'])) : 'N/A' ?>
                    </div>
                    <div class="info-item mb-2">
                        <strong>Última modificación:</strong> 
                        <?= isset($tarea['fecha_modificacion']) ? date('d/m/Y H:i', strtotime($tarea['fecha_modificacion'])) : 'N/A' ?>
                    </div>
                    <div class="info-item">
                        <strong>Lista:</strong> 
                        <?= htmlspecialchars($tarea['lista_nombre'] ?? 'Sin lista') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS adicional para comentarios -->
<style>
.avatar {
    width: 32px;
    height: 32px;
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    flex-shrink: 0;
}

.comentario-item:hover {
    background-color: #f8f9fa;
}

.comentario-texto {
    font-size: 0.9rem;
    margin: 0.25rem 0;
    line-height: 1.4;
}

.comentarios-container {
    border-top: 1px solid #dee2e6;
}

.comentario-form textarea {
    resize: none;
    border-radius: 0.375rem 0 0 0.375rem;
}

.info-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

/* Validación de fechas */
.form-control:invalid {
    border-color: #dc3545;
}

.form-control:valid {
    border-color: #198754;
}
</style>

<!-- JavaScript para validaciones y UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    function validarFechas() {
        if (fechaInicio.value && fechaFin.value && fechaFin.value < fechaInicio.value) {
            fechaFin.setCustomValidity('La fecha límite no puede ser anterior a la fecha de inicio');
        } else {
            fechaFin.setCustomValidity('');
        }
    }
    
    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);
    
    // Auto-resize del textarea de comentarios
    const comentarioTextarea = document.querySelector('.comentario-form textarea');
    if (comentarioTextarea) {
        comentarioTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Scroll automático en comentarios
    const comentariosContainer = document.querySelector('.comentarios-container');
    if (comentariosContainer && comentariosContainer.children.length > 0) {
        comentariosContainer.scrollTop = comentariosContainer.scrollHeight;
    }
    
    // Confirmar cambios antes de salir si hay modificaciones
    const form = document.querySelector('form');
    const originalData = new FormData(form);
    
    window.addEventListener('beforeunload', function(e) {
        const currentData = new FormData(form);
        let hasChanges = false;
        
        for (let [key, value] of currentData.entries()) {
            if (originalData.get(key) !== value) {
                hasChanges = true;
                break;
            }
        }
        
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Remover evento beforeunload al enviar el formulario
    form.addEventListener('submit', function() {
        window.removeEventListener('beforeunload', arguments.callee);
    });
});
</script>