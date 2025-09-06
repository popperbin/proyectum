<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: /views/usuarios/login.php");
    exit();
}
$usuario = $_SESSION['usuario'];
$rol = strtolower(trim($usuario['rol']));

?>

<div class="d-flex">
    <!-- Contenido principal -->
    <main class="flex-grow-1 p-4" style="background-color: #78c2ffff;">
        <h1 class="mb-3">Bienvenido, <?php echo $usuario['nombres']; ?> 👋</h1>
        <h4 class="mb-4">Panel principal</h4>
        <p>Aquí verás accesos y resúmenes según tu rol <b>(<?= ucfirst($rol) ?>)</b>.</p>

        <!-- Tarjetas resumen -->
        <div class="row">
            <?php if ($rol === "gestor" || $rol === "administrador"): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">📁 Proyectos activos</h5>
                            <p class="card-text">Gestiona y supervisa tus proyectos en curso.</p>
                            <a href="router.php?page=proyectos/listar" class="btn btn-primary btn-sm">Ver proyectos</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($rol === "gestor"): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">⚠️ Riesgos</h5>
                            <p class="card-text">Identifica y gestiona riesgos de proyectos.</p>
                            <a href="router.php?page=riesgos/listar" class="btn btn-warning btn-sm">Ver riesgos</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($rol === "administrador"): ?>
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">👤 Usuarios</h5>
                            <p class="card-text">Administra cuentas y roles de usuario.</p>
                            <a href="router.php?page=usuarios/listar" class="btn btn-dark btn-sm">Ver usuarios</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
