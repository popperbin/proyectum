<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// router.php
require_once __DIR__ . "/config/config.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: " . url("views/usuarios/login.php"));
    exit();
}

// Usuario en sesión
$usuario = $_SESSION['usuario'];

// Página solicitada (?page=)
$page = $_GET['page'] ?? "dashboard";

// Variable para controlar si debemos mostrar el layout completo
$showLayout = true;

// --- Procesar Controladores ANTES del header ---
// Verificar si es una acción de controlador que puede hacer redirecciones
if (!in_array($page, [
    'dashboard',
    'proyectos/listar',
    'proyectos/crear', 
    'proyectos/editar',
    'proyectos/detalle',
    'tareas/tablero',
    'tareas/listar',
    'tareas/editar',
    'usuarios/listar',
    'usuarios/perfil',
    'usuarios/crear',
    'usuarios/editar',
    'informes/generar',
    'informes/listar',
    'riesgos/listar',
    'riesgos/crear',
    'comentario/crear'
])) {
    // Procesar controladores que pueden hacer redirecciones
    switch ($page) {

        case 'tareas':
            require_once __DIR__ . "/controllers/TareaController.php";
            $controller = new TareaController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
                $showLayout = false;
            } elseif ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
                $controller->editar($_GET['id'], $_POST);
                $showLayout = false;
            } elseif ($accion === 'eliminar' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
                $showLayout = false;
            } elseif ($accion === 'mover' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Llamar al método sin parámetros - él lee $_POST directamente
                $controller->mover();
                $showLayout = false;
            }
            break;

        case 'proyectos':
            require_once __DIR__ . "/controllers/ProyectoController.php";
            $controller = new ProyectoController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
                $showLayout = false;
            } elseif ($accion === 'editar' && isset($_GET['id'])) {
                $controller->editar($_GET['id'], $_POST);
                $showLayout = false;
            } elseif ($accion === 'eliminar' && isset($_GET['id'])) {
                $controller->eliminar($_GET['id']);
                $showLayout = false;
            }
            break;

        case 'listas':
            require_once __DIR__ . "/controllers/ListaController.php";
            $controller = new ListaController();

            $accion = $_GET['accion'] ?? null;
            if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear();
                $showLayout = false;
            } elseif ($accion === 'archivar' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->archivar($_GET['id'], $_GET['proyecto_id']);
                $showLayout = false;
            }
            break;

        default:
            // Ruta no permitida - mostraremos error después del header
            break;
    }
}

// Solo mostrar header si no se procesó un controlador o si es una vista normal
if ($showLayout) {
    // Header global
    include __DIR__ . "/views/layout/header.php";
}

// Rutas permitidas en el sistema (vistas que SÍ necesitan layout)
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
    
    // Riesgos
    'riesgos/listar',
    'riesgos/crear',

    //Comentarios
    'comentario/crear',
];

// Solo procesar vistas si debemos mostrar el layout
if ($showLayout) {
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
            echo '<h4>🔍 Página no encontrada</h4>';
            echo '<p>La vista <strong>' . htmlspecialchars($page) . '</strong> no existe.</p>';
            echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        // Ruta no permitida - mostrar error 403
        echo '<div class="container mt-5"><div class="alert alert-warning text-center">';
        echo '<h4>🚫 Acceso no permitido</h4>';
        echo '<p>No tienes permisos para acceder a <strong>' . htmlspecialchars($page) . '</strong>.</p>';
        echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
        echo '</div></div>';
    }

    // Footer global
    include __DIR__ . "/views/layout/footer.php";
}