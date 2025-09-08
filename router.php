<?php
require_once __DIR__ . '/config/bootstrap.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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
    'tareas/acciones',  // Nueva vista para acciones de tareas (crear, editar, eliminar)
    
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
// Lista de entidades que usan acciones separadas
$entitiesWithActions = ['tareas', 'listas', 'proyectos', 'informes'];

// 🔹 DETECCIÓN DE rutas tipo: tareas/acciones, listas/acciones, etc.
$pageParts = explode('/', $page);
if (count($pageParts) === 2 && $pageParts[1] === 'acciones' && in_array($pageParts[0], $entitiesWithActions)) {
    $entity = $pageParts[0];
    $accionesPath = __DIR__ . "/views/{$entity}/acciones.php";

    if (file_exists($accionesPath)) {
        require $accionesPath;
        exit;
    } else {
        echo '<div class="container mt-5">';
        echo '<div class="alert alert-danger text-center">';
        echo '<h4>📁 Archivo de acciones no encontrado</h4>';
        echo '<p>No existe el archivo <strong>' . htmlspecialchars($accionesPath) . '</strong>.</p>';
        echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
        echo '</div></div>';
        exit;
    }
}

// 🔹 VISTAS PERMITIDAS
if (in_array($page, $allowedRoutes)) {
    if ($page === 'dashboard') {
        require __DIR__ . "/dashboard.php";
    } else {
        $viewPath = __DIR__ . "/views/{$page}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo '<div class="container mt-5">';
            echo '<div class="alert alert-danger text-center">';
            echo '<h4>📁 Página no encontrada</h4>';
            echo '<p>La vista <strong>' . htmlspecialchars($page) . '</strong> no existe.</p>';
            echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
            echo '</div></div>';
        }
    }

// 🔹 CONTROLADORES TRADICIONALES (si no usas acciones separadas)
} else {
    switch ($page) {
        case 'tareas':
            require_once __DIR__ . "/controllers/TareaController.php";
            $controller = new TareaController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
                $controller->crear($_POST);
            } elseif ($accion === 'eliminar' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
            }
            elseif ($accion === 'eliminar_lista' && isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminarLista($_GET['id'], $_GET['proyecto_id']);
            }
            break;

        case 'proyectos':
            require_once __DIR__ . "/controllers/ProyectoController.php";
            $controller = new ProyectoController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
                $controller->crear($_POST);
            }
            break;

        case 'listas':
            require_once __DIR__ .  "/controllers/ListaController.php";

            $controller = new ListaController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'crear') {
                $controller->crear($_POST);
            }
            break;

        // Puedes seguir agregando más controladores aquí...

        default:
            // 🔸 Ruta no reconocida
            echo '<div class="container mt-5">';
            echo '<div class="alert alert-warning text-center">';
            echo '<h4>🚫 Acceso no permitido</h4>';
            echo '<p>No tienes permisos para acceder a <strong>' . htmlspecialchars($page) . '</strong>.</p>';
            echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
            echo '</div></div>';
            break;
    }
}

// 🔹 FOOTER GLOBAL
require_once __DIR__ . "/views/layout/footer.php";