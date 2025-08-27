<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["administrador", "gestor"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generar Informes</title>
</head>
<body>
    <h2>📊 Generar Informes de Proyectos</h2>
    <ul>
        <li><a href="../../controllers/InformeController.php?accion=proyectos_pdf">📄 Descargar PDF</a></li>
        <li><a href="../../controllers/InformeController.php?accion=proyectos_excel">📑 Descargar Excel</a></li>
    </ul>
</body>
</html>

