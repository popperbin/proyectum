<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Proyectum</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="../../controllers/UsuarioController.php?accion=login">
        <input type="email" name="email" placeholder="Correo electrónico" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
