<?php
require_once __DIR__ . "/../../controllers/ProyectoController.php";
require_once __DIR__ . "/../../config/auth.php";

requireLogin();
$controller = new ProyectoController();
$proyectos = $controller->listar();
$usuario = $_SESSION['usuario'];
$titulo = "Listado de Proyectos"; // <- opcional, para usar en header
?>


<h2>Proyectos</h2>

<?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
    <a href="router.php?page=tareas/crear.php" class="btn btn-primary mb-3">+ Crear Proyecto</a>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Gestor</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
   <tbody>
    <?php foreach ($proyectos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td>
                <a href="router.php?page=proyectos/detalle&id=<?= $p['id'] ?>">
                    <?= $p['nombre'] ?>
                </a>
            </td>
            <td><?= $p['gestor'] ?? '-' ?></td>
            <td><?= $p['cliente'] ?? '-' ?></td>
            <td><?= $p['estado'] ?></td>
            <td>
                <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
                    <!-- ✅ CORRECTO: Usar router para tareas -->
                    <a href="router.php?page=tareas/tablero&proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Tareas 📋</a>
                    
                    <!-- ✅ CORRECTO: Usar router para editar -->
                    <a href="router.php?page=proyectos/editar&id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar ✏️</a>
                    
                    <!-- ✅ CORRECTO: Controller para eliminar (POST action) -->
                    <a href="controllers/ProyectoController.php?accion=eliminar&id=<?= $p['id'] ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('¿Seguro que deseas eliminar este proyecto?')">Eliminar 🗑️</a>
                    
                    <!-- ✅ CORRECTO: Usar router para informes -->
                    <a href="router.php?page=informes/generar&proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">Informes 📑</a>
                
                <?php elseif ($usuario['rol'] === "colaborador"): ?>
                    <a href="router.php?page=tareas/tablero&proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Mis Tareas 📌</a>
                
                <?php elseif ($usuario['rol'] === "cliente"): ?>
                    <a href="router.php?page=informes/generar&proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">Informes 📑</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
</table>

