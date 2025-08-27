<?php
session_start();
require_once 'config/db.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$nombre_usuario = $_SESSION['nombre_usuario'];
$rol_usuario = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Proyectum</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div class="logo">
                <img src="assets/img/logo.png" alt="Proyectum">
                <h1>Proyectum</h1>
            </div>
            <div class="user-info">
                <span>Bienvenido, <?php echo htmlspecialchars($nombre_usuario); ?></span>
                <span class="rol">(<?php echo htmlspecialchars($rol_usuario); ?>)</span>
                <a href="controllers/UsuarioController.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            </div>
        </header>

        <nav class="sidebar">
            <ul class="menu">
                <?php if ($rol_usuario == 'admin'): ?>
                    <li><a href="views/usuarios/listar.php">Gestión de Usuarios</a></li>
                    <li><a href="views/proyectos/listar.php">Todos los Proyectos</a></li>
                    <li><a href="views/informes/generar.php">Informes Generales</a></li>
                <?php endif; ?>

                <?php if ($rol_usuario == 'gestor' || $rol_usuario == 'admin'): ?>
                    <li><a href="views/proyectos/listar.php">Mis Proyectos</a></li>
                    <li><a href="views/proyectos/crear.php">Crear Proyecto</a></li>
                    <li><a href="views/tareas/tablero.php">Tablero de Tareas</a></li>
                    <li><a href="views/riesgos/listar.php">Gestión de Riesgos</a></li>
                    <li><a href="views/informes/generar.php">Generar Informes</a></li>
                <?php endif; ?>

                <?php if ($rol_usuario == 'colaborador'): ?>
                    <li><a href="views/tareas/tablero.php">Mis Tareas</a></li>
                    <li><a href="views/proyectos/listar.php">Mis Proyectos</a></li>
                <?php endif; ?>

                <?php if ($rol_usuario == 'cliente'): ?>
                    <li><a href="views/proyectos/listar.php">Mis Proyectos</a></li>
                    <li><a href="views/informes/generar.php">Consultar Informes</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <main class="content">
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Proyectos Activos</h3>
                    <div class="card-number" id="proyectos-activos">-</div>
                </div>
                
                <div class="card">
                    <h3>Tareas Pendientes</h3>
                    <div class="card-number" id="tareas-pendientes">-</div>
                </div>
                
                <div class="card">
                    <h3>Riesgos Altos</h3>
                    <div class="card-number" id="riesgos-altos">-</div>
                </div>
            </div>

            <div class="recent-activity">
                <h3>Actividad Reciente</h3>
                <div id="actividad-reciente">
                    <p>Cargando actividad reciente...</p>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/funciones.js"></script>
    <script>
        // Cargar datos del dashboard
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticasDashboard();
            cargarActividadReciente();
        });
    </script>
</body>
</html>