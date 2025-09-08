<?php require_once __DIR__ . "/../../config/auth.php"; requireLogin(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header de la página -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-danger mb-1">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Riesgos del Proyecto
                    </h2>
                    <p class="text-muted mb-0">Gestiona y monitorea los riesgos identificados</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="router.php?page=riesgos/crear=<?php echo $_GET['id_proyecto']; ?>" 

                       class="btn btn-danger">
                        Registrar Riesgo
                    </a>
                    <a href="router.php?page=dashboard" class="btn btn-outline-secondary">
                        🏠 Volver al Dashboard
                    </a>
                </div>
            </div>

            <?php if (empty($riesgos)): ?>
                <!-- Estado vacío -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-shield-alt text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="text-muted mb-3">No hay riesgos registrados</h4>
                        <p class="text-muted mb-4">
                            Este proyecto aún no tiene riesgos identificados.<br>
                            Comienza registrando los posibles riesgos para una mejor gestión.
                        </p>
                        <a href="/proyectum/controllers/RiesgoController.php?accion=crear&id_proyecto=<?php echo $_GET['id_proyecto']; ?>" 
                           class="btn btn-danger btn-lg">
                            <i class="fas fa-plus me-2"></i>Registrar Primer Riesgo
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Tabla de riesgos -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="mb-0 text-muted">
                                    <i class="fas fa-list me-2"></i>
                                    Total de riesgos: <span class="badge bg-danger"><?php echo count($riesgos); ?></span>
                                </h6>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex gap-2">
                                    <span class="badge bg-success">Bajo</span>
                                    <span class="badge bg-warning">Medio</span>
                                    <span class="badge bg-danger">Alto</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 fw-semibold">
                                            <i class="fas fa-file-alt me-2"></i>Descripción
                                        </th>
                                        <th class="border-0 fw-semibold text-center">
                                            <i class="fas fa-impact me-2"></i>Impacto
                                        </th>
                                        <th class="border-0 fw-semibold text-center">
                                            <i class="fas fa-percentage me-2"></i>Probabilidad
                                        </th>
                                        <th class="border-0 fw-semibold">
                                            <i class="fas fa-shield-alt me-2"></i>Medidas de Mitigación
                                        </th>
                                        <th class="border-0 fw-semibold text-center">
                                            <i class="fas fa-cogs me-2"></i>Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riesgos as $r): ?>
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-start">
                                                <div class="me-3 mt-1">
                                                    <?php
                                                    $iconColor = '';
                                                    switch(strtolower($r['impacto'])) {
                                                        case 'bajo': $iconColor = 'text-success'; break;
                                                        case 'medio': $iconColor = 'text-warning'; break;
                                                        case 'alto': $iconColor = 'text-danger'; break;
                                                    }
                                                    ?>
                                                    <i class="fas fa-exclamation-circle <?php echo $iconColor; ?>"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-medium"><?php echo htmlspecialchars($r['descripcion']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php
                                            $impactoBadge = '';
                                            switch(strtolower($r['impacto'])) {
                                                case 'bajo': $impactoBadge = 'bg-success'; break;
                                                case 'medio': $impactoBadge = 'bg-warning'; break;
                                                case 'alto': $impactoBadge = 'bg-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $impactoBadge; ?> px-3 py-2">
                                                <?php echo $r['impacto']; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <?php
                                            $probBadge = '';
                                            switch(strtolower($r['probabilidad'])) {
                                                case 'baja': $probBadge = 'bg-success'; break;
                                                case 'media': $probBadge = 'bg-warning'; break;
                                                case 'alta': $probBadge = 'bg-danger'; break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $probBadge; ?> px-3 py-2">
                                                <?php echo $r['probabilidad']; ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-truncate" style="max-width: 250px;" 
                                                 title="<?php echo htmlspecialchars($r['medidas_mitigacion']); ?>">
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($r['medidas_mitigacion']); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="/proyectum/controllers/RiesgoController.php?accion=editar&id=<?php echo $r['id']; ?>&id_proyecto=<?php echo $_GET['id_proyecto']; ?>" 
                                                   class="btn btn-outline-primary" title="Editar riesgo">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="/proyectum/controllers/RiesgoController.php?accion=eliminar&id=<?php echo $r['id']; ?>&id_proyecto=<?php echo $_GET['id_proyecto']; ?>" 
                                                   class="btn btn-outline-danger" 
                                                   title="Eliminar riesgo"
                                                   onclick="return confirm('¿Estás seguro de que deseas eliminar este riesgo?\n\nEsta acción no se puede deshacer.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas rápidas -->
                <div class="row mt-4">
                    <?php
                    $stats = [
                        'alto' => array_filter($riesgos, fn($r) => strtolower($r['impacto']) === 'alto'),
                        'medio' => array_filter($riesgos, fn($r) => strtolower($r['impacto']) === 'medio'),
                        'bajo' => array_filter($riesgos, fn($r) => strtolower($r['impacto']) === 'bajo')
                    ];
                    ?>
                    <div class="col-md-4">
                        <div class="card border-danger bg-light-danger">
                            <div class="card-body text-center">
                                <h3 class="text-danger mb-1"><?php echo count($stats['alto']); ?></h3>
                                <p class="text-danger mb-0 small">Riesgos de Alto Impacto</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-warning bg-light-warning">
                            <div class="card-body text-center">
                                <h3 class="text-warning mb-1"><?php echo count($stats['medio']); ?></h3>
                                <p class="text-warning mb-0 small">Riesgos de Impacto Medio</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-success bg-light-success">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-1"><?php echo count($stats['bajo']); ?></h3>
                                <p class="text-success mb-0 small">Riesgos de Bajo Impacto</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}
.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}
.card {
    transition: all 0.2s ease;
}
.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>