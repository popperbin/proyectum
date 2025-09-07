<?php
require_once __DIR__ . "/../../config/auth.php";
requireLogin();

require_once __DIR__ . "/../../models/Lista.php";
require_once __DIR__ . "/../../models/Tarea.php";
require_once __DIR__ . "/../../models/Proyecto.php";
require_once __DIR__ . "/../../models/comentario.php";

$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    die("Proyecto no especificado.");
}

$listaModel = new Lista();
$tareaModel = new Tarea();
$proyectoModel = new Proyecto();
$comentarioModel = new Comentario();

// Obtener información del proyecto
$proyecto = $proyectoModel->obtenerPorId($proyecto_id);
if (!$proyecto) {
    die("Proyecto no encontrado");
}

// Obtener listas y tareas del proyecto
$listas = $listaModel->listarPorProyecto($proyecto_id);

// Obtener colaboradores del proyecto
$colaboradores = $proyectoModel->obtenerColaboradores($proyecto_id);
?>

<div class="container-fluid">
    <!-- Header del tablero -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">🎯 <?= htmlspecialchars($proyecto['nombre']) ?></h2>
            <p class="text-muted">Tablero de tareas estilo Trello</p>
        </div>
        <div>
            <a href="router.php?page=tareas/listar&proyecto_id=<?= $proyecto_id ?>" class="btn btn-outline-info me-2">
                📋 Vista Lista
            </a>
            <a href="router.php?page=proyectos/listar" class="btn btn-outline-secondary">
                ⬅️ Volver a Proyectos
            </a>
        </div>
    </div>

    <!-- Tablero Trello Style -->
    <div class="kanban-board" id="kanban-board">
        <?php foreach ($listas as $lista): ?>
            <div class="kanban-column" data-lista-id="<?= $lista['id'] ?>">
                <!-- Header de la columna -->
                <div class="kanban-header">
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($lista['nombre']) ?></h5>
                    <span class="badge bg-secondary"><?= count($tareaModel->listarPorLista($lista['id'])) ?></span>
                </div>

                <!-- Área de tareas con drop zone -->
                <div class="kanban-tasks" 
                     ondrop="drop(event)" 
                     ondragover="allowDrop(event)"
                     data-lista-id="<?= $lista['id'] ?>">
                    
                    <?php foreach ($tareaModel->listarPorLista($lista['id']) as $tarea): ?>
                        <div class="kanban-card" 
                             draggable="true" 
                             ondragstart="drag(event)" 
                             data-id="<?= $tarea['id'] ?>">
                            
                            <!-- Título de la tarea -->
                            <div class="card-title">
                                <strong><?= htmlspecialchars($tarea['nombre']) ?></strong>
                            </div>

                            <!-- Descripción -->
                            <?php if (!empty($tarea['descripcion'])): ?>
                                <div class="card-description">
                                    <?= htmlspecialchars(substr($tarea['descripcion'], 0, 80)) ?>...
                                </div>
                            <?php endif; ?>

                            <!-- Badges y etiquetas -->
                            <div class="card-labels mb-2">
                                <!-- Prioridad -->
                                <?php
                                $prioridadClass = match($tarea['prioridad']) {
                                    'urgente' => 'danger',
                                    'alta' => 'warning',
                                    'media' => 'info',
                                    'baja' => 'secondary',
                                    default => 'light'
                                };
                                ?>
                                <span class="badge bg-<?= $prioridadClass ?> me-1">
                                    <?= ucfirst($tarea['prioridad']) ?>
                                </span>

                                <!-- Fecha límite -->
                                <?php if (!empty($tarea['fecha_fin'])): ?>
                                    <?php
                                    $fechaFin = strtotime($tarea['fecha_fin']);
                                    $hoy = time();
                                    $diasRestantes = ($fechaFin - $hoy) / (60 * 60 * 24);
                                    $fechaClass = $diasRestantes < 0 ? 'danger' : ($diasRestantes < 3 ? 'warning' : 'success');
                                    ?>
                                    <span class="badge bg-<?= $fechaClass ?>">
                                        📅 <?= date('d/m', $fechaFin) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Asignado -->
                            <?php if (!empty($tarea['asignado_nombre'])): ?>
                                <div class="card-assignee mb-2">
                                    <div class="avatar">
                                        <?= strtoupper(substr($tarea['asignado_nombre'], 0, 2)) ?>
                                    </div>
                                    <small><?= htmlspecialchars($tarea['asignado_nombre']) ?></small>
                                </div>
                            <?php endif; ?>

                            <!-- Comentarios -->
                            <?php 
                            $comentarios = $comentarioModel->listarPorTarea($tarea['id']);
                            if (!empty($comentarios)): 
                            ?>
                                <div class="card-comments mb-2">
                                    <span class="badge bg-light text-dark">
                                        💬 <?= count($comentarios) ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Acciones -->
                            <div class="card-actions">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                            type="button" 
                                            data-bs-toggle="dropdown">
                                        ⚙️
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" 
                                               href="router.php?page=tareas/editar&id=<?= $tarea['id'] ?>&proyecto_id=<?= $proyecto_id ?>">
                                                ✏️ Editar
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" 
                                               href="controllers/TareaController.php?accion=eliminar&id=<?= $tarea['id'] ?>&proyecto_id=<?= $proyecto_id ?>"
                                               onclick="return confirm('¿Eliminar esta tarea?')">
                                                🗑️ Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botón para añadir tarea -->
                <div class="kanban-add-card">
                    <button class="btn btn-light w-100" 
                            onclick="mostrarFormulario(<?= $lista['id'] ?>)">
                        ➕ Añadir tarea
                    </button>

                    <!-- Formulario de nueva tarea -->
                    <div id="form-tarea-<?= $lista['id'] ?>" class="form-nueva-tarea" style="display:none;">
                        <div class="card mt-2">
                            <div class="card-body">
                                <form method="POST" action="controllers/TareaController.php?accion=crear">
                                    <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">
                                    <input type="hidden" name="lista_id" value="<?= $lista['id'] ?>">

                                    <div class="mb-2">
                                        <input type="text" 
                                               class="form-control" 
                                               name="nombre" 
                                               placeholder="Título de la tarea" 
                                               required>
                                    </div>

                                    <div class="mb-2">
                                        <textarea class="form-control" 
                                                  name="descripcion" 
                                                  rows="2" 
                                                  placeholder="Descripción (opcional)"></textarea>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <select class="form-select form-select-sm" name="asignado_a">
                                                <option value="">Sin asignar</option>
                                                <?php foreach ($colaboradores as $colab): ?>
                                                    <option value="<?= $colab['id'] ?>">
                                                        <?= htmlspecialchars($colab['nombres'] . " " . $colab['apellidos']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <select class="form-select form-select-sm" name="prioridad">
                                                <option value="baja">🟢 Baja</option>
                                                <option value="media" selected>🟡 Media</option>
                                                <option value="alta">🟠 Alta</option>
                                                <option value="urgente">🔴 Urgente</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <input type="date" 
                                               class="form-control form-control-sm" 
                                               name="fecha_fin"
                                               placeholder="Fecha límite">
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            💾 Guardar
                                        </button>
                                        <button type="button" 
                                                class="btn btn-secondary btn-sm" 
                                                onclick="ocultarFormulario(<?= $lista['id'] ?>)">
                                            ❌ Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Crear nueva lista -->
        <div class="kanban-column kanban-add-column">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <form method="POST" action="controllers/ListaController.php?accion=crear" class="w-100">
                        <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">
                        <div class="input-group">
                            <input type="text" 
                                   class="form-control" 
                                   name="nombre" 
                                   placeholder="Nueva lista" 
                                   required>
                            <button class="btn btn-primary" type="submit">➕</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS para el tablero Trello -->
<style>
.kanban-board {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: 70vh;
}

.kanban-column {
    min-width: 300px;
    max-width: 300px;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    height: fit-content;
    border: 1px solid #dee2e6;
}

.kanban-add-column {
    min-width: 280px;
    max-width: 280px;
    background-color: #e9ecef;
    border: 2px dashed #adb5bd;
}

.kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #dee2e6;
}

