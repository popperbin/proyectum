<h2>Registrar Riesgo</h2>
<form method="POST">
    <input type="hidden" name="id_proyecto" value="<?php echo $_GET['id_proyecto']; ?>">
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
    <textarea name="mitigacion" required></textarea><br><br>

    <button type="submit">Guardar</button>
</form>
