<?php
// Verificar sesión y permisos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    echo '<div class="alert alert-danger">🚫 Solo administradores pueden crear usuarios</div>';
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
            <h2 class="mb-1">➕ Crear Nuevo Usuario</h2>
            <p class="text-muted">Completa el formulario para registrar un nuevo usuario</p>
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
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">👤 Información del Usuario</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="controllers/UsuarioController.php?accion=crear" id="formCrearUsuario">
                        <div class="row">
                            <!-- Nombres -->
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nombres" 
                                       name="nombres" 
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
                                       placeholder="Ej: usuario@empresa.com"
                                       maxlength="150"
                                       required>
                                <div class="form-text">Debe ser un email válido</div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Contraseña -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Contraseña *</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Mínimo 6 caracteres"
                                           minlength="6"
                                           maxlength="50"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        👁️
                                    </button>
                                </div>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Rol *</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Selecciona un rol</option>
                                    <option value="administrador">🔴 Administrador (Control total)</option>
                                    <option value="gestor">🟡 Gestor (Gestión de proyectos)</option>
                                    <option value="colaborador">🔵 Colaborador (Tareas asignadas)</option>
                                    <option value="cliente">🟢 Cliente (Solo visualización)</option>
                                </select>
                                <div class="form-text">Define los permisos del usuario</div>
                            </div>
                        </div>

                        <!-- Información adicional opcional -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <small class="text-muted">
                                            <strong>Nota:</strong> El usuario recibirá sus credenciales y podrá ver su información personal desde su perfil.
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
                                    <button type="submit" class="btn btn-success" id="btnGuardar">
                                        ✅ Crear Usuario
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
        const passwordInput = document.getElementById('password');
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

    // Validación del formulario antes de enviar
    document.getElementById('formCrearUsuario').addEventListener('submit', function(e) {
        const btnGuardar = document.getElementById('btnGuardar');
        
        // Desactivar botón para evitar doble envío
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '⏳ Creando...';
        
        // Reactivar si hay error (opcional)
        setTimeout(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '✅ Crear Usuario';
        }, 5000);
    });
</script>