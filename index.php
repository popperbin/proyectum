<?php
session_start();

// Incluir configuración de base de datos
require_once 'config/db.php';

// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Variable para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validar campos vacíos
    if (empty($email) || empty($password)) {
        $mensaje = "Por favor, complete todos los campos.";
        $tipo_mensaje = "error";
    } else {
        try {
            $db = getDB();
            
            // Buscar usuario en la base de datos
            $usuario = $db->fetchOne(
                "SELECT id, nombres, apellidos, email, password, rol, estado FROM usuarios WHERE email = ?",
                [$email]
            );
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Verificar si el usuario está activo
                if ($usuario['estado'] != 'activo') {
                    $mensaje = "Su cuenta está inactiva. Contacte al administrador.";
                    $tipo_mensaje = "error";
                } else {
                    // Login exitoso - registrar en log de auditoría
                    $db->execute(
                        "INSERT INTO log_auditoria (usuario_id, accion, detalles, ip_address, fecha_hora) VALUES (?, ?, ?, ?, NOW())",
                        [
                            $usuario['id'], 
                            'LOGIN', 
                            "Inicio de sesión exitoso",
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]
                    );
                    
                    // Establecer variables de sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];
                    $_SESSION['login_time'] = time();
                    
                    // Redirigir al dashboard
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                $mensaje = "Credenciales incorrectas. Verifique su email y contraseña.";
                $tipo_mensaje = "error";
                
                // Registrar intento fallido en log
                if ($usuario) {
                    $db->execute(
                        "INSERT INTO log_auditoria (usuario_id, accion, detalles, ip_address, fecha_hora) VALUES (?, ?, ?, ?, NOW())",
                        [
                            $usuario['id'], 
                            'LOGIN_FAILED', 
                            "Intento de login fallido",
                            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            $mensaje = "Error del sistema. Intente nuevamente más tarde.";
            $tipo_mensaje = "error";
            error_log("Error en login: " . $e->getMessage());
        }
    }
}

// Crear estructura de BD si no existe (solo para desarrollo)
if (isset($_GET['setup']) && $_GET['setup'] == '1') {
    if (crearEstructuraBD()) {
        $mensaje = "Base de datos inicializada correctamente. Usuario admin: admin@proyectum.com / admin123";
        $tipo_mensaje = "success";
    } else {
        $mensaje = "Error al inicializar la base de datos.";
        $tipo_mensaje = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectum - Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">

</head>
<body>
    <div class="main-container">
        <!-- Sección Hero -->
        <div class="hero-section">
            <h1>Proyectum</h1>
            <p>Sistema integral de gestión de proyectos diseñado para optimizar la colaboración, el seguimiento y el control de recursos en equipos de trabajo.</p>
            
            <ul class="features">
                <li>Gestión completa de proyectos y tareas</li>
                <li>Tableros Kanban interactivos</li>
                <li>Control de usuarios y roles</li>
                <li>Gestión de riesgos e incidencias</li>
                <li>Generación de informes PDF</li>
                <li>Seguimiento en tiempo real</li>
                <li>Auditoría y control de acceso</li>
            </ul>
        </div>

        <!-- Sección Login -->
        <div class="login-section">
            <div class="login-header">
                <h2>Iniciar Sesión</h2>
                <p>Acceda a su cuenta para gestionar sus proyectos</p>
            </div>

            <div class="system-info">
                <h4>Roles del Sistema:</h4>
                <ul>
                    <li><strong>Administrador:</strong> Control total del sistema y usuarios</li>
                    <li><strong>Gestor:</strong> Gestión de proyectos, tareas y recursos</li>
                    <li><strong>Colaborador:</strong> Ejecución y seguimiento de tareas</li>
                    <li><strong>Cliente:</strong> Consulta de proyectos e informes</li>
                </ul>
                
                <?php if (isset($_GET['demo']) && $_GET['demo'] == '1'): ?>
                <div class="demo-accounts">
                    <h4>Cuentas de Demostración:</h4>
                    <ul>
                        <li><strong>Admin:</strong> admin@proyectum.com / admin123</li>
                        <li><strong>Gestor:</strong> gestor@proyectum.com / gestor123</li>
                        <li><strong>Colaborador:</strong> colaborador@proyectum.com / colab123</li>
                        <li><strong>Cliente:</strong> cliente@proyectum.com / cliente123</li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Correo Electrónico:</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="login" class="btn-primary">Iniciar Sesión</button>
            </form>

            <div class="footer-links">
                <a href="?setup=1">Configurar Sistema</a>
                <a href="?demo=1">Ver Cuentas Demo</a>
                <a href="views/usuarios/recuperar.php">Recuperar Contraseña</a>
            </div>
        </div>
    </div>

    <div class="version-info">
        v1.0.0 - Beta
    </div>

    <script src="assets/js/funciones.js"></script>
    <script>
        // Limpiar mensajes después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            });
        }, 5000);

        // Validación del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('Por favor, complete todos los campos.');
                return false;
            }

            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Por favor, ingrese un correo electrónico válido.');
                return false;
            }
        });

        // Efectos visuales
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.main-container');
            container.style.opacity = '0';
            container.style.transform = 'scale(0.9)';
            
            setTimeout(function() {
                container.style.transition = 'all 0.5s ease';
                container.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 100);
        });
    </script>
</body>
</html>