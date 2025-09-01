<?php require_once __DIR__ . "/../../config/auth.php"; requireRole(["administrador", "gestor"]); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Proyecto</title>
</head>
<body>
    <h2>Nuevo Proyecto</h2>
    <form method="POST" action="../../controllers/ProyectoController.php?accion=crear">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <textarea name="descripcion" placeholder="Descripción"></textarea><br>
        <label>Fecha Inicio</label><input type="date" name="fecha_inicio"><br>
        <label>Fecha Fin</label><input type="date" name="fecha_fin"><br>
        <label>Estado</label>
        <select name="estado">
            <option value="planificacion">Planificación</option>
            <option value="activo">Activo</option>
            <option value="en_pausa">En pausa</option>
            <option value="completado">Completado</option>
        <select><br>
        <label>Gestor</label>
            <select name="gestor_id">
                <?php foreach($gestores as $gestor): ?>
                    <option value="<?= $gestor["id"] ?>"
                        <?= ($gestor['id'] == $gestorLogueado) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($gestor['nombres'] . ' ' . $gestor['apellidos']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>ID Cliente</label><input type="number" name="cliente_id"><br>
    
        <button type="submit">Guardar</button>
    </form>
    <a href="listar.php">Volver</a>
</body>
</html>
