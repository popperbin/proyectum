<?php
require_once __DIR__ . "/../models/Proyecto.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../models/Usuario.php";

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
        case 'inactivar':
            if (isset($_GET['id'])) {
                $controller->inactivar($_GET['id']);
            }
            break;
        case 'activar':
            if (isset($_GET['id'])) {
                $controller->activar($_GET['id']);
            }
            break;
        case 'formulario':
            $controller->crearFormulario();
            break;

    }
}

class ProyectoController {
    private $proyectoModel;

    public function __construct() {
        $this->proyectoModel = new Proyecto();
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }
    }

    public function crearFormulario() {
        requireRole(["gestor"]);

        $usuariosModel = new Usuario();
        $colaboradores = $usuariosModel->obtenerColaboradores();  
        //gestor logueado por defecto
        $gestorLogueado = $_SESSION['usuario']['id'];

        //cargar la vista pasando los datos
        require "../views/proyectos/crear.php";
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
        requireRole(["gestor"]);

        // El gestor que está logueado
        $data['gestor_id'] = $_SESSION['usuario']['id'];

        // Crear proyecto y obtener su ID
        $proyecto_id = $this->proyectoModel->crear($data);

        // Asignar colaboradores al proyecto
        if (!empty($data['colaboradores'])) {
            foreach ($data['colaboradores'] as $colaborador_id) {
                $this->proyectoModel->asignarColaborador($proyecto_id, $colaborador_id);
            }
        }

        header("Location: ../views/proyectos/listar.php");
    }


    public function editar($id, $data) {
        requireRole(["gestor"]);
        $this->proyectoModel->actualizar($id, $data);
        header("Location: ../views/proyectos/listar.php");
    }

    public function eliminar($id) {
        requireRole(["gestor"]);
        $this->proyectoModel->eliminar($id);
        header("Location: ../views/proyectos/listar.php");
    }
    public function inactivar($id) {
        requireRole(["gestor"]);
        $this->proyectoModel->cambiarEstado($id, "inactivo");
        header("Location: ../views/proyectos/listar.php");
    }

    public function activar($id) {
        requireRole(["gestor"]);
        $this->proyectoModel->cambiarEstado($id, "activo");
        header("Location: ../views/proyectos/listar.php");
    }
}