.kanban-tasks {
    min-height: 200px;
    margin-bottom: 1rem;
}

.kanban-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
    cursor: grab;
    transition: transform 0.2s, box-shadow 0.2s;
}

.kanban-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.kanban-card:active {
    cursor: grabbing;
}

.kanban-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.card-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.card-description {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
}

.card-labels {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.card-assignee {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.avatar {
    width: 24px;
    height: 24px;
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: bold;
}

.card-comments {
    display: flex;
    align-items: center;
}

.card-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.5rem;
}

.kanban-add-card button {
    border: 1px dashed #6c757d;
    color: #6c757d;
}

.kanban-add-card button:hover {
    background-color: #e9ecef;
    border-color: #495057;
    color: #495057;
}

.form-nueva-tarea {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Drop zone styles */
.kanban-tasks.drag-over {
    background-color: #e3f2fd;
    border: 2px dashed #2196f3;
    border-radius: 8px;
}
</style>

<!-- JavaScript para drag and drop -->
<script>
// Variables globales
let draggedElement = null;

// Funciones de drag and drop
function allowDrop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('drag-over');
}

function drag(ev) {
    draggedElement = ev.target;
    ev.target.classList.add('dragging');
    ev.dataTransfer.setData("text", ev.target.dataset.id);
}

function drop(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('drag-over');
    
    const tareaId = ev.dataTransfer.getData("text");
    const nuevaListaId = ev.currentTarget.dataset.listaId;
    
    if (draggedElement && nuevaListaId) {
        // Mover visualmente la tarea
        ev.currentTarget.appendChild(draggedElement);
        draggedElement.classList.remove('dragging');
        
        // Enviar actualización al servidor
        actualizarTareaLista(tareaId, nuevaListaId);
    }
}

// Funciones para formularios
function mostrarFormulario(listaId) {
    const form = document.getElementById(`form-tarea-${listaId}`);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    if (form.style.display === 'block') {
        const input = form.querySelector('input[name="nombre"]');
        input.focus();
    }
}

function ocultarFormulario(listaId) {
    document.getElementById(`form-tarea-${listaId}`).style.display = 'none';
}

// Actualizar tarea en el servidor
function actualizarTareaLista(tareaId, nuevaListaId) {
    fetch('controllers/TareaController.php?accion=mover', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `tarea_id=${tareaId}&lista_id=${nuevaListaId}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Error al mover tarea:', data.message);
            // Revertir el movimiento visual si hay error
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        location.reload();
    });
}

// Limpiar estados de drag cuando termine
document.addEventListener('dragend', function(e) {
    if (draggedElement) {
        draggedElement.classList.remove('dragging');
        draggedElement = null;
    }
    
    // Limpiar todas las zonas de drop
    document.querySelectorAll('.drag-over').forEach(el => {
        el.classList.remove('drag-over');
    });
});

// Shortcuts de teclado
document.addEventListener('keydown', function(e) {
    // Escape para cerrar formularios
    if (e.key === 'Escape') {
        document.querySelectorAll('.form-nueva-tarea').forEach(form => {
            form.style.display = 'none';
        });
    }
});
</script>