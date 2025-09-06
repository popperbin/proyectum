<?php
require_once __DIR__ . "/../../config/auth.php";
requireRole(["gestor"]);

require_once __DIR__ . "/../../models/Tarea.php";
require_once __DIR__ . "/../../models/Proyecto.php";
require_once __DIR__ . "/../../models/Comentario.php";

$comentarioModel = new Comentario();
$tareaModel = new Tarea();
$proyectoModel = new Proyecto();

$tarea = $tareaModel->obtenerPorId($_GET['id']);
if (!$tarea) die("Tarea no encontrada");

$colaboradores = $proyectoModel->obtenerColaboradores($tarea['proyecto_id']);
$comentarios = $comentarioModel->listarPorTarea($tarea['id']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar tarea</title>
</head>
<body>
    <h2>Editar tarea: <?= htmlspecialchars($tarea['nombre']) ?></h2>

    <!-- Formulario para actualizar tarea -->
    <form method="POST" action="../../controllers/TareaController.php?accion=editar&id=<?= $tarea['id'] ?>">
        <input type="hidden" name="proyecto_id" value="<?= $tarea['proyecto_id'] ?>">
        <input type="hidden" name="lista_id" value="<?= $tarea['lista_id'] ?>">

        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($tarea['nombre']) ?>" required><br>

        <label>Descripción:</label>
        <textarea name="descripcion"><?= htmlspecialchars($tarea['descripcion']) ?></textarea><br>

        <label>Responsable:</label>
        <select name="asignado_a">
            <option value="">-- Sin asignar --</option>
            <?php foreach ($colaboradores as $colab): ?>
                <option value="<?= $colab['id'] ?>" <?= $tarea['asignado_a']==$colab['id']?'selected':'' ?>>
                    <?= htmlspecialchars($colab['nombres']." ".$colab['apellidos']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label>Fecha inicio:</label>
        <input type="date" name="fecha_inicio" value="<?= $tarea['fecha_inicio'] ?>"><br>

        <label>Fecha fin:</label>
        <input type="date" name="fecha_fin" value="<?= $tarea['fecha_fin'] ?>"><br>

        <label>Estado:</label>
        <select name="estado">
            <option value="pendiente" <?= $tarea['estado']=="pendiente"?"selected":"" ?>>Pendiente</option>
            <option value="en_progreso" <?= $tarea['estado']=="en_progreso"?"selected":"" ?>>En progreso</option>
            <option value="completado" <?= $tarea['estado']=="completado"?"selected":"" ?>>Completado</option>
            <option value="cancelado" <?= $tarea['estado']=="cancelado"?"selected":"" ?>>Cancelado</option>
        </select><br>

        <label>Prioridad:</label>
        <select name="prioridad">
            <option value="baja" <?= $tarea['prioridad']=="baja"?"selected":"" ?>>Baja</option>
            <option value="media" <?= $tarea['prioridad']=="media"?"selected":"" ?>>Media</option>
            <option value="alta" <?= $tarea['prioridad']=="alta"?"selected":"" ?>>Alta</option>
            <option value="urgente" <?= $tarea['prioridad']=="urgente"?"selected":"" ?>>Urgente</option>
        </select><br>

        <!-- Botones -->
        <button type="submit">Actualizar</button>
        <a href="../tareas/tablero.php?proyecto_id=<?= $tarea['proyecto_id'] ?>">
            <button type="button">Cancelar</button>
        </a>
    </form>

    <hr>

    <!-- Sección de comentarios -->
    <h3>Comentarios</h3>
    <div style="max-height:200px; overflow-y:auto; border:1px solid #ccc; padding:5px;">
        <?php foreach($comentarios as $c): ?>
            <div class="comentario">
                <strong><?= htmlspecialchars($c['autor']) ?>:</strong> <?= htmlspecialchars($c['comentario']) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formulario para agregar comentario -->
    <form method="POST" action="../../controllers/ComentarioController.php?accion=crear">
        <input type="hidden" name="tarea_id" value="<?= $tarea['id'] ?>">
        <input type="text" name="comentario" placeholder="Agregar comentario" required>
        <button type="submit">Enviar comentario</button>
    </form>
</body>
</html>

