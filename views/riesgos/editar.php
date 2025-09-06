<h2>Editar Riesgo</h2>
<form method="POST">
    <input type="hidden" name="proyecto_id" value="<?php echo $riesgo['proyecto_id']; ?>">
    
    <label>Descripción:</label><br>
    <textarea name="descripcion" required><?php echo $riesgo['descripcion']; ?></textarea><br><br>
    
    <label>Impacto:</label><br>
    <select name="impacto" required>
        <option value="Bajo"  <?= ($riesgo['impacto']=="Bajo") ? "selected" : "" ?>>Bajo</option>
        <option value="Medio" <?= ($riesgo['impacto']=="Medio") ? "selected" : "" ?>>Medio</option>
        <option value="Alto"  <?= ($riesgo['impacto']=="Alto") ? "selected" : "" ?>>Alto</option>
    </select><br><br>

    <label>Probabilidad:</label><br>
    <select name="probabilidad" required>
        <option value="Baja"  <?= ($riesgo['probabilidad']=="Baja") ? "selected" : "" ?>>Baja</option>
        <option value="Media" <?= ($riesgo['probabilidad']=="Media") ? "selected" : "" ?>>Media</option>
        <option value="Alta"  <?= ($riesgo['probabilidad']=="Alta") ? "selected" : "" ?>>Alta</option>
    </select><br><br>

    <label>Mitigación:</label><br>
    <textarea name="medidas_mitigacion" required><?php echo $riesgo['medidas_mitigacion']; ?></textarea><br><br>

    <button type="submit">Actualizar</button>
</form>
<a href="/proyectum/controllers/RiesgoController.php?accion=listar&id_proyecto=<?= $riesgo['proyecto_id'] ?>">⬅ Volver a riesgos</a>

