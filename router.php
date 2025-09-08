<?php
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

// Header global
include __DIR__ . "/views/layout/header.php";

// Página solicitada (?page=)
$page = $_GET['page'] ?? "dashboard";

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
    'tareas/crear',
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
    'riesgos/crear'
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
    // Ruta no permitida - mostrar error 403
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-warning text-center">';
    echo '<h4>🚫 Acceso no permitido</h4>';
    echo '<p>No tienes permisos para acceder a <strong>' . htmlspecialchars($page) . '</strong>.</p>';
    echo '<a href="router.php?page=dashboard" class="btn btn-primary">🏠 Volver al Dashboard</a>';
    echo '</div>';
    echo '</div>';
}

// Footer global
include __DIR__ . "/views/layout/footer.php";
