<?php
require_once __DIR__ . "/../../config/auth.php";
requireLogin();

require_once __DIR__ . "/../../models/Lista.php";
require_once __DIR__ . "/../../models/Tarea.php";
require_once __DIR__ . "/../../models/Proyecto.php";
require_once __DIR__ . "/../../models/comentario.php";

$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    die("Proyecto no especificado.");
}

$listaModel = new Lista();
$tareaModel = new Tarea();
$proyectoModel = new Proyecto();
$comentarioModel = new Comentario();

// Obtener información del proyecto
$proyecto = $proyectoModel->obtenerPorId($proyecto_id);
if (!$proyecto) {
    die("Proyecto no encontrado");
}

// Obtener listas y tareas del proyecto
$listas = $listaModel->listarPorProyecto($proyecto_id);

// Obtener colaboradores del proyecto
$colaboradores = $proyectoModel->obtenerColaboradores($proyecto_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablero de Tareas</title>
    <link rel="stylesheet" href="../../assets/css/estilos.css">
</head>
<body>
<nav>
    <h2>Tablero de Proyecto <?= htmlspecialchars($proyecto['nombre']) ?></h2>
    <a href="../proyectos/listar.php">⬅ Volver a proyectos</a>
</nav>

<div class="tablero">
    <?php foreach ($listas as $lista): ?>
        <div class="lista" data-lista-id="<?= $lista['id'] ?>">
            <h3><?= htmlspecialchars($lista['nombre']) ?></h3>

            <div class="tareas" ondrop="drop(event)" ondragover="allowDrop(event)">
                <?php foreach ($tareaModel->listarPorLista($lista['id']) as $tarea): ?>
                    <div class="tarea" draggable="true" ondragstart="drag(event)" data-id="<?= $tarea['id'] ?>">
                        <strong><?= htmlspecialchars($tarea['nombre']) ?></strong><br>

                        <?php if (!empty($tarea['descripcion'])): ?>
                            <small><?= htmlspecialchars($tarea['descripcion']) ?></small><br>
                        <?php endif; ?>

                        <?php if (!empty($tarea['asignado_nombre'])): ?>
                            <div><small>👤 <?= htmlspecialchars($tarea['asignado_nombre']) ?></small></div>
                        <?php endif; ?>

                        <?php if (!empty($tarea['fecha_fin'])): ?>
                            <div><small>📅 <?= date('d/m/Y', strtotime($tarea['fecha_fin'])) ?></small></div>
                        <?php endif; ?>
                        <?php   $comentarios = $comentarioModel->listarPorTarea($tarea['id']);
                            if (!empty($comentarios)): ?>
                                <div class="comentarios-tarea" style="margin-top:5px; border-top:1px solid #ddd; padding-top:5px;">
                                    <?php foreach ($comentarios as $c): ?>
                                        <div><strong>comentarios:</strong>
                                            <?= htmlspecialchars($c['comentario']) ?>
                                         </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <a href="editar.php?id=<?= $tarea['id'] ?>&proyecto_id=<?= $proyecto_id ?>">✏️ editar tarea</a>
                        <div class="acciones">
                            <a href="../../controllers/TareaController.php?accion=eliminar&id=<?= $tarea['id'] ?>&proyecto_id=<?= $proyecto_id ?>" 
                               onclick="return confirm('¿Eliminar esta tarea?')">🗑️</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Botón para mostrar formulario -->
            <button onclick="mostrarFormulario(<?= $lista['id'] ?>)">+ Añadir tarea</button>

            <!-- Formulario de nueva tarea -->
            <div id="form-tarea-<?= $lista['id'] ?>" class="form-tarea" style="display:none;">
                <form method="POST" action="../../controllers/TareaController.php?accion=crear">
                    <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">
                    <input type="hidden" name="lista_id" value="<?= $lista['id'] ?>">

                    <label>Nombre de la tarea:</label>
                    <input type="text" name="nombre" required>

                    <label>Descripción:</label>
                    <textarea name="descripcion" rows="3"></textarea>

                    <label>Asignar a:</label>
                    <select name="asignado_a">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($colaboradores as $colab): ?>
                            <option value="<?= $colab['id'] ?>"><?= htmlspecialchars($colab['nombres'] . " " . $colab['apellidos']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Fecha límite:</label>
                    <input type="date" name="fecha_fin">

                    <label>Prioridad:</label>
                    <select name="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>

                    <div style="margin-top: 10px;">
                        <button type="submit">Guardar Tarea</button>
                        <button type="button" onclick="ocultarFormulario(<?= $lista['id'] ?>)">Cancelar</button>
                    </div>
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

