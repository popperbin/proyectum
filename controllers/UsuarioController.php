<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
};
require_once __DIR__ . "/../models/Usuario.php";

if (!class_exists('Usuario')) {
    die("Error: La clase Usuario no está definida. Verifique que el archivo models/Usuario.php exista y sea correcto.");
}
class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    public function actualizar($id, $data) {
        return $this->usuarioModel->actualizar($id, $data);
    }

    public function eliminar($id) {
        return $this->usuarioModel->eliminar($id);
    }

    public function listar() {
        return $this->usuarioModel->listar();
    }

    public function crear($data) {
        try {
            // Validaciones del lado del servidor
            $errores = $this->validarDatosUsuario($data);
            
            if (!empty($errores)) {
                $_SESSION['mensaje'] = implode(', ', $errores);
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: /proyectum/router.php?page=usuarios/crear");
                exit();
            }

            // Verificar si ya existe el email
            if ($this->usuarioModel->existeEmail($data['email'])) {
                $_SESSION['mensaje'] = '⚠️ Ya existe un usuario con ese correo electrónico';
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: /proyectum/router.php?page=usuarios/crear");
                exit();
            }

            // Verificar si ya existe la cédula
            if ($this->usuarioModel->existeCedula($data['cedula'])) {
                $_SESSION['mensaje'] = '⚠️ Ya existe un usuario con esa cédula';
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: /proyectum/router.php?page=usuarios/crear");
                exit();
            }

            // Crear el usuario
            $resultado = $this->usuarioModel->registrar(
                trim($data['nombres']),
                trim($data['apellidos']),
                trim($data['cedula']),
                trim($data['email']),
                $data['password'],
                $data['rol']
            );

            if ($resultado) {
                $_SESSION['mensaje'] = '✅ Usuario creado exitosamente';
                $_SESSION['tipo_mensaje'] = 'success';
                header("Location: /proyectum/router.php?page=usuarios/listar");
            } else {
                $_SESSION['mensaje'] = '❌ Error al crear el usuario. Intenta nuevamente.';
                $_SESSION['tipo_mensaje'] = 'error';
                header("Location: /proyectum/router.php?page=usuarios/crear");
            }
            
        } catch (Exception $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            $_SESSION['mensaje'] = '❌ Error interno del sistema. Contacta al administrador.';
            $_SESSION['tipo_mensaje'] = 'error';
            header("Location: /proyectum/router.php?page=usuarios/crear");
        }
        exit();
    }

    private function validarDatosUsuario($data) {
        $errores = [];
        
        // Validar nombres
        if (empty(trim($data['nombres'])) || strlen(trim($data['nombres'])) < 2) {
            $errores[] = 'Los nombres deben tener al menos 2 caracteres';
        }
        
        // Validar apellidos
        if (empty(trim($data['apellidos'])) || strlen(trim($data['apellidos'])) < 2) {
            $errores[] = 'Los apellidos deben tener al menos 2 caracteres';
        }
        
        // Validar cédula
        if (empty($data['cedula']) || !preg_match('/^[0-9]{6,15}$/', $data['cedula'])) {
            $errores[] = 'La cédula debe tener entre 6 y 15 dígitos';
        }
        
        // Validar email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        }
        
        // Validar contraseña
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        // Validar rol
        $rolesValidos = ['administrador', 'gestor', 'colaborador', 'cliente'];
        if (!in_array($data['rol'], $rolesValidos)) {
            $errores[] = 'El rol seleccionado no es válido';
        }
        
        return $errores;
    }

    public function login($email, $password) {
        $usuario = $this->usuarioModel->login($email, $password);
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: /proyectum/router.php?page=dashboard");
            exit();
        } else {
            $_SESSION['error'] = "Credenciales inválidas";
            header("Location: ../views/usuarios/login.php");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../views/usuarios/login.php");
        exit();
    }

}

// --- Router básico ---
if (isset($_GET['accion'])) {
    $controller = new UsuarioController();

    switch ($_GET['accion']) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login($_POST['email'], $_POST['password']);
            }
            break;

        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->actualizar($_POST['id'], $_POST);
        header("Location: /proyectum/router.php?page=usuarios/listar");
            }
            break;

        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
        header("Location: /proyectum/router.php?page=usuarios/listar");
            }
            break;

        case 'eliminar':
            if (isset($_GET['id'])) {
                $controller->eliminar($_GET['id']);
        header("Location: /proyectum/router.php?page=usuarios/listar");
            }
            break;

        case 'logout':
            $controller->logout();
            break;
    }

}
