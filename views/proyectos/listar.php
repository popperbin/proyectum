<?php
require_once __DIR__ . "/../../controllers/ProyectoController.php";
require_once __DIR__ . "/../../config/auth.php";

requireLogin();
$controller = new ProyectoController();
$proyectos = $controller->listar();
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Proyectos</title>
</head>
<body>
    <h2>Proyectos</h2>
    <a href="../../dashboard.php">⬅ Volver a proyectos</a>
    <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
        
        <a href="crear.php">+ Crear Proyecto</a>
    <?php endif; ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Gestor</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($proyectos as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><a href="detalle.php?id=<?= $p['id'] ?>"><?= $p['nombre'] ?></a></td>
                <td><?= $p['gestor'] ?? '-' ?></td>
                <td><?= $p['cliente'] ?? '-' ?></td>
                <td><?= $p['estado'] ?></td>
                <td>
                    <?php if (in_array($usuario['rol'], ["administrador", "gestor"])): ?>
                    <?php if ($p['estado'] === 'activo'): ?>
                        <a href="../tareas/tablero.php?proyecto_id=<?= $p['id'] ?>">Tareas 📋</a>
                        <a href="editar.php?id=<?= $p['id'] ?>">Editar ✏️</a>
                        <a href="../../controllers/ProyectoController.php?accion=inactivar&id=<?= $p['id'] ?>"
                            onclick="return confirm('¿Seguro que deseas inactivar este proyecto?');">
                            Inactivar
                        </a>
                        <a href="../informes/listar.php?proyecto_id=<?= $p['id'] ?>">
                            📑 Ver informes
                        </a>
                        <a href="../../controllers/RiesgoController.php?accion=listar&id_proyecto=<?= $p['id'] ?>">⚠️ Gestión de riesgos</a>
                        <?php else: ?>
                            <a href="../../controllers/ProyectoController.php?accion=activar&id=<?= $p['id'] ?>"
                             onclick="return confirm('¿Quieres reactivar este proyecto?');">
                                Activar
                            </a>
                        <?php endif; ?>
                        <?php elseif ($usuario['rol'] === "colaborador"): ?>
                            <?php if ($p['estado'] === 'activo'): ?>
                                <a href="../tareas/tablero.php?proyecto_id=<?= $p['id'] ?>">Mis Tareas 📌</a>
                        <?php endif; ?>
                        <?php elseif ($usuario['rol'] === "cliente"): ?>
                            <?php if ($p['estado'] === 'activo'): ?>
                                <a href="../informes/listar.php?proyecto_id=<?= $p['id'] ?>">Informes 📑</a>
                        <?php endif; ?>
                    <?php endif; ?>

                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
