<?php
require_once __DIR__ . "/../../controllers/TareaController.php";
require_once __DIR__ . "/../../config/auth.php";

requireRole(["gestor","administrador","colaborador"]);

$proyecto_id = $_GET['proyecto_id'];
$controller = new TareaController();
$tareas = $controller->listar($proyecto_id);

// Agrupamos por estado
$estados = ["pendiente","en progreso","completada","bloqueada"];
$agrupadas = [];
foreach ($estados as $e) {
    $agrupadas[$e] = array_filter($tareas, fn($t) => $t['estado'] === $e);
}
?>
<h2>📌 Tablero de Tareas (Kanban)</h2>
<div style="display:flex; gap:20px;">
    <?php foreach ($estados as $estado): ?>
        <div style="flex:1; border:1px solid #ccc; padding:10px;">
            <h3><?php echo ucfirst($estado); ?></h3>
            <?php foreach ($agrupadas[$estado] as $t): ?>
                <div style="border:1px solid #999; margin:5px; padding:5px;">
                    <strong><?php echo $t['titulo']; ?></strong><br>
                    Responsable: <?php echo $t['responsable']; ?><br>
                    <a href="../../controllers/TareaController.php?accion=estado&id=<?php echo $t['id']; ?>&estado=en progreso&proyecto_id=<?php echo $proyecto_id; ?>">➡️ Mover</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
