<?php
require_once __DIR__ . "/../models/Usuario.php";

class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
        session_start();
    }

    public function login($email, $password) {
        $usuario = $this->usuarioModel->login($email, $password);
        if ($usuario) {
            $_SESSION['usuario'] = $usuario;
            header("Location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Credenciales inválidas";
            header("Location: ../index.php");
            exit();
        }
    }

    public function registrar($data) {
        return $this->usuarioModel->registrar(
            $data['nombres'],
            $data['apellidos'],
            $data['cedula'],
            $data['email'],
            $data['password'],
            $data['rol'] ?? "cliente"
        );
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

        case 'registrar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->registrar($_POST);
                header("Location: ../views/usuarios/login.php");
            }
            break;

        case 'logout':
            $controller->logout();
            break;
    }
}
