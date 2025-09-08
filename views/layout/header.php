<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario = $_SESSION['usuario'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        
        /* Header fijo */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 60px;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 60px; /* Debajo del header */
            left: 0;
            width: 220px;
            height: calc(100vh - 60px);
            z-index: 1020;
        }
        
        /* Contenido principal */
        .main-content {
            margin-left: 220px;
            margin-top: 60px; /* Espacio para el header fijo */
            min-height: calc(100vh - 60px);
            background-color: #ceeaffff;
        }
        
        /* Botón hamburguesa (solo visible en móviles) */
        .sidebar-toggle {
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar-toggle {
                display: inline-block;
            }
            
            /* Overlay cuando sidebar está abierto */
            .sidebar-overlay {
                position: fixed;
                top: 60px;
                left: 0;
                width: 100%;
                height: calc(100vh - 60px);
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1010;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Header fijo -->
    <header class="main-header d-flex justify-content-between align-items-center px-3 bg-dark text-white">
        <!-- Botón hamburguesa + Logo -->
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle btn btn-outline-light btn-sm me-3" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon">☰</span>
            </button>
            <div class="logo">
                <h4 class="m-0">
                    <img src="../proyectum/assets/media/logo.jpg" alt="Logo" style="height: 30px;" class="me-2">
                    Proyectum
                </h4>
            </div>
        </div>
        
        <!-- Usuario -->
        <?php if ($usuario): ?>
            <div class="user-menu dropdown">
                <a href="#" class="text-white dropdown-toggle" data-bs-toggle="dropdown">
                    <?= htmlspecialchars($usuario['nombres']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="router.php?page=usuarios/perfil">👤 Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="controllers/UsuarioController.php?accion=logout">🚪 Cerrar sesión</a></li>
                </ul>
            </div>
        <?php endif; ?>
    </header>

    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php if ($usuario): ?>
                <?php include __DIR__ . "/sidebar.php"; ?>
            <?php endif; ?>
        </div>
        
        <!-- Overlay para móviles -->
        <div class="sidebar-overlay" onclick="closeSidebar()"></div>
        
        <!-- Contenido principal -->
        <main class="main-content flex-grow-1 p-4">