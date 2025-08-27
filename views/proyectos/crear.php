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
        <label>ID Gestor</label><input type="number" name="gestor_id"><br>
        <label>ID Cliente</label><input type="number" name="cliente_id"><br>
        <label>Presupuesto</label><input type="number" step="0.01" name="presupuesto"><br>
        <button type="submit">Guardar</button>
    </form>
    <a href="listar.php">Volver</a>
</body>
</html>
