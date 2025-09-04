<?php
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
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <div class="layout">
        <!-- Contenido -->
        <main class="content">
            <h1>Bienvenido, <?= $usuario['nombres']; ?> 👋</h1>
            <h2>Panel principal</h2>
            <p>Aquí verás accesos y resúmenes según tu rol.</p>
        </main>
    </div>
</body>
</html>
