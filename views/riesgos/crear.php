<?php 
require_once __DIR__ . "/../../config/auth.php"; 
requireLogin();
?>

<h2>Registrar Riesgo</h2>

<form method="POST" action="/proyectum/controllers/RiesgoController.php?accion=crear&id_proyecto=<?= $idProyecto ?>">
    <!-- Hidden input con id del proyecto -->
   <input type="hidden" name="proyecto_id" value="<?= $idProyecto ?>">

    <label>Descripción:</label><br>
    <textarea name="descripcion" required></textarea><br><br>
    
    <label>Impacto:</label><br>
    <select name="impacto" required>
        <option value="Bajo">Bajo</option>
        <option value="Medio">Medio</option>
        <option value="Alto">Alto</option>
    </select><br><br>

    <label>Probabilidad:</label><br>
    <select name="probabilidad" required>
        <option value="Baja">Baja</option>
        <option value="Media">Media</option>
        <option value="Alta">Alta</option>
    </select><br><br>

    <label>Mitigación:</label><br>
    <textarea name="medidas_mitigacion" required></textarea><br><br>

    <label>Responsable:</label><br>
    <select name="responsable_id" required>
        <option value="">-- Seleccione --</option>
        <?php foreach ($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombres'] . " " . $u['apellidos']) ?></option>
        <?php endforeach; ?>
    </select>

    <br><br>
    <button type="submit">Guardar</button>
</form>

<a href="/proyectum/controllers/RiesgoController.php?accion=listar&id_proyecto=<?= $idProyecto ?>">⬅ Volver a riesgos</a>
<br>
