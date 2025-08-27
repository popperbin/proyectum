<?php
require_once __DIR__ . "/../../models/Proyecto.php";
$proyectoModel = new Proyecto();
$proyecto = $proyectoModel->obtenerPorId($_GET['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Proyecto</title>
</head>
<body>
    <h2>Detalle del Proyecto</h2>
    <p><strong>Nombre:</strong> <?= $proyecto['nombre'] ?></p>
    <p><strong>Descripción:</strong> <?= $proyecto['descripcion'] ?></p>
    <p><strong>Fecha inicio:</strong> <?= $proyecto['fecha_inicio'] ?></p>
    <p><strong>Fecha fin:</strong> <?= $proyecto['fecha_fin'] ?></p>
    <p><strong>Presupuesto:</strong> <?= $proyecto['presupuesto'] ?></p>
    <p><strong>Estado:</strong> <?= $proyecto['estado'] ?></p>
    <a href="listar.php">Volver</a>
</body>
</html>
