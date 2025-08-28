<h2>Crear Usuario</h2>
<form method="POST" action="../../controllers/UsuarioController.php?accion=crear">
    <input type="text" name="nombres" placeholder="Nombres" required><br>
    <input type="text" name="apellidos" placeholder="Apellidos" required><br>
    <input type="text" name="cedula" placeholder="Cédula" required><br>
    <input type="email" name="email" placeholder="Correo" required><br>
    <input type="password" name="password" placeholder="Contraseña" required><br>

    <label>Rol:</label>
    <select name="rol">
        <option value="administrador">Administrador</option>
        <option value="gestor">Gestor</option>
        <option value="colaborador">Colaborador</option>
        <option value="cliente">Cliente</option>
    </select><br>

    <button type="submit">Guardar</button>
</form>
