<?php
// Verificar que haya sesión activa
if (!isset($_SESSION['usuario'])) {
    return;
}

$usuario = $_SESSION['usuario'];
$rol = strtolower(trim($usuario['rol']));
?>

<aside class="bg-dark text-white p-3 h-100" id="sidebar">
    <!-- Header del sidebar -->
    <div class="text-center mb-3 border-bottom border-secondary pb-3">
        <small class="text-muted">Bienvenido</small>
        <h6 class="mb-0 text-info"><?php
        echo $usuario['nombres'];
        ?>
        </h6><small class="text-warning"><?=ucfirst($rol)?></small>
    </div>

    <!-- Menú principal -->
    <h6 class="text-center mb-3">Menú Principal</h6>
    <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item mb-2">
            <a href="router.php?page=dashboard" class="nav-link text-white">
                🏠 Dashboard
            </a>
        </li>

        <!-- Opciones según el rol -->
        <?php if ($rol === "administrador"): ?>
            <li class="nav-item mb-2">
                <a href="router.php?page=usuarios/listar" class="nav-link text-white">
                    👤 Gestión de Usuarios
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="router.php?page=proyectos/listar" class="nav-link text-white">
                    📁 Proyectos
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="router.php?page=riesgos/listar" class="nav-link text-white">
                    ⚠️ Riesgos
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="router.php?page=informes/listar" class="nav-link text-white">
                    📊 Informes
                </a>
            </li>
        <?php endif; ?>

        <?php if ($rol === "gestor"): ?>
            <li class="nav-item mb-2">
                <a href="router.php?page=proyectos/listar" class="nav-link text-white">
                    📁 Proyectos
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="router.php?page=riesgos/listar" class="nav-link text-white">
                    ⚠️ Riesgos
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="router.php?page=informes/listar" class="nav-link text-white">
                    📊 Informes
                </a>
            </li>
        <?php endif; ?>

        <?php if ($rol === "colaborador" || $rol === "cliente"): ?>
            <li class="nav-item mb-2">
                <a href="router.php?page=proyectos/listar" class="nav-link text-white">
                    📁 Proyectos
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Menú inferior -->
    <div class="mt-auto pt-3 border-top border-secondary">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="router.php?page=usuarios/perfil" class="nav-link text-white-50">
                    ⚙️ Configuración
                </a>
            </li>
        </ul>
    </div>
</aside>