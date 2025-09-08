<?php

function requireLogin() {
    if (!isset($_SESSION['usuario'])) {
        // Detectar desde dónde se llama para redireccionar correctamente
        $currentPath = $_SERVER['REQUEST_URI'];
        if (strpos($currentPath, '/views/') !== false) {
            header("Location: ../../index.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    }
}

function requireRole($roles = []) {
    requireLogin();
    $rolUsuario = $_SESSION['usuario']['rol'];
    if (!in_array($rolUsuario, $roles)) {
        echo "<h2>🚫 Acceso denegado</h2>";
        exit();
    }
}
