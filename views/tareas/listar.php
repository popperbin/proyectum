<?php
require_once __DIR__ . "/../../controllers/TareaController.php";
require_once __DIR__ . "/../../config/auth.php";

requireRole(["gestor","administrador","colaborador"]);

$proyecto_id = $_GET['proyecto_id'];
$controller = new TareaController();
$tareas = $controller->listar($proyecto_id);

// Obtener información del proyecto si está disponible
require_once __DIR__ . "/../../models/Proyecto.php";
$proyectoModel = new Proyecto();
$proyecto = $proyectoModel->obtenerPorId($proyecto_id);
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📋 Lista de Tareas</h2>
            <p class="text-muted">
                Proyecto: <strong><?= htmlspecialchars($proyecto['nombre'] ?? 'Desconocido') ?></strong>
            </p>
        </div>
        <div>
            <a href="router.php?page=tareas/tablero&proyecto_id=<?= $proyecto_id ?>" class="btn btn-info me-2">
                🎯 Ver Tablero
            </a>
            <a href="router.php?page=tareas/crear&proyecto_id=<?= $proyecto_id ?>" class="btn btn-success">
                ➕ Nueva Tarea
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
                📋 Tareas
            </li>
        </ol>
    </nav>

    <!-- Tabla de tareas -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Lista de Tareas (<?= count($tareas) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Fecha Límite</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tareas)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    📭 No hay tareas registradas para este proyecto
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tareas as $t): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($t['titulo']) ?></strong>
                                        <?php if (!empty($t['descripcion'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars(substr($t['descripcion'], 0, 60)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($t['responsable'])): ?>
                                            <span class="badge bg-info">👤 <?= htmlspecialchars($t['responsable']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin asignar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $estadoClass = match($t['estado']) {
                                            'completada', 'completado' => 'bg-success',
                                            'en_progreso' => 'bg-warning text-dark',
                                            'pendiente' => 'bg-secondary',
                                            'cancelado' => 'bg-danger',
                                            default => 'bg-light text-dark'
                                        };
                                        $estadoIcon = match($t['estado']) {
                                            'completada', 'completado' => '✅',
                                            'en_progreso' => '🔄',
                                            'pendiente' => '⏳',
                                            'cancelado' => '❌',
                                            default => '⚪'
                                        };
                                        ?>
                                        <span class="badge <?= $estadoClass ?>">
                                            <?= $estadoIcon ?> <?= ucfirst($t['estado']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $prioridadClass = match($t['prioridad']) {
                                            'urgente' => 'bg-danger',
                                            'alta' => 'bg-warning text-dark',
                                            'media' => 'bg-info',
                                            'baja' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                        $prioridadIcon = match($t['prioridad']) {
                                            'urgente' => '🚨',
                                            'alta' => '⚡',
                                            'media' => '📊',
                                            'baja' => '📝',
                                            default => '⚪'
                                        };
                                        ?>
                                        <span class="badge <?= $prioridadClass ?>">
                                            <?= $prioridadIcon ?> <?= ucfirst($t['prioridad']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= isset($t['fecha_fin']) ? date('d/m/Y', strtotime($t['fecha_fin'])) : '-' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Completar tarea -->
                                            <?php if ($t['estado'] !== 'completada' && $t['estado'] !== 'completado'): ?>
                                                <a href="controllers/TareaController.php?accion=estado&id=<?= $t['id'] ?>&estado=completada&proyecto_id=<?= $proyecto_id ?>" 
                                                   class="btn btn-sm btn-outline-success"
                                                   title="Marcar como completada"
                                                   onclick="return confirm('¿Marcar esta tarea como completada?')">
                                                    ✔️
                                                </a>
                                            <?php endif; ?>
                                            
                                            <!-- Editar -->
                                            <a href="router.php?page=tareas/editar&id=<?= $t['id'] ?>&proyecto_id=<?= $proyecto_id ?>" 
                                               class="btn btn-sm btn-outline-warning"
                                               title="Editar tarea">
                                                ✏️
                                            </a>
                                            
                                            <!-- Eliminar -->
                                            <a href="controllers/TareaController.php?accion=eliminar&id=<?= $t['id'] ?>&proyecto_id=<?= $proyecto_id ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               title="Eliminar tarea"
                                               onclick="return confirm('⚠️ ¿Seguro que deseas eliminar esta tarea?')">
                                                🗑️
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mt-4">
        <?php
        $estadisticas = [];
        foreach ($tareas as $tarea) {
            $estado = $tarea['estado'];
            $estadisticas[$estado] = ($estadisticas[$estado] ?? 0) + 1;
        }
        ?>
        <div class="col-md-3">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <h5 class="text-secondary"><?= $estadisticas['pendiente'] ?? 0 ?></h5>
                    <small class="text-muted">⏳ Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="text-warning"><?= $estadisticas['en_progreso'] ?? 0 ?></h5>
                    <small class="text-muted">🔄 En Progreso</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="text-success"><?= ($estadisticas['completada'] ?? 0) + ($estadisticas['completado'] ?? 0) ?></h5>
                    <small class="text-muted">✅ Completadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5 class="text-danger"><?= $estadisticas['cancelado'] ?? 0 ?></h5>
                    <small class="text-muted">❌ Canceladas</small>
                </div>
            </div>
        </div>
    </div>
</div>