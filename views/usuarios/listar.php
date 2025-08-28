<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: ../../index.php");
    exit();
}

require_once __DIR__ . "/../../controllers/UsuarioController.php";
$controller = new UsuarioController();
$usuarios = $controller->listar();
?>

<?php include __DIR__ . "/../layout/header.php"; ?>

<h2>Gestión de Usuarios</h2>
<a href="crear.php">Crear Usuario</a>
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['nombres'] . " " . $u['apellidos'] ?></td>
            <td><?= $u['email'] ?></td>
            <td><?= $u['rol'] ?></td>
            <td><?= $u['estado'] ?></td>
            <td>
                <a href="editar.php?id=<?= $u['id'] ?>">Editar</a>
                <a href="../../controllers/UsuarioController.php?accion=eliminar&id=<?= $u['id'] ?>"
                   onclick="return confirm('¿Seguro que deseas eliminar este usuario?');">Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php include __DIR__ . "/../layout/footer.php"; ?>

</table>
