<h2>Editar Riesgo</h2>
<form method="POST">
    <input type="hidden" name="id_proyecto" value="<?php echo $riesgo['id_proyecto']; ?>">
    
    <label>Descripción:</label><br>
    <textarea name="descripcion" required><?php echo $riesgo['descripcion']; ?></textarea><br><br>
    
    <label>Impacto:</label><br>
    <select name="impacto" required>
        <option <?php if($riesgo['impacto']=="Bajo") echo "selected"; ?>>Bajo</option>
        <option <?php if($riesgo['impacto']=="Medio") echo "selected"; ?>>Medio</option>
        <option <?php if($riesgo['impacto']=="Alto") echo "selected"; ?>>Alto</option>
    </select><br><br>

    <label>Probabilidad:</label><br>
    <select name="probabilidad" required>
        <option <?php if($riesgo['probabilidad']=="Baja") echo "selected"; ?>>Baja</option>
        <option <?php if($riesgo['probabilidad']=="Media") echo "selected"; ?>>Media</option>
        <option <?php if($riesgo['probabilidad']=="Alta") echo "selected"; ?>>Alta</option>
    </select><br><br>

    <label>Mitigación:</label><br>
    <textarea name="mitigacion" required><?php echo $riesgo['mitigacion']; ?></textarea><br><br>

    <button type="submit">Actualizar</button>
</form>
