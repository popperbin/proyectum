<?php 
require_once __DIR__ . "/../../config/auth.php"; 
requireRole(["administrador", "gestor"]);

// Cargar datos necesarios para el formulario
require_once __DIR__ . "/../../controllers/UsuarioController.php";
$usuarioController = new UsuarioController();

// Obtener todos los usuarios y filtrar por rol
$todosUsuarios = $usuarioController->listar();
$colaboradores = array_filter($todosUsuarios, fn($u) => $u['rol'] === 'colaborador');
$clientes = array_filter($todosUsuarios, fn($u) => $u['rol'] === 'cliente');
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">➕ Nuevo Proyecto</h2>
            <p class="text-muted">Crea un nuevo proyecto en el sistema</p>
        </div>
        <a href="router.php?page=proyectos/listar" class="btn btn-outline-secondary">
            ⬅️ Volver a Proyectos
        </a>
    </div>

    <!-- Formulario de creación -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">📋 Información del Proyecto</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="controllers/ProyectoController.php?accion=crear">
                <div class="row">
                    <!-- Información básica -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <strong>Nombre del Proyecto *</strong>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nombre" 
                                   name="nombre" 
                                   placeholder="Ej: Sistema de Gestión Web" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <strong>Descripción</strong>
                            </label>
                            <textarea class="form-control" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4"
                                      placeholder="Describe los objetivos y alcance del proyecto..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">
                                        <strong>📅 Fecha de Inicio</strong>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="fecha_inicio" 
                                           name="fecha_inicio"
                                           value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_fin" class="form-label">
                                        <strong>📅 Fecha Estimada de Fin</strong>
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="fecha_fin" 
                                           name="fecha_fin">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asignaciones -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">
                                <strong>Estado Inicial</strong>
                            </label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="planificacion">🟡 Planificación</option>
                                <option value="activo">🟢 Activo</option>
                                <option value="en_pausa">⏸️ En Pausa</option>
                                <option value="completado">✅ Completado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="cliente_id" class="form-label">
                                <strong>👤 Cliente Asignado</strong>
                            </label>
                            <select class="form-select" id="cliente_id" name="cliente_id">
                                <option value="">-- Seleccionar Cliente --</option>
                                <?php if (!empty($clientes)): ?>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= $cliente['id'] ?>">
                                            <?= htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apellidos']) ?>
                                            (<?= htmlspecialchars($cliente['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Colaboradores -->
                        <div class="mb-3">
                            <label class="form-label">
                                <strong>👥 Colaboradores del Proyecto</strong>
                            </label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php if (!empty($colaboradores)): ?>
                                    <?php foreach ($colaboradores as $colaborador): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="colaboradores[]" 
                                                   value="<?= $colaborador['id'] ?>"
                                                   id="colab_<?= $colaborador['id'] ?>">
                                            <label class="form-check-label" for="colab_<?= $colaborador['id'] ?>">
                                                <strong><?= htmlspecialchars($colaborador['nombres'] . ' ' . $colaborador['apellidos']) ?></strong>
                                                <br>
                                                <small class="text-muted"><?= htmlspecialchars($colaborador['email']) ?></small>
                                            </label>
                                        </div>
                                        <hr class="my-2">
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted">
                                        <p>📭 No hay colaboradores disponibles</p>
                                        <small>Los colaboradores deben ser creados desde el módulo de usuarios</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="router.php?page=proyectos/listar" class="btn btn-secondary">
                        ❌ Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        💾 Crear Proyecto
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title">💡 Consejos para crear proyectos</h6>
                    <ul class="mb-0">
                        <li><strong>Nombre descriptivo:</strong> Usa nombres claros que identifiquen fácilmente el proyecto</li>
                        <li><strong>Fechas realistas:</strong> Establece fechas de inicio y fin factibles</li>
                        <li><strong>Cliente apropiado:</strong> Asigna el cliente correcto desde el inicio</li>
                        <li><strong>Colaboradores:</strong> Puedes agregar o remover colaboradores después de la creación</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación adicional con JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    
    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
    });
    
    fechaFin.addEventListener('change', function() {
        if (this.value && fechaInicio.value && this.value < fechaInicio.value) {
            alert('⚠️ La fecha de fin no puede ser anterior a la fecha de inicio');
            this.value = '';
        }
    });
});
</script>