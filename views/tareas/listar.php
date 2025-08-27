<?php
require_once __DIR__ . "/../../controllers/TareaController.php";
require_once __DIR__ . "/../../config/auth.php";

requireRole(["gestor","administrador","colaborador"]);

$proyecto_id = $_GET['proyecto_id'];
$controller = new TareaController();
$tareas = $controller->listar($proyecto_id);
?>
<h2>📋 Lista de Tareas</h2>
<a href="crear.php?proyecto_id=<?php echo $proyecto_id; ?>">➕ Nueva Tarea</a>
<table border="1">
    <tr>
        <th>Título</th>
        <th>Responsable</th>
        <th>Estado</th>
        <th>Prioridad</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($tareas as $t): ?>
        <tr>
            <td><?php echo $t['titulo']; ?></td>
            <td><?php echo $t['responsable']; ?></td>
            <td><?php echo $t['estado']; ?></td>
            <td><?php echo $t['prioridad']; ?></td>
            <td>
                <a href="../../controllers/TareaController.php?accion=estado&id=<?php echo $t['id']; ?>&estado=completada&proyecto_id=<?php echo $proyecto_id; ?>">✔️ Completar</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
