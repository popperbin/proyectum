<?php
require_once __DIR__ . '/config/bootstrap.php';

// --- Bloque de acciones de controlador antes de imprimir nada ---
if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'tareas.crear':
            require __DIR__ . '/controllers/TareaController.php';
            exit; // importante
        case 'listas.archivar':
            require __DIR__ . '/controllers/ListaController.php';
            exit;
    }
}
// ----------------------------------------------------------------


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// router.php
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/controllers/InformeController.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: " . url("views/usuarios/login.php"));
    exit();
}

// Usuario en sesión
$usuario = $_SESSION['usuario'];

// Página solicitada (?page=)
$page = $_GET['page'] ?? "dashboard";

        // Header global
include __DIR__ . "/views/layout/header.php";

// Rutas permitidas en el sistema
$allowedRoutes = [
    // Dashboard
    'dashboard',
    
    // Proyectos
    'proyectos/listar',
    'proyectos/crear', 
    'proyectos/editar',
    'proyectos/detalle',
    
    // Tareas
    'tareas/tablero',
    'tareas/listar',
    'tareas/editar',
    
    // Usuarios
    'usuarios/listar',
    'usuarios/perfil',
    'usuarios/crear',
    'usuarios/editar',
    
    // Informes
    'informes/generar',
    'informes/listar',
    'informes/acciones',    
    
    // Riesgos
    'riesgos/listar',
    'riesgos/crear',

    //Comentarios
    'comentario/crear',
];
$page = $_GET['page'] ?? "dashboard";
$accion = $_GET['accion'] ?? null;
if ($page === "informes/acciones" && $accion === "descargar" && isset($_GET['id'])) {
    $controller = new InformeController();
    $controller->descargar($_GET['id']);
    exit;
}

// Verificar si la ruta está permitida
if (in_array($page, $allowedRoutes)) {
    // Construir la ruta del archivo
    if ($page === 'dashboard') {
        $viewPath = __DIR__ . "/dashboard.php";
    } else {
        $viewPath = __DIR__ . "/views/{$page}.php";
    }

    // Verificar que el archivo existe
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        // Si el archivo no existe, mostrar error 404
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-danger text-center">';
        echo '<h4>📁 Página no encontrada</h4>';
        echo '<p>La vista <strong>' . htmlspecialchars($page) . '</strong> no existe.</p>';
        echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
        echo '</div>';
        echo '</div>';
    }
} else {

     // --- Controladores ---
    switch ($page) {
        case 'tareas':
            require_once __DIR__ . "/controllers/TareaController.php";
            $controller = new TareaController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
            } elseif ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $controller->editar($_GET['id'], $_POST);
            } elseif ($accion === 'eliminar' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
            } elseif ($accion === 'mover' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->mover($_POST['id'], $_POST['lista_id']);
            }
            break;

        case 'proyectos':
            require_once __DIR__ . "/controllers/ProyectoController.php";
            $controller = new ProyectoController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
            } elseif ($accion === 'editar' && isset($_GET['id'])) {
                $controller->editar($_GET['id'], $_POST);
            } elseif ($accion === 'eliminar' && isset($_GET['id'])) {
                $controller->eliminar($_GET['id']);
            }
            break;

        case 'listas':
            require_once __DIR__ . "/controllers/ListaController.php";
            $controller = new ListaController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
            } elseif ($accion === 'archivar' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->archivar($_GET['id'], $_GET['proyecto_id']);
            }
            break;

    // Ruta no permitida - mostrar error 403
    default:

            echo '<div class="container mt-5"><div class="alert alert-warning text-center">';
            echo '<h4>🚫 Acceso no permitido</h4>';
            echo '<p>No tienes permisos para acceder a <strong>' . htmlspecialchars($page) . '</strong>.</p>';
            echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
            echo '</div></div>';
    }
}
// Footer global
include __DIR__ . "/views/layout/footer.php";
