<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["gestor"]);
require_once __DIR__ . "/../../models/Proyecto.php";

$proyectoModel = new Proyecto();
$proyecto = $proyectoModel->obtenerPorId($_GET['id']);

if (!$proyecto) {
    echo "<p style='color:red;'>Proyecto no encontrado.</p>";
    echo "<a href='listar.php'>Volver a la lista de proyectos</a>";
    exit;
}
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
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($proyecto['nombre']) ?>" required><br>

    <label>Descripción</label>
    <textarea name="descripcion"><?= htmlspecialchars($proyecto['descripcion']) ?></textarea><br>

    <label>Fecha Inicio</label>
    <input type="date" name="fecha_inicio" value="<?= $proyecto['fecha_inicio'] ?>"><br>

    <label>Fecha Fin</label>
    <input type="date" name="fecha_fin" value="<?= $proyecto['fecha_fin'] ?>"><br>

    <label>ID Gestor</label>
    <input type="number" name="gestor_id" value="<?= $proyecto['gestor_id'] ?>"><br>

    <label>ID Cliente</label>
    <input type="number" name="cliente_id" value="<?= $proyecto['cliente_id'] ?>"><br>

    <label>Estado</label>
    <select name="estado">
        <option value="planificacion" <?= $proyecto['estado']=="planificacion"?"selected":"" ?>>Planificación</option>
        <option value="activo" <?= $proyecto['estado']=="activo"?"selected":"" ?>>Activo</option>
        <option value="en_pausa" <?= $proyecto['estado']=="en_pausa"?"selected":"" ?>>En pausa</option>
        <option value="completado" <?= $proyecto['estado']=="completado"?"selected":"" ?>>Completado</option>
        <option value="cancelado" <?= $proyecto['estado']=="cancelado"?"selected":"" ?>>Cancelado</option>
    </select><br>

    <button type="submit">Actualizar</button>
</form>

    <a href="listar.php">Volver</a>
</body>
</html>
