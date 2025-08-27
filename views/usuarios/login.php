<?php
session_start();

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'proyectum';
$username = 'root';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Variable para mensajes
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validar campos vacíos
    if (empty($email) || empty($password)) {
        $mensaje = "Por favor, complete todos los campos.";
        $tipo_mensaje = "error";
    } else {
        try {
            // Buscar usuario en la base de datos
            $stmt = $pdo->prepare("SELECT id, nombres, apellidos, email, password, rol, estado FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Verificar si el usuario está activo
                if ($usuario['estado'] != 'activo') {
                    $mensaje = "Su cuenta está inactiva. Contacte al administrador.";
                    $tipo_mensaje = "error";
                } else {
                    // Login exitoso - registrar en log de auditoría
                    $stmt_log = $pdo->prepare("INSERT INTO log_auditoria (usuario_id, accion, fecha_hora, detalles) VALUES (?, ?, NOW(), ?)");
                    $stmt_log->execute([
                        $usuario['id'], 
                        'LOGIN', 
                        "Inicio de sesión desde IP: " . $_SERVER['REMOTE_ADDR']
                    ]);
                    
                    // Establecer variables de sesión
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombres'] . ' ' . $usuario['apellidos'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];
                    $_SESSION['login_time'] = time();
                    
                    // Redirigir según el rol del usuario
                    switch ($usuario['rol']) {
                        case 'administrador':
                            header("Location: panel_admin.php");
                            break;
                        case 'gestor':
                            header("Location: panel_gestor.php");
                            break;
                        case 'colaborador':
                            header("Location: panel_colaborador.php");
                            break;
                        case 'cliente':
                            header("Location: panel_cliente.php");
                            break;
                        default:
                            header("Location: dashboard.php");
                    }
                    exit();
                }
            } else {
                $mensaje = "Credenciales incorrectas. Verifique su email y contraseña.";
                $tipo_mensaje = "error";
                
                // Registrar intento fallido en log
                if ($usuario) {
                    $stmt_log = $pdo->prepare("INSERT INTO log_auditoria (usuario_id, accion, fecha_hora, detalles) VALUES (?, ?, NOW(), ?)");
                    $stmt_log->execute([
                        $usuario['id'], 
                        'LOGIN_FAILED', 
                        "Intento fallido desde IP: " . $_SERVER['REMOTE_ADDR']
                    ]);
                }
            }
        } catch (PDOException $e) {
            $mensaje = "Error del sistema. Intente nuevamente más tarde.";
            $tipo_mensaje = "error";
            error_log("Error en login: " . $e->getMessage());
        }
    }
}

// Si ya está logueado, redirigir al panel correspondiente
if (isset($_SESSION['usuario_id'])) {
    switch ($_SESSION['usuario_rol']) {
        case 'administrador':
            header("Location: panel_admin.php");
            break;
        case 'gestor':
            header("Location: panel_gestor.php");
            break;
        case 'colaborador':
            header("Location: panel_colaborador.php");
            break;
        case 'cliente':
            header("Location: panel_cliente.php");
            break;
        default:
            header("Location: dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Gestión de Proyectos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
        }

        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .system-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }

        .system-info h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .roles-info {
            margin-top: 10px;
        }

        .roles-info ul {
            list-style-type: none;
            padding-left: 0;
        }

        .roles-info li {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Iniciar Sesión</h1>
            <p>Sistema de Gestión de Proyectos</p>
        </div>

        <div class="system-info">
            <h4>Roles del Sistema:</h4>
            <div class="roles-info">
                <ul>
                    <li><strong>Administrador:</strong> Control total del sistema</li>
                    <li><strong>Gestor:</strong> Gestión de proyectos y recursos</li>
                    <li><strong>Colaborador:</strong> Ejecución de tareas</li>
                    <li><strong>Cliente:</strong> Consulta de proyectos</li>
                </ul>
            </div>
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

            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>

        <div class="forgot-password">
            <a href="recuperar_password.php">¿Olvidó su contraseña?</a>
        </div>
    </div>

    <script>
        // Limpiar mensajes después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.display = 'none';
            });
        }, 5000);

        // Validación del formulario en el cliente
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
    </script>
</body>
</html>