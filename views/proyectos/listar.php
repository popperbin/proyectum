<?php
require_once __DIR__ . "/../../controllers/ProyectoController.php";
require_once __DIR__ . "/../../config/auth.php";

requireLogin();
$controller = new ProyectoController();
$proyectos = $controller->listar();
$usuario = $_SESSION['usuario'];
$titulo = "Listado de Proyectos"; // <- opcional, para usar en header
?>

<?php include("../layout/header.php"); ?>

<h2>Proyectos</h2>
<a href="../../dashboard.php" class="btn btn-secondary mb-3">⬅ Volver</a>

<?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
    <a href="crear.php" class="btn btn-primary mb-3">+ Crear Proyecto</a>
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
                <td><a href="detalle.php?id=<?= $p['id'] ?>"><?= $p['nombre'] ?></a></td>
                <td><?= $p['gestor'] ?? '-' ?></td>
                <td><?= $p['cliente'] ?? '-' ?></td>
                <td><?= $p['estado'] ?></td>
                <td>
                    <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
                        <a href="../tareas/tablero.php?proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Tareas 📋</a>
                        <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar ✏️</a>
                        <a href="../../controllers/ProyectoController.php?accion=eliminar&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger">Eliminar 🗑️</a>
                        <a href="../informes/listar.php?proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">Informes 📑</a>
                    
                    <?php elseif ($usuario['rol'] === "colaborador"): ?>
                        <a href="../tareas/tablero.php?proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-info">Mis Tareas 📌</a>
                    
                    <?php elseif ($usuario['rol'] === "cliente"): ?>
                        <a href="../informes/listar.php?proyecto_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">Informes 📑</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include("../layout/footer.php"); ?>
