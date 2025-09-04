<?php
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
        return $this->usuarioModel->registrar(
            $data['nombres'],
            $data['apellidos'],
            $data['cedula'],
            $data['email'],
            $data['password'],
            $data['rol']
        );
    }


    public function login($email, $password) {
        $usuario = $this->usuarioModel->login($email, $password);
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: /proyectum/index.php?page=dashboard");
            exit();
        } else {
            $_SESSION['error'] = "Credenciales inválidas";
            header("Location: ../index.php");
            exit();
        }
    }

    public function logout() {
        session_destroy();
        header("Location: ../index.php");
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
                header("Location: ../views/usuarios/listar.php");
            }
            break;

        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
                header("Location: ../views/usuarios/listar.php");
            }
            break;

        case 'eliminar':
            if (isset($_GET['id'])) {
                $controller->eliminar($_GET['id']);
                header("Location: ../views/usuarios/listar.php");
            }
            break;

        case 'logout':
            $controller->logout();
            break;
    }
}
