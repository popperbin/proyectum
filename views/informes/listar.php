<?php 
require_once __DIR__ . "/../../config/auth.php"; 
requireLogin();

// Cargar controladores necesarios
require_once __DIR__ . "/../../controllers/ProyectoController.php";
require_once __DIR__ . "/../../models/Informe.php";

$proyectoController = new ProyectoController();
$informeModel = new Informe();

// Obtener usuario actual
$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'];

// Filtrar proyectos según el rol
if ($rol === 'administrador' || $rol === 'gestor') {
    $proyectos = $proyectoController->listar();
} else {
    // Para clientes y colaboradores, solo sus proyectos
    $proyectos = $proyectoController->listarPorUsuario($usuario['id']);
}

// Si se especifica un proyecto, obtener sus informes
$proyecto_id = $_GET['proyecto_id'] ?? null;
$informes = [];
$proyecto_seleccionado = null;

if ($proyecto_id) {
    $informes = $informeModel->listarPorProyecto($proyecto_id);
    $proyecto_seleccionado = $proyectoController->obtenerPorId($proyecto_id);
}
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📄 Gestión de Informes</h2>
            <p class="text-muted">Visualiza y gestiona los informes del sistema</p>
        </div>
        <div>
            <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
                <a href="router.php?page=informes/generar" class="btn btn-success me-2">
                    📊 Generar Informe
                </a>
            <?php endif; ?>
            <a href="router.php?page=dashboard" class="btn btn-outline-secondary">
                🏠 Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Filtro por proyecto -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">🔍 Filtrar por Proyecto</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="router.php" class="row align-items-end">
                <input type="hidden" name="page" value="informes/listar">
                <div class="col-md-8">
                    <label for="proyecto_id" class="form-label">Seleccionar Proyecto</label>
                    <select class="form-select" id="proyecto_id" name="proyecto_id">
                        <option value="">-- Todos los proyectos --</option>
                        <?php foreach ($proyectos as $proyecto): ?>
                            <option value="<?= $proyecto['id'] ?>" 
                                    <?= ($proyecto_id == $proyecto['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($proyecto['nombre']) ?>
                                (<?= ucfirst($proyecto['estado']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        🔍 Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($proyecto_seleccionado): ?>
        <!-- Información del proyecto seleccionado -->
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
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-<?= 
                            $proyecto_seleccionado['estado'] === 'completado' ? 'success' : 
                            ($proyecto_seleccionado['estado'] === 'activo' ? 'primary' : 'warning') 
                        ?> fs-6">
                            <?= ucfirst($proyecto_seleccionado['estado']) ?>
                        </span>
                        <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
                            <a href="router.php?page=informes/generar&id=<?= $proyecto_id ?>" 
                               class="btn btn-sm btn-success ms-2">
                                ➕ Crear Informe
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Lista de informes -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                📋 Informes Disponibles 
                <?php if ($proyecto_seleccionado): ?>
                    <span class="badge bg-secondary"><?= count($informes) ?></span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (!empty($informes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>📄 Título</th>
                                <th>📊 Tipo</th>
                                <th>👤 Generado por</th>
                                <th>📅 Fecha</th>
                                <th>📁 Archivo</th>
                                <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
                                    <th>⚙️ Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($informes as $informe): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($informe['titulo']) ?></strong>
                                        <?php if (!empty($informe['contenido'])): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= substr(htmlspecialchars($informe['contenido']), 0, 100) ?>...
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $informe['tipo'] === 'final' ? 'success' : 
                                            ($informe['tipo'] === 'progreso' ? 'primary' : 
                                            ($informe['tipo'] === 'riesgos' ? 'warning' : 'secondary')) 
                                        ?>">
                                            <?= ucfirst($informe['tipo']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(($informe['autor_nombre'] ?? '') . ' ' . ($informe['autor_apellido'] ?? '')) ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($informe['fecha_generacion'])) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($informe['archivo_pdf'])): ?>
                                            <a href="controllers/InformeController.php?accion=descargar&id=<?= $informe['id'] ?>" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-danger">
                                                📄 PDF
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Sin archivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalDetalle<?= $informe['id'] ?>">
                                                    👁️ Ver
                                                </button>
                                                <a href="controllers/InformeController.php?accion=eliminar&id=<?= $informe['id'] ?>&proyecto_id=<?= $proyecto_id ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('¿Estás seguro de eliminar este informe?')">
                                                    🗑️ Eliminar
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>

                                <!-- Modal de detalle del informe -->
                                <div class="modal fade" id="modalDetalle<?= $informe['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    📄 <?= htmlspecialchars($informe['titulo']) ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <strong>Tipo:</strong> <?= ucfirst($informe['tipo']) ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($informe['fecha_generacion'])) ?>
                                                    </div>
                                                </div>
                                                <?php if (!empty($informe['contenido'])): ?>
                                                    <div class="mb-3">
                                                        <strong>Contenido:</strong>
                                                        <p class="mt-2"><?= nl2br(htmlspecialchars($informe['contenido'])) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($informe['comentarios'])): ?>
                                                    <div class="mb-3">
                                                        <strong>Comentarios:</strong>
                                                        <p class="mt-2"><?= nl2br(htmlspecialchars($informe['comentarios'])) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($informe['observaciones'])): ?>
                                                    <div class="mb-3">
                                                        <strong>Observaciones:</strong>
                                                        <p class="mt-2"><?= nl2br(htmlspecialchars($informe['observaciones'])) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <?php if (!empty($informe['archivo_pdf'])): ?>
                                                    <a href="controllers/InformeController.php?accion=descargar&id=<?= $informe['id'] ?>" 
                                                       target="_blank" 
                                                       class="btn btn-danger">
                                                        📄 Descargar PDF
                                                    </a>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cerrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($proyecto_seleccionado): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-file-alt" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h4 class="text-muted">No hay informes para este proyecto</h4>
                    <p class="text-muted">
                        Este proyecto aún no tiene informes generados.
                    </p>
                    <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
                        <a href="router.php?page=informes/generar&id=<?= $proyecto_id ?>" 
                           class="btn btn-primary">
                            📊 Crear Primer Informe
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search" style="font-size: 4rem; color: #dee2e6;"></i>
                    </div>
                    <h4 class="text-muted">Selecciona un proyecto</h4>
                    <p class="text-muted">
                        Usa el filtro de arriba para seleccionar un proyecto y ver sus informes.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información adicional -->
    <?php if ($rol === 'administrador' || $rol === 'gestor'): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title">💡 Gestión de Informes</h6>
                        <ul class="mb-0">
                            <li><strong>Tipos de informes:</strong> Progreso, Final, Riesgos y Personalizados</li>
                            <li><strong>Generación automática:</strong> Los informes se generan en PDF automáticamente</li>
                            <li><strong>Permisos:</strong> Solo gestores y administradores pueden crear/eliminar informes</li>
                            <li><strong>Descarga:</strong> Todos los usuarios pueden descargar los PDFs de los informes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Confirmación mejorada para eliminar informes
document.addEventListener('DOMContentLoaded', function() {
    const deleteLinks = document.querySelectorAll('a[onclick*="eliminar"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmed = confirm('⚠️ ¿Estás seguro de eliminar este informe?\n\nEsta acción no se puede deshacer.');
            if (confirmed) {
                window.location.href = this.href;
            }
        });
    });
});
</script>