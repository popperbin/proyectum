<?php
require_once __DIR__ . "/../../controllers/UsuarioController.php";
require_once __DIR__ . "/../../models/Usuario.php";

$usuarioModel = new Usuario();
$usuario = $usuarioModel->obtenerPorId($_GET['id']);
?>

<h2>Editar Usuario</h2>
<form method="POST" action="../../controllers/UsuarioController.php?accion=actualizar">
    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
    <input type="text" name="nombres" value="<?= $usuario['nombres'] ?>" required><br>
    <input type="text" name="apellidos" value="<?= $usuario['apellidos'] ?>" required><br>
    <input type="text" name="cedula" value="<?= $usuario['cedula'] ?>" required><br>
    <input type="email" name="email" value="<?= $usuario['email'] ?>" required><br>
    <input type="text" name="direccion" value="<?= $usuario['direccion'] ?>"><br>
    <input type="text" name="celular" value="<?= $usuario['celular'] ?>"><br>
    <input type="text" name="cargo" value="<?= $usuario['cargo'] ?>"><br>

    <label>Rol:</label>
    <select name="rol">
        <option value="administrador" <?= $usuario['rol'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
        <option value="gestor" <?= $usuario['rol'] == 'gestor' ? 'selected' : '' ?>>Gestor</option>
        <option value="colaborador" <?= $usuario['rol'] == 'colaborador' ? 'selected' : '' ?>>Colaborador</option>
        <option value="cliente" <?= $usuario['rol'] == 'cliente' ? 'selected' : '' ?>>Cliente</option>
    </select><br>

    <label>Estado:</label>
    <select name="estado">
        <option value="activo" <?= $usuario['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= $usuario['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
    </select><br>

    <button type="submit">Actualizar</button>
</form>
