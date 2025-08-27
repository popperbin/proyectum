<?php
session_start();
require_once '../config/db.php';
require_once '../models/Usuario.php';

class UsuarioController {
    private $usuario;
    
    public function __construct() {
        $this->usuario = new Usuario();
    }
    
    public function login($email, $password) {
        $usuario_data = $this->usuario->autenticar($email, $password);
        
        if ($usuario_data) {
            $_SESSION['usuario_id'] = $usuario_data['id'];
            $_SESSION['nombre_usuario'] = $usuario_data['nombre'];
            $_SESSION['email'] = $usuario_data['email'];
            $_SESSION['rol'] = $usuario_data['rol'];
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        header('Location: ../index.php');
        exit();
    }
    
    public function registrar($datos) {
        // Validar datos
        if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['password'])) {
            return ['success' => false, 'message' => 'Todos los campos son obligatorios'];
        }
        
        // Verificar si el email ya existe
        if ($this->usuario->existeEmail($datos['email'])) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }
        
        // Crear usuario
        $resultado = $this->usuario->crear($datos);
        
        if ($resultado) {
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar usuario'];
        }
    }
    
    public function listar() {
        return $this->usuario->obtenerTodos();
    }
    
    public function obtener($id) {
        return $this->usuario->obtenerPorId($id);
    }
    
    public function actualizar($id, $datos) {
        return $this->usuario->actualizar($id, $datos);
    }
    
    public function eliminar($id) {
        return $this->usuario->eliminar($id);
    }
    
    public function cambiarRol($id, $nuevo_rol) {
        return $this->usuario->cambiarRol($id, $nuevo_rol);
    }
}

// Manejar peticiones AJAX y formularios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $controller = new UsuarioController();
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if ($controller->login($email, $password)) {
                    header('Location: ../dashboard.php');
                } else {
                    header('Location: ../index.php?error=1');
                }
                exit();
                break;
                
            case 'register':
                $datos = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'rol' => $_POST['rol'] ?? 'colaborador'
                ];
                
                $resultado = $controller->registrar($datos);
                
                if ($resultado['success']) {
                    header('Location: ../views/usuarios/listar.php?success=1');
                } else {
                    header('Location: ../views/usuarios/registro.php?error=' . urlencode($resultado['message']));
                }
                exit();
                break;
                
            case 'update':
                $id = $_POST['id'] ?? 0;
                $datos = [
                    'nombre' => $_POST['nombre'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'rol' => $_POST['rol'] ?? ''
                ];
                
                if ($controller->actualizar($id, $datos)) {
                    header('Location: ../views/usuarios/listar.php?updated=1');
                } else {
                    header('Location: ../views/usuarios/editar.php?id=' . $id . '&error=1');
                }
                exit();
                break;
        }
    }
}

// Manejar peticiones GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['action'])) {
        $controller = new UsuarioController();
        
        switch ($_GET['action']) {
            case 'logout':
                $controller->logout();
                break;
                
            case 'delete':
                $id = $_GET['id'] ?? 0;
                if ($controller->eliminar($id)) {
                    header('Location: ../views/usuarios/listar.php?deleted=1');
                } else {
                    header('Location: ../views/usuarios/listar.php?error=1');
                }
                exit();
                break;
                
            case 'get':
                $id = $_GET['id'] ?? 0;
                $usuario = $controller->obtener($id);
                header('Content-Type: application/json');
                echo json_encode($usuario);
                exit();
                break;
                
            case 'list':
                $usuarios = $controller->listar();
                header('Content-Type: application/json');
                echo json_encode($usuarios);
                exit();
                break;
        }
    }
}
?>