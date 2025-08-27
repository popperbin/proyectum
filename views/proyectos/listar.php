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
                        <a href="editar.php?id=<?= $p['id'] ?>">✏️</a>
                        <a href="../../controllers/ProyectoController.php?accion=eliminar&id=<?= $p['id'] ?>">🗑️</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
