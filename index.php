<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/config.php";

// Si hay usuario en sesión → dashboard
if (isset($_SESSION['usuario'])) {
    header("Location: " . url("router.php?page=dashboard"));
    exit();
} else {
    // Si no hay usuario en sesión → login
    header("Location: " . url("views/usuarios/login.php"));
    exit();
}
