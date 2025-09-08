<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . "/../models/Lista.php";
require_once __DIR__ . "/../config/auth.php";

class ListaController {
    private $listaModel;

    public function __construct() {
        $this->listaModel = new Lista();
    }

    // Crear lista
public function crear() {
    requireRole(['gestor', 'administrador']);

    if (!empty($_POST['nombre']) && !empty($_POST['proyecto_id'])) {
        $lista = new Lista();
        $lista->crear($_POST['nombre'], $_POST['proyecto_id']);

        $_SESSION['success'] = "Lista creada correctamente.";
        header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=" . $_POST['proyecto_id']));
        exit();
    } else {
        $_SESSION['error'] = "Faltan datos para crear la lista.";
        header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=" . $_POST['proyecto_id'] ?? ''));
        exit();
    }
}


    // archivar lista
public function archivar($id, $proyecto_id) {
    try {
        requireRole(["gestor", "administrador"]);
        
        // Validar parámetros
        $id = (int)$id;
        $proyecto_id = (int)$proyecto_id;
        
        if ($id <= 0 || $proyecto_id <= 0) {
            $_SESSION['error'] = "Parámetros inválidos.";
            header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=$proyecto_id"));
            exit();
        }
        
        // Ejecutar archivado - AHORA CON LOS 2 PARÁMETROS
        $resultado = $this->listaModel->archivar($id, $proyecto_id);
        
        if ($resultado) {
            $_SESSION['success'] = "Lista archivada correctamente.";
        } else {
            $_SESSION['error'] = "Error al archivar la lista.";
        }
        
    } catch (Exception $e) {
        error_log("Error archivando lista: " . $e->getMessage());
        $_SESSION['error'] = "Error interno del servidor.";
    }
    
    header("Location: " . url("router.php?page=tareas/tablero&proyecto_id=$proyecto_id"));
    exit();
}

    

}
