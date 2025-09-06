<?php
require_once __DIR__ . "/../models/Comentario.php";
require_once __DIR__ . "/../config/auth.php";


requireLogin();


class ComentarioController {
    private $comentarioModel;

    public function __construct() {
        $this->comentarioModel = new Comentario();
    }

    // Crear comentario
    public function crear($tarea_id, $usuario_id, $comentario) {
        if (!$usuario_id) {
            die("No hay usuario logueado.");
            var_dump($_SESSION);
            exit();
        }
        if (!$tarea_id || empty($comentario)) {
            die("Datos incompletos para crear comentario.");
        }
        $this->comentarioModel->crear($tarea_id, $usuario_id, $comentario);
        // Redirigir de regreso al tablero de la tarea
        header("Location: ../views/tareas/editar.php?id=$tarea_id");
        exit();


    }

    // Listar comentarios de una tarea
    public function listar($tarea_id) {
        if (!$tarea_id) {
            die("Tarea no especificada.");
        }
        $comentarios = $this->comentarioModel->listarPorTarea($tarea_id);
        echo json_encode($comentarios);
        exit();
    }
}

if(isset($_GET['accion'])) {
    $controller = new ComentarioController();

    switch($_GET['accion']) {
        case 'crear':
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tarea_id = $_POST['tarea_id'] ?? null;
                $comentario = $_POST['comentario'] ?? '';
                $usuario_id = $_SESSION['usuario']['id'] ?? null;   

                $controller->crear($tarea_id, $usuario_id, $comentario);
            }
            break;

        case 'listar':
            if(isset($_GET['tarea_id'])) {
                $controller->listar($_GET['tarea_id']);
            }
            break;
    }
}

