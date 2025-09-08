<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["administrador", "gestor"]);

require_once __DIR__ . "/../../models/Proyecto.php";
require_once __DIR__ . "/../../controllers/UsuarioController.php";

$proyectoModel = new Proyecto();
$usuarioController = new UsuarioController();

$proyecto_id = $_GET['id'] ?? null;
if (!$proyecto_id) {
    header("Location: router.php?page=proyectos/listar");
    exit;
}

$proyecto = $proyectoModel->obtenerPorId($proyecto_id);
if (!$proyecto) {
    echo '<div class="alert alert-danger">🚫 Proyecto no encontrado</div>';
    echo '<a href="router.php?page=proyectos/listar" class="btn btn-primary">Volver a proyectos</a>';
    exit;
}

// Obtener usuarios para dropdowns
$todosUsuarios = $usuarioController->listar();
$gestores = array_filter($todosUsuarios, fn($u) => in_array($u['rol'], ['administrador', 'gestor']));
$colaboradores = array_filter($todosUsuarios, fn($u) => $u['rol'] === 'colaborador');
$clientes = array_filter($todosUsuarios, fn($u) => $u['rol'] === 'cliente');

// Obtener colaboradores actuales del proyecto
$colaboradoresActuales = $proyectoModel->obtenerColaboradores($proyecto_id);
$colaboradoresIds = array_column($colaboradoresActuales, 'id');
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">✏️ Editar Proyecto</h2>
            <p class="text-muted">Modifica la información del proyecto: <strong><?= htmlspecialchars($proyecto['nombre']) ?></strong></p>
        </div>
        <div>
            <a href="router.php?page=proyectos/detalle&id=<?= $proyecto_id ?>" class="btn btn-info me-2">
                👁️ Ver Detalle
            </a>
            <a href="router.php?page=proyectos/listar" class="btn btn-outline-secondary">
                ⬅️ Volver a Proyectos
            </a>
        </div>
    </div>

    <!-- Navegación -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="router.php?page=proyectos/listar">📁 Proyectos</a>
            </li>
            <li class="breadcrumb-item active">
                ✏️ Editar: <?= htmlspecialchars($proyecto['nombre']) ?>
            </li>
        </ol>
    </nav>

    <div class="row">
        <!-- Formulario principal -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">📝 Información del Proyecto</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="controllers/ProyectoController.php?accion=editar&id=<?= $proyecto['id'] ?>">
                        <!-- Información básica -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="nombre" class="form-label">
                                    <strong>Nombre del Proyecto *</strong>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($proyecto['nombre']) ?>" 
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
                                      rows="4"><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>
                        </div>

                        <!-- Fechas -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fecha_inicio" class="form-label">
                                    <strong>📅 Fecha de Inicio</strong>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_inicio" 
                                       name="fecha_inicio" 
                                       value="<?= $proyecto['fecha_inicio'] ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_fin" class="form-label">
                                    <strong>📅 Fecha de Fin</strong>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_fin" 
                                       name="fecha_fin" 
                                       value="<?= $proyecto['fecha_fin'] ?>">
                            </div>
                        </div>

                        <!-- Asignaciones -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gestor_id" class="form-label">
                                    <strong>👨‍💼 Gestor del Proyecto</strong>
                                </label>
                                <select class="form-select" id="gestor_id" name="gestor_id">
                                    <option value="">-- Sin gestor asignado --</option>
                                    <?php foreach ($gestores as $gestor): ?>
                                        <option value="<?= $gestor['id'] ?>" 
                                                <?= $proyecto['gestor_id'] == $gestor['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($gestor['nombres'] . ' ' . $gestor['apellidos']) ?>
                                            (<?= ucfirst($gestor['rol']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="cliente_id" class="form-label">
                                    <strong>👤 Cliente</strong>
                                </label>
                                <select class="form-select" id="cliente_id" name="cliente_id">
                                    <option value="">-- Sin cliente asignado --</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente['id'] ?>" 
                                                <?= $proyecto['cliente_id'] == $cliente['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?>
                                            (<?= htmlspecialchars($cliente['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mb-4">
                            <label for="estado" class="form-label">
                                <strong>Estado del Proyecto</strong>
                            </label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="planificacion" <?= $proyecto['estado'] == "planificacion" ? "selected" : "" ?>>
                                    🟡 Planificación
                                </option>
                                <option value="activo" <?= $proyecto['estado'] == "activo" ? "selected" : "" ?>>
                                    🟢 Activo
                                </option>
                                <option value="en_pausa" <?= $proyecto['estado'] == "en_pausa" ? "selected" : "" ?>>
                                    ⏸️ En Pausa
                                </option>
                                <option value="completado" <?= $proyecto['estado'] == "completado" ? "selected" : "" ?>>
                                    ✅ Completado
                                </option>
                                <option value="cancelado" <?= $proyecto['estado'] == "cancelado" ? "selected" : "" ?>>
                                    ❌ Cancelado
                                </option>
                            </select>
                        </div>

                        <!-- Colaboradores -->
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>👥 Colaboradores del Proyecto</strong>
                                <small class="text-muted">(Selecciona los colaboradores que trabajarán en este proyecto)</small>
                            </label>
                            <div class="colaboradores-grid border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php if (!empty($colaboradores)): ?>
                                    <div class="row">
                                        <?php foreach ($colaboradores as $colaborador): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="colaboradores[]" 
                                                           value="<?= $colaborador['id'] ?>"
                                                           id="colab_<?= $colaborador['id'] ?>"
                                                           <?= in_array($colaborador['id'], $colaboradoresIds) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="colab_<?= $colaborador['id'] ?>">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar me-2">
                                                                <?= strtoupper(substr($colaborador['nombres'], 0, 2)) ?>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold">
                                                                    <?= htmlspecialchars($colaborador['nombres'] . ' ' . $colaborador['apellidos']) ?>
                                                                </div>
                                                                <small class="text-muted">
                                                                    <?= htmlspecialchars($colaborador['email']) ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-3">
                                        <p>📭 No hay colaboradores disponibles</p>
                                        <small>Los colaboradores deben ser creados desde el módulo de usuarios</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <a href="router.php?page=proyectos/listar" class="btn btn-secondary">
                                ❌ Cancelar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-success">
                                    💾 Actualizar Proyecto
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel lateral de información -->
        <div class="col-md-4">
            <!-- Información actual -->
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="mb-0">📊 Información Actual</h6>
                </div>
                <div class="card-body">
                    <div class="info-item mb-2">
                        <strong>ID:</strong> #<?= $proyecto['id'] ?>
                    </div>
                    <div class="info-item mb-2">
                        <strong>Creado:</strong> 
                        <?= isset($proyecto['fecha_creacion']) ? date('d/m/Y', strtotime($proyecto['fecha_creacion'])) : 'N/A' ?>
                    </div>
                    <div class="info-item mb-2">
                        <strong>Estado actual:</strong>
                        <?php
                        $estadoClass = match($proyecto['estado']) {
                            'activo' => 'bg-success',
                            'planificacion' => 'bg-warning text-dark',
                            'en_pausa' => 'bg-secondary',
                            'completado' => 'bg-primary',
                            'cancelado' => 'bg-danger',
                            default => 'bg-light text-dark'
                        };
                        ?>
                        <span class="badge <?= $estadoClass ?>">
                            <?= ucfirst($proyecto['estado']) ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <strong>Colaboradores actuales:</strong> 
                        <?= count($colaboradoresActuales) ?>
                    </div>
                </div>
            </div>

            <!-- Colaboradores actuales -->
            <?php if (!empty($colaboradoresActuales)): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">👥 Colaboradores Actuales</h6>
                    </div>
                    <div class="card-body p-2">
                        <?php foreach ($colaboradoresActuales as $colab): ?>
                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                <div class="avatar me-2">
                                    <?= strtoupper(substr($colab['nombres'], 0, 2)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">
                                        <?= htmlspecialchars($colab['nombres'] . ' ' . $colab['apellidos']) ?>
                                    </div>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($colab['email']) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Acciones rápidas -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">⚡ Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="router.php?page=tareas/tablero&proyecto_id=<?= $proyecto_id ?>" 
                           class="btn btn-info btn-sm">
                            🎯 Ver Tablero de Tareas
                        </a>
                        <a href="router.php?page=informes/generar&proyecto_id=<?= $proyecto_id ?>" 
                           class="btn btn-success btn-sm">
                            📊 Generar Informe
                        </a>
                        <?php if ($proyecto['estado'] === 'activo'): ?>
                            <a href="controllers/ProyectoController.php?accion=pausar&id=<?= $proyecto_id ?>" 
                               class="btn btn-warning btn-sm"
                               onclick="return confirm('¿Pausar este proyecto?')">
                               ⏸️ Pausar Proyecto
                            </a>
                        <?php elseif ($proyecto['estado'] === 'en_pausa'): ?>
                            <a href="controllers/ProyectoController.php?accion=reanudar&id=<?= $proyecto_id ?>" 
                               class="btn btn-success btn-sm"
                               onclick="return confirm('¿Reanudar este proyecto?')">
                               ▶️ Reanudar Proyecto
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS adicional -->
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

.colaboradores-grid .form-check {
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: background-color 0.2s;
}

.colaboradores-grid .form-check:hover {
    background-color: #f8f9fa;
}

.colaboradores-grid .form-check-input:checked + .form-check-label {
    color: #0d6efd;
}

.info-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

/* Validaciones visuales */
.form-control:invalid {
    border-color: #dc3545;
}

.form-control:valid {
    border-color: #198754;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    display: block;
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
            fechaFin.setCustomValidity('La fecha de fin no puede ser anterior a la fecha de inicio');
            fechaFin.classList.add('is-invalid');
        } else {
            fechaFin.setCustomValidity('');
            fechaFin.classList.remove('is-invalid');
        }
    }
    
    fechaInicio.addEventListener('change', validarFechas);
    fechaFin.addEventListener('change', validarFechas);
    
    // Seleccionar/deseleccionar todos los colaboradores
    const colaboradoresContainer = document.querySelector('.colaboradores-grid');
    if (colaboradoresContainer) {
        // Agregar botón de seleccionar todos
        const selectAllBtn = document.createElement('div');
        selectAllBtn.className = 'mb-2 text-end';
        selectAllBtn.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="seleccionarTodos()">
                ✓ Todos
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodos()">
                ✗ Ninguno
            </button>
        `;
        colaboradoresContainer.insertBefore(selectAllBtn, colaboradoresContainer.firstChild);
    }
    
    // Confirmar cambios antes de salir
    const form = document.querySelector('form');
    const originalData = new FormData(form);
    let hasChanges = false;
    
    // Detectar cambios en el formulario
    form.addEventListener('input', function() {
        hasChanges = true;
    });
    
    form.addEventListener('change', function() {
        hasChanges = true;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Remover evento beforeunload al enviar el formulario
    form.addEventListener('submit', function() {
        hasChanges = false;
    });
    
    // Validación del formulario antes de enviar
    form.addEventListener('submit', function(e) {
        validarFechas();
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        form.classList.add('was-validated');
    });
});

// Funciones para colaboradores
function seleccionarTodos() {
    const checkboxes = document.querySelectorAll('input[name="colaboradores[]"]');
    checkboxes.forEach(cb => cb.checked = true);
}

function deseleccionarTodos() {
    const checkboxes = document.querySelectorAll('input[name="colaboradores[]"]');
    checkboxes.forEach(cb => cb.checked = false);
}
</script>