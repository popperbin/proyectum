<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificación específica de rol (el router ya verifica la sesión)
if ($_SESSION['usuario']['rol'] !== 'administrador') {
    echo '<div class="alert alert-danger">🚫 Solo administradores pueden acceder a esta sección</div>';
    exit();
}

// Cargar datos
require_once __DIR__ . "/../../controllers/UsuarioController.php";
$controller = new UsuarioController();
$usuarios = $controller->listar();
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">👤 Gestión de Usuarios</h2>
            <p class="text-muted">Administra cuentas y roles del sistema</p>
        </div>
        <a href="router.php?page=usuarios/crear" class="btn btn-success">
            ➕ Nuevo Usuario
        </a>
    </div>

    <!-- Tabla de usuarios con diseño Bootstrap -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Lista de Usuarios (<?= count($usuarios) ?>)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th width="200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    📭 No hay usuarios registrados
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= $u['id'] ?></span></td>
                                    <td>
                                        <strong><?= htmlspecialchars($u['nombres'] . " " . $u['apellidos']) ?></strong>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= $u['email'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($u['email']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php
                                        $rolClass = match($u['rol']) {
                                            'administrador' => 'bg-danger',
                                            'gestor' => 'bg-warning text-dark',
                                            'colaborador' => 'bg-info',
                                            'cliente' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                        ?>
                                        <span class="badge <?= $rolClass ?>">
                                            <?= ucfirst($u['rol']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($u['estado'] === 'activo'): ?>
                                            <span class="badge bg-success">✅ Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">❌ Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= date('d/m/Y', strtotime($u['fecha_creacion'] ?? '')) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- ✅ CORRECTO: Usar router -->
                                            <a href="router.php?page=usuarios/editar&id=<?= $u['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Editar usuario">
                                                ✏️
                                            </a>
                                            
                                            <!-- ✅ CORRECTO: Controller para eliminar -->
                                            <a href="controllers/UsuarioController.php?accion=eliminar&id=<?= $u['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               title="Eliminar usuario"
                                               onclick="return confirm('⚠️ ¿Seguro que deseas eliminar a <?= htmlspecialchars($u['nombres']) ?>?\n\nEsta acción no se puede deshacer.');">
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

    <!-- Estadísticas rápidas (opcional) -->
    <div class="row mt-4">
        <?php
        $stats = array_count_values(array_column($usuarios, 'rol'));
        $activos = count(array_filter($usuarios, fn($u) => $u['estado'] === 'activo'));
        ?>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-primary"><?= $activos ?></h5>
                    <small class="text-muted">Usuarios Activos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-danger"><?= $stats['administrador'] ?? 0 ?></h5>
                    <small class="text-muted">Administradores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-warning"><?= $stats['gestor'] ?? 0 ?></h5>
                    <small class="text-muted">Gestores</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="text-info"><?= $stats['colaborador'] ?? 0 ?></h5>
                    <small class="text-muted">Colaboradores</small>
                </div>
            </div>
        </div>
    </div>
</div>