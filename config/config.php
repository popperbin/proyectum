<?php
// config/config.php

// URL base del proyecto
define("BASE_URL", "/proyectum/");

// Ruta absoluta al directorio del proyecto en el servidor
define("ROOT_PATH", __DIR__ . "/../");

// Función helper para generar URLs fácilmente
function url($path = "") {
    return BASE_URL . ltrim($path, "/");
}
