<?php
require_once __DIR__ . "/../models/Proyecto.php";
require_once __DIR__ . "/../config/auth.php";

$controller = new ProyectoController();

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'crear':
            $controller->crear($_POST);
            break;
        case 'editar':
            $controller->editar($_GET['id'], $_POST);
            break;
        case 'eliminar':
            $controller->eliminar($_GET['id']);
            break;
    }
}

class ProyectoController {
    private $proyectoModel;

    public function __construct() {
        $this->proyectoModel = new Proyecto();
        session_start();
    }

    public function listar() {
        $usuario = $_SESSION['usuario'];
        if ($usuario['rol'] === "cliente") {
            return $this->proyectoModel->listarPorCliente($usuario['id']);
        } else {
            return $this->proyectoModel->listarTodos();
        }
    }

    public function crear($data) {
        requireRole(["administrador", "gestor"]);
        $this->proyectoModel->crear($data);
        header("Location: ../views/proyectos/listar.php");
    }

    public function editar($id, $data) {
        requireRole(["administrador", "gestor"]);
        $this->proyectoModel->actualizar($id, $data);
        header("Location: ../views/proyectos/listar.php");
    }

    public function eliminar($id) {
        requireRole(["administrador", "gestor"]);
        $this->proyectoModel->eliminar($id);
        header("Location: ../views/proyectos/listar.php");
    }
}
