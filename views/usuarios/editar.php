<?php
// Verificar sesión y permisos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    echo '<div class="alert alert-danger">🚫 Solo administradores pueden editar usuarios</div>';
    exit();
}

// Verificar que se proporcionó el ID del usuario
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensaje'] = 'ID de usuario no válido';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: router.php?page=usuarios/listar');
    exit();
}

require_once __DIR__ . "/../../controllers/UsuarioController.php";
require_once __DIR__ . "/../../models/Usuario.php";

$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorId($_GET['id']);

// Verificar que el usuario existe
if (!$usuario) {
    $_SESSION['mensaje'] = 'Usuario no encontrado';
    $_SESSION['tipo_mensaje'] = 'error';
    header('Location: router.php?page=usuarios/listar');
    exit();
}

// Mostrar mensajes de error o éxito si existen
$mensaje = $_SESSION['mensaje'] ?? null;
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? 'info';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>

<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">✏️ Editar Usuario</h2>
            <p class="text-muted">Modifica la información del usuario: <strong><?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?></strong></p>
        </div>
        <a href="router.php?page=usuarios/listar" class="btn btn-outline-secondary">
            ← Volver a la lista
        </a>
    </div>

    <!-- Mensaje de feedback -->
    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo_mensaje === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($mensaje) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Formulario -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">👤 Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="controllers/UsuarioController.php?accion=actualizar" id="formEditarUsuario">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
                        
                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombres" 
                                       name="nombres" 
                                       value="<?= htmlspecialchars($usuario['nombres']) ?>"
                                       placeholder="Ej: Juan Carlos"
                                       pattern="[A-Za-zÀ-ÿ\s]+" 
                                       title="Solo letras y espacios"
                                       minlength="2"
                                       maxlength="50"
                                       required>
                                <div class="form-text">Solo letras y espacios (2-50 caracteres)</div>
                            </div>

                            <!-- Apellidos -->
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="apellidos" 
                                       name="apellidos" 
                                       value="<?= htmlspecialchars($usuario['apellidos']) ?>"
                                       placeholder="Ej: Pérez García"
                                       pattern="[A-Za-zÀ-ÿ\s]+" 
                                       title="Solo letras y espacios"
                                       minlength="2"
                                       maxlength="50"
                                       required>
                                <div class="form-text">Solo letras y espacios (2-50 caracteres)</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cédula -->
                            <div class="col-md-6 mb-3">
                                <label for="cedula" class="form-label">Cédula *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cedula" 
                                       name="cedula" 
                                       value="<?= htmlspecialchars($usuario['cedula']) ?>"
                                       placeholder="Ej: 12345678"
                                       pattern="[0-9]{6,15}" 
                                       title="Solo números (6-15 dígitos)"
                                       minlength="6"
                                       maxlength="15"
                                       required>
                                <div class="form-text">Solo números (6-15 dígitos)</div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($usuario['email']) ?>"
                                       placeholder="Ej: usuario@empresa.com"
                                       maxlength="150"
                                       required>
                                <div class="form-text">Debe ser un email válido</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dirección -->
                            <div class="col-md-6 mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="direccion" 
                                       name="direccion" 
                                       value="<?= htmlspecialchars($usuario['direccion'] ?? '') ?>"
                                       placeholder="Ej: Calle 123 #45-67"
                                       maxlength="200">
                                <div class="form-text">Campo opcional</div>
                            </div>

                            <!-- Celular -->
                            <div class="col-md-6 mb-3">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="celular" 
                                       name="celular" 
                                       value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>"
                                       placeholder="Ej: +57 300 123 4567"
                                       pattern="[\+]?[0-9\s\-\(\)]+"
                                       title="Solo números, espacios, guiones y paréntesis"
                                       maxlength="20">
                                <div class="form-text">Campo opcional</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cargo -->
                            <div class="col-md-6 mb-3">
                                <label for="cargo" class="form-label">Cargo</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="cargo" 
                                       name="cargo" 
                                       value="<?= htmlspecialchars($usuario['cargo'] ?? '') ?>"
                                       placeholder="Ej: Desarrollador Senior"
                                       maxlength="100">
                                <div class="form-text">Campo opcional</div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Rol *</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="administrador" <?= $usuario['rol'] == 'administrador' ? 'selected' : '' ?>>
                                        🔴 Administrador (Control total)
                                    </option>
                                    <option value="gestor" <?= $usuario['rol'] == 'gestor' ? 'selected' : '' ?>>
                                        🟡 Gestor (Gestión de proyectos)
                                    </option>
                                    <option value="colaborador" <?= $usuario['rol'] == 'colaborador' ? 'selected' : '' ?>>
                                        🔵 Colaborador (Tareas asignadas)
                                    </option>
                                    <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>
                                        🟢 Cliente (Solo visualización)
                                    </option>
                                </select>
                                <div class="form-text">Define los permisos del usuario</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="">Selecciona un estado</option>
                                    <option value="activo" <?= $usuario['estado'] == 'activo' ? 'selected' : '' ?>>
                                        ✅ Activo
                                    </option>
                                    <option value="inactivo" <?= $usuario['estado'] == 'inactivo' ? 'selected' : '' ?>>
                                        ❌ Inactivo
                                    </option>
                                </select>
                                <div class="form-text">Estado del usuario en el sistema</div>
                            </div>

                            <!-- Cambiar contraseña (opcional) -->
                            <div class="col-md-6 mb-3">
                                <label for="nueva_password" class="form-label">Nueva Contraseña</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="nueva_password" 
                                           name="nueva_password" 
                                           placeholder="Dejar vacío para mantener actual"
                                           minlength="6"
                                           maxlength="50">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        👁️
                                    </button>
                                </div>
                                <div class="form-text">Solo si deseas cambiar la contraseña</div>
                            </div>
                        </div>

                        <!-- Información adicional -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Nota:</strong> Los cambios se aplicarán inmediatamente. Si cambias la contraseña, 
                                            el usuario deberá usar la nueva contraseña en su próximo inicio de sesión.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del usuario -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card bg-info bg-opacity-10">
                                    <div class="card-body py-2">
                                        <small class="text-info">
                                            <strong>📅 Información del registro:</strong><br>
                                            Creado: <?= isset($usuario['fecha_creacion']) ? date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) : 'No disponible' ?><br>
                                            Última actualización: <?= isset($usuario['fecha_actualizacion']) ? date('d/m/Y H:i', strtotime($usuario['fecha_actualizacion'])) : 'No disponible' ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="router.php?page=usuarios/listar" class="btn btn-secondary">
                                        ❌ Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning" id="btnActualizar">
                                        ✏️ Actualizar Usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para validaciones -->
