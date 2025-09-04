<?php
session_start();
require_once "config/db.php";

// Header global
include "views/layout/header.php";

// Router muy simple
$page = $_GET['page'] ?? "dashboard";

switch ($page) {
    case "usuarios/listar":
        require "views/usuarios/listar.php";
        break;
    case "usuarios/crear":
        require "views/usuarios/crear.php";
        break;
    case "proyectos/listar":
        require "views/proyectos/listar.php";
        break;
    default:
        require "dashboard.php";
        break;
}

// Footer global
include "views/layout/footer.php";

?>