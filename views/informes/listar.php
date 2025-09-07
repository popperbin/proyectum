<?php
require_once __DIR__ . "/../../controllers/ProyectoController.php";
require_once __DIR__ . "/../../config/auth.php";

requireLogin();
$controller = new ProyectoController();
$proyectos = $controller->listar();
$usuario = $_SESSION['usuario'];
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📋 Gestión de Proyectos</h2>
            <p class="text-muted">Administra y supervisa todos los proyectos del sistema</p>
        </div>
        <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
            <a href="router.php?page=proyectos/crear" class="btn btn-success">
                ➕ Nuevo Proyecto
            </a>
        <?php endif; ?>
    </div>

    <!-- Tabla de proyectos con diseño Bootstrap -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Lista de Proyectos (<?= count($proyectos) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Gestor</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Fecha Inicio</th>
                            <th width="280">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($proyectos)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    🔭 No hay proyectos registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($proyectos as $p): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= $p['id'] ?></span></td>
                                    <td>
                                        <a href="router.php?page=proyectos/detalle&id=<?= $p['id'] ?>" 
                                           class="text-decoration-none fw-bold">
                                            <?= htmlspecialchars($p['nombre']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($p['gestor'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($p['cliente'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $estadoClass = match($p['estado']) {
                                            'activo' => 'bg-success',
                                            'planificacion' => 'bg-warning text-dark',
                                            'en_pausa' => 'bg-secondary',
                                            'completado' => 'bg-primary',
                                            'inactivo' => 'bg-danger',
                                            default => 'bg-light text-dark'
                                        };
                                        $estadoIcon = match($p['estado']) {
                                            'activo' => '🟢',
                                            'planificacion' => '🟡',
                                            'en_pausa' => '⏸️',
                                            'completado' => '✅',
                                            'inactivo' => '🔴',
                                            default => '⚪'
                                        };
                                        ?>
                                        <span class="badge <?= $estadoClass ?>">
                                            <?= $estadoIcon ?> <?= ucfirst($p['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        <?= isset($p['fecha_inicio']) ? date('d/m/Y', strtotime($p['fecha_inicio'])) : '-' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Acciones según rol -->
                                            <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
                                                <!-- Tareas -->
                                                <a href="router.php?page=tareas/tablero&proyecto_id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Gestionar tareas">
                                                    📋
                                                </a>
                                                
                                                <!-- Editar -->
                                                <a href="router.php?page=proyectos/editar&id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Editar proyecto">
                                                    ✏️
                                                </a>
                                                
                                                <!-- Estado Toggle -->
                                                <?php if ($p['estado'] === 'activo'): ?>
                                                    <a href="controllers/ProyectoController.php?accion=inactivar&id=<?= $p['id'] ?>" 
                                                       class="btn btn-sm btn-outline-secondary"
                                                       title="Pausar proyecto"
                                                       onclick="return confirm('¿Pausar este proyecto?')">
                                                        ⏸️
                                                    </a>
                                                <?php else: ?>
                                                    <a href="controllers/ProyectoController.php?accion=activar&id=<?= $p['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success"
                                                       title="Activar proyecto"
                                                       onclick="return confirm('¿Reactivar este proyecto?')">
                                                        ▶️
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- Informes -->
                                                <a href="router.php?page=informes/generar&proyecto_id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-outline-success"
                                                   title="Generar informes">
                                                    📊
                                                </a>
                                                
                                                <!-- Eliminar -->
                                                <a href="controllers/ProyectoController.php?accion=eliminar&id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   title="Eliminar proyecto"
                                                   onclick="return confirm('⚠️ ¿Seguro que deseas eliminar <?= htmlspecialchars($p['nombre']) ?>?\n\nEsta acción no se puede deshacer.')">
                                                    🗑️
                                                </a>
                                                
                                            <?php elseif ($usuario['rol'] === "colaborador"): ?>
                                                <a href="router.php?page=tareas/tablero&proyecto_id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-info">
                                                    📌 Mis Tareas
                                                </a>
                                                
                                            <?php elseif ($usuario['rol'] === "cliente"): ?>
                                                <a href="router.php?page=informes/generar&proyecto_id=<?= $p['id'] ?>" 
                                                   class="btn btn-sm btn-success">
                                                    📊 Ver Informes
                                                </a>
                                            <?php endif; ?>
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
        $stats = array_count_values(array_column($proyectos, 'estado'));
        $activos = $stats['activo'] ?? 0;
        $completados = $stats['completado'] ?? 0;
        $enPausa = $stats['en_pausa'] ?? 0;
        $planificacion = $stats['planificacion'] ?? 0;
        ?>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h5 class="text-success"><?= $activos ?></h5>
                    <small class="text-muted">🟢 Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h5 class="text-primary"><?= $completados ?></h5>
                    <small class="text-muted">✅ Completados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <h5 class="text-secondary"><?= $enPausa ?></h5>
                    <small class="text-muted">⏸️ En Pausa</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="text-warning"><?= $planificacion ?></h5>
                    <small class="text-muted">🟡 Planificación</small>
                </div>
            </div>
        </div>
    </div>
</div>