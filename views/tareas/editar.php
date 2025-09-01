<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["gestor"]);
require_once __DIR__ . "/../../models/Tarea.php";

$tareaModel = new Tarea();
$tarea = $tareaModel->obtenerPorId($_GET['id']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar tarea</title>
</head>
<body>
    <h2>Editar tarea</h2>
    <form method="POST" action="../../controllers/TareaController.php?accion=editar&id=<?= $tarea['id'] ?>">
        <input type="hidden" name="proyecto_id" value="<?= $tarea['proyecto_id'] ?>">

        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= $tarea['nombre'] ?>" required><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion"><?= $tarea['descripcion'] ?></textarea><br>

        <label>Responsable:</label><br>
        <input type="number" name="asignado_a" value="<?= $tarea['asignado_a'] ?>"><br>

        <label>Fecha inicio:</label>
        <input type="date" name="fecha_inicio" value="<?= $tarea['fecha_inicio'] ?>"><br>

        <label>Fecha fin:</label>
        <input type="date" name="fecha_fin" value="<?= $tarea['fecha_fin'] ?>"><br>

        <label>Estado:</label>
        <select name="estado">
            <option <?= $tarea['estado']=="pendiente"?"selected":"" ?> value="pendiente">Pendiente</option>
            <option <?= $tarea['estado']=="en_progreso"?"selected":"" ?> value="en_progreso">En progreso</option>
            <option <?= $tarea['estado']=="completado"?"selected":"" ?> value="completado">Completado</option>
            <option <?= $tarea['estado']=="cancelado"?"selected":"" ?> value="cancelado">Cancelado</option>
        </select><br>

        <label>Prioridad:</label>
        <select name="prioridad">
            <option <?= $tarea['prioridad']=="baja"?"selected":"" ?> value="baja">Baja</option>
            <option <?= $tarea['prioridad']=="media"?"selected":"" ?> value="media">Media</option>
            <option <?= $tarea['prioridad']=="alta"?"selected":"" ?> value="alta">Alta</option>
            <option <?= $tarea['prioridad']=="urgente"?"selected":"" ?> value="urgente">Urgente</option>
        </select><br>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
