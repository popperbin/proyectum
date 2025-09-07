<?php

function requireLogin() {
    if (!isset($_SESSION['usuario'])) {
        header("Location: ../index.php");
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
