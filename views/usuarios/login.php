<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Proyectum</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tu CSS global (si lo necesitas) -->
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
        <!-- Logo -->
        <div class="text-center mb-4">
            <img src="../assets/img/logo.png" alt="Logo Proyectum" class="img-fluid" style="max-height: 100px;">
        </div>

        <!-- Título -->
        <h4 class="text-center mb-3">Iniciar Sesión</h4>

        <!-- Error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center py-2">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" action="../../controllers/UsuarioController.php?accion=login">
            <div class="mb-3">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
