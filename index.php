<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔍 DEBUG en index.php
if (isset($_SESSION['usuario'])) {
    echo "<h3>🔍 DEBUG index.php - Usuario en sesión:</h3>";
    echo "<pre>";
    print_r($_SESSION['usuario']);
    echo "</pre>";
    echo "<p>Redirigiendo a dashboard...</p>";
    // header("Location: router.php?page=dashboard");
    // exit();
} else {
    echo "<h3>🔍 DEBUG index.php - No hay usuario en sesión</h3>";
    // header("Location: views/usuarios/login.php");
    // exit();
}
?>