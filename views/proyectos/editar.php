<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["administrador", "gestor"]);
require_once __DIR__ . "/../../models/Proyecto.php";

$proyectoModel = new Proyecto();
$proyecto = $proyectoModel->obtenerPorId($_GET['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Proyecto</title>
</head>
<body>
    <h2>Editar Proyecto</h2>
    <form method="POST" action="../../controllers/ProyectoController.php?accion=editar&id=<?= $proyecto['id'] ?>">
        <input type="text" name="nombre" value="<?= $proyecto['nombre'] ?>" required><br>
        <textarea name="descripcion"><?= $proyecto['descripcion'] ?></textarea><br>
        <label>Fecha Inicio</label><input type="date" name="fecha_inicio" value="<?= $proyecto['fecha_inicio'] ?>"><br>
        <label>Fecha Fin</label><input type="date" name="fecha_fin" value="<?= $proyecto['fecha_fin'] ?>"><br>
        <label>ID Gestor</label><input type="number" name="gestor_id" value="<?= $proyecto['gestor_id'] ?>"><br>
        <label>ID Cliente</label><input type="number" name="cliente_id" value="<?= $proyecto['cliente_id'] ?>"><br>
        <label>Presupuesto</label><input type="number" step="0.01" name="presupuesto" value="<?= $proyecto['presupuesto'] ?>"><br>
        <label>Estado</label>
        <select name="estado">
            <option <?= $proyecto['estado']=="planificacion"?"selected":"" ?>>planificacion</option>
            <option <?= $proyecto['estado']=="activo"?"selected":"" ?>>activo</option>
            <option <?= $proyecto['estado']=="en_pausa"?"selected":"" ?>>en_pausa</option>
            <option <?= $proyecto['estado']=="completado"?"selected":"" ?>>completado</option>
            <option <?= $proyecto['estado']=="cancelado"?"selected":"" ?>>cancelado</option>
        </select><br>
        <button type="submit">Actualizar</button>
    </form>
    <a href="listar.php">Volver</a>
</body>
</html>
