<?php
session_start();

// Si no está logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $usuario['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Proyectum</title>
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo $usuario['nombres']; ?> 👋</h1>
        <p>Rol: <strong><?php echo ucfirst($rol); ?></strong></p>
        <a href="controllers/UsuarioController.php?accion=logout">Cerrar sesión</a>
    </header>

    <nav>
        <ul>
            <?php if ($rol === "administrador"): ?>
                <li><a href="views/usuarios/listar.php">👤 Gestión de Usuarios</a></li>
            <?php endif; ?>

            <?php if ($rol === "gestor"): ?>
                <li><a href="views/proyectos/listar.php">📁 Proyectos</a></li>
                <li><a href="views/riesgos/listar.php">⚠️ Riesgos</a></li>
                <li><a href="views/informes/listar.php">📊 Informes</a></li>
            <?php endif; ?>

            <?php if ($rol === "colaborador"): ?>
                <li><a href="views/proyectos/listar.php">📁 Proyectos</a></li>
            <?php endif; ?>

            <?php if ($rol === "cliente"): ?>
                <li><a href="views/proyectos/listar.php">📁 Proyectos</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2>Panel principal</h2>
        <p>Aquí verás accesos y resúmenes según tu rol.</p>
    </main>
</body>
</html>