<script>
    // Toggle mostrar/ocultar contraseña
    function togglePassword() {
        const passwordInput = document.getElementById('nueva_password');
        const button = passwordInput.nextElementSibling;
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            button.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            button.textContent = '👁️';
        }
    }

    // Validación solo números para cédula
    document.getElementById('cedula').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validación solo letras para nombres y apellidos
    document.getElementById('nombres').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');
    });

    document.getElementById('apellidos').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s]/g, '');
    });

    // Validación para celular (números, espacios, guiones, paréntesis)
    document.getElementById('celular').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9\s\-\(\)\+]/g, '');
    });

    // Validación del formulario antes de enviar
    document.getElementById('formEditarUsuario').addEventListener('submit', function(e) {
        const btnActualizar = document.getElementById('btnActualizar');
        
        // Confirmar cambios
        if (!confirm('¿Estás seguro de que deseas actualizar este usuario?')) {
            e.preventDefault();
            return;
        }
        
        // Desactivar botón para evitar doble envío
        btnActualizar.disabled = true;
        btnActualizar.innerHTML = '⏳ Actualizando...';
        
        // Reactivar si hay error (opcional)
        setTimeout(() => {
            btnActualizar.disabled = false;
            btnActualizar.innerHTML = '✏️ Actualizar Usuario';
        }, 5000);
    });

    // Resaltar campos modificados
    const originalValues = {};
    const inputs = document.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        originalValues[input.name] = input.value;
        
        input.addEventListener('change', function() {
            if (this.value !== originalValues[this.name]) {
                this.classList.add('border-warning');
                this.classList.remove('border-success');
            } else {
                this.classList.remove('border-warning');
                this.classList.add('border-success');
            }
        });
    });
</script>