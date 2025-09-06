<?php require_once __DIR__ . "/../../config/auth.php"; requireLogin(); ?>
<h2>⚠️ Riesgos del Proyecto</h2>

<a href="/proyectum/controllers/RiesgoController.php?accion=crear&id_proyecto=<?php echo $_GET['id_proyecto']; ?>">➕ Registrar Riesgo</a>


<?php if (empty($riesgos)): ?>
    <p>No hay riesgos registrados en este proyecto.</p>
    <p>Puedes agregar nuevos riesgos haciendo clic en "Registrar Riesgo".</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Descripción</th>
            <th>Impacto</th>
            <th>Probabilidad</th>
            <th>Mitigación</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($riesgos as $r): ?>
        <tr>
            <td><?php echo $r['descripcion']; ?></td>
            <td><?php echo $r['impacto']; ?></td>
            <td><?php echo $r['probabilidad']; ?></td>
            <td><?php echo $r['medidas_mitigacion']; ?></td>
            <td>
                <a href="/proyectum/controllers/RiesgoController.php?accion=editar&id=<?php echo $r['id']; ?>&id_proyecto=<?php echo $_GET['id_proyecto']; ?>">editar</a>
                <a href="/proyectum/controllers/RiesgoController.php?accion=eliminar&id=<?php echo $r['id']; ?>&id_proyecto=<?php echo $_GET['id_proyecto']; ?>" onclick="return confirm('¿Eliminar riesgo?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<a href="/proyectum/views/proyectos/listar.php">⬅ Volver a proyectos</a>
<br>

