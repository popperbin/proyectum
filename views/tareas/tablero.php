<?php
require_once __DIR__ . "/../../config/auth.php";
requireLogin();

require_once __DIR__ . "/../../models/Lista.php";
require_once __DIR__ . "/../../models/Tarea.php";

$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    die("Proyecto no especificado.");
}

$listaModel = new Lista();
$tareaModel = new Tarea();

// Obtener listas y tareas del proyecto
$listas = $listaModel->listarPorProyecto($proyecto_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablero de Tareas</title>
    <link rel="stylesheet" href="../../assets/css/estilos.css">
</head>
<body>
    <h2>Tablero de Proyecto <?= htmlspecialchars($proyecto_id) ?></h2>
    <a href="../proyectos/listar.php">⬅ Volver a proyectos</a>

    <div class="tablero">
        <?php foreach ($listas as $lista): ?>
            <div class="lista" data-lista-id="<?= $lista['id'] ?>">
                <h3><?= htmlspecialchars($lista['nombre']) ?></h3>

                <div class="tareas" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <?php foreach ($tareaModel->listarPorLista($lista['id']) as $tarea): ?>
                        <div class="tarea" draggable="true" ondragstart="drag(event)" data-id="<?= $tarea['id'] ?>">
                            <strong><?= htmlspecialchars($tarea['nombre']) ?></strong><br>
                            <small><?= htmlspecialchars($tarea['descripcion'] ?? '') ?></small>
                            <?php if (!empty($tarea['asignado_nombre'])): ?>
                                <div><small>👤 <?= htmlspecialchars($tarea['asignado_nombre']) ?></small></div>
                            <?php endif; ?>
                            <div class="acciones">
                                <a href="../../controllers/TareaController.php?accion=eliminar&id=<?= $tarea['id'] ?>&proyecto_id=<?= $proyecto_id ?>">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Crear tarea rápida -->
                <button onclick="document.getElementById('form-tarea').style.display='block'">+ Añadir tarea</button>

                <div id="form-tarea" style="display:none; border:1px solid #ccc; padding:10px; margin:10px;">
                    <form method="POST" action="../../controllers/TareaController.php?accion=crear">
                        <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">
                        <input type="hidden" name="lista_id" value="<?= $lista['id'] ?>">

                        <label>Nombre:</label><br>
                        <input type="text" name="nombre" required><br>

                        <label>Descripción:</label><br>
                        <textarea name="descripcion"></textarea><br>

                        <label>Responsable:</label><br>
                        <select name="asignado_a">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($usuarios as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= $u['nombres'] . " " . $u['apellidos'] ?></option>
                            <?php endforeach; ?>
                        </select><br>

                        <label>Fecha inicio:</label>
                        <input type="date" name="fecha_inicio"><br>

                        <label>Fecha fin:</label>
                        <input type="date" name="fecha_fin"><br>

                        <label>Estado:</label>
                        <select name="estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="en_progreso">En progreso</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select><br>

                        <label>Prioridad:</label>
                        <select name="prioridad">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="urgente">Urgente</option>
                        </select><br>

                        <button type="submit">Guardar</button>
                        <button type="button" onclick="document.getElementById('form-tarea').style.display='none'">Cancelar</button>

                        
                    </form>
                </div>

            </div>
        <?php endforeach; ?>

        <!-- Crear nueva lista -->
        <div class="lista nueva-lista">
            <form method="POST" action="../../controllers/ListaController.php?accion=crear">
                <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">
                <input type="text" name="nombre" placeholder="Nueva lista" required>
                <button type="submit">Crear Lista</button>
            </form>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
</body>
</html>
