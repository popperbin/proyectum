<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.php");
    exit();
}
$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyectum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 220px;
            background-color: #6a1b9a; /* morado */
            color: #fff;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 20px 10px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background-color: #51247a;
        }
        .content {
            margin-left: 220px; /* igual al ancho del sidebar */
            padding: 20px;
            flex: 1;
            background-color: #8ebceaff;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <h4 class="text-center">Proyectum</h4>
        <hr>
        <ul class="nav flex-column">
            <?php if ($rol === "administrador"): ?>
                <li class="nav-item"><a href="/proyectum/views/usuarios/listar.php">👤 Usuarios</a></li>
            <?php endif; ?>

            <?php if ($rol === "gestor"): ?>
                <li class="nav-item"><a href="/proyectum/views/proyectos/listar.php">📁 Proyectos</a></li>
                <li class="nav-item"><a href="/views/riesgos/listar.php">⚠️ Riesgos</a></li>
                <li class="nav-item"><a href="/views/informes/listar.php">📊 Informes</a></li>
            <?php endif; ?>

            <?php if ($rol === "colaborador" || $rol === "cliente"): ?>
                <li class="nav-item"><a href="/views/proyectos/listar.php">📁 Proyectos</a></li>
            <?php endif; ?>
        </ul>
        <hr>
        <div class="mt-auto text-center">
            <p><?= $usuario['nombres'] ?> <br><small>(<?= ucfirst($rol) ?>)</small></p>
            <a href="/controllers/UsuarioController.php?accion=logout" class="btn btn-sm btn-light">Cerrar sesión</a>
        </div>
    </aside>

    <!-- Contenido -->
    <main class="content">
