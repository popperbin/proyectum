<?php
// config/bootstrap.php
// Inícialo SIEMPRE antes de que cualquier archivo imprima algo

// Arranca sesión una sola vez
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Activa buffer global de salida (imprescindible)
if (!defined('APP_OB_STARTED')) {
    ob_start();                 // <-- clave
    define('APP_OB_STARTED', 1);
}

// (Opcional) Codificación interna
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

// Trae tus constantes
require_once __DIR__ . '/config.php';

// Redirección segura reutilizable
function redirect($path) {
    // Soporta rutas absolutas o relativas a tu BASE_URL
    $url = (preg_match('#^https?://#', $path)) ? $path : url($path);

    // Mientras los headers no se hayan enviado, usa header()
    if (!headers_sent()) {
        header("Location: {$url}");
    } else {
        // Plan B si algún output se coló
        echo "<script>location.replace('{$url}');</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url={$url}'></noscript>";
    }
    exit();
}