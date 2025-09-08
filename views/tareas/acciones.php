<?php
require_once __DIR__ . '/../../controllers/TareaController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new TareaController();

$accion = $_GET['accion'] ?? null;
$proyectoId = $_POST['proyecto_id'] ?? $_GET['proyecto_id'] ?? '';
$baseUrl = "router.php?page=tareas/tablero" . ($proyectoId ? "&proyecto_id=$proyectoId" : '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($accion) {
            case 'crear':
                $controller->crear($_POST);
                // crear() ya hace redirect, por si acaso:
                header("Location: $baseUrl");
                exit();

            case 'editar':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    throw new Exception("ID de tarea no proporcionado para editar.");
                }
                $controller->editar($id, $_POST);
                header("Location: $baseUrl");
                exit();

            case 'eliminar':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    throw new Exception("ID de tarea no proporcionado para eliminar.");
                }
                $controller->eliminar($id, $proyectoId);
                header("Location: $baseUrl");
                exit();
            case 'editar':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    throw new Exception("ID de tarea no proporcionado para editar.");
                }
                $controller->editar($id, $_POST);
                header("Location: $baseUrl");
                exit();


            case 'mover':
                // Asumiendo que mover es un AJAX que espera JSON
                $tarea_id = $_POST['tarea_id'] ?? null;
                $lista_id = $_POST['lista_id'] ?? null;
                if (!$tarea_id || !$lista_id) {
                    throw new Exception("Parámetros incompletos para mover tarea.");
                }
                $controller->mover();
                // mover() hace echo json y exit, no hay redirect aquí
                break;

            case 'comentario':   // 👈 nuevo case
                require_once __DIR__ . '/../../controllers/ComentarioController.php';
                $comentarioController = new ComentarioController();

                $tarea_id   = $_POST['tarea_id'] ?? null;
                $comentario = $_POST['comentario'] ?? '';
                $usuario_id = $_SESSION['usuario']['id'] ?? null;

                $comentarioController->crear($tarea_id, $usuario_id, $comentario, $proyectoId);
                break;

                default:
                    throw new Exception("Acción POST no reconocida.");
                }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: $baseUrl");
        exit();
    }
} else {
    // Si no es POST, redirigimos al tablero
    header("Location: $baseUrl");
    exit();
}
?>