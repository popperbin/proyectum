<?php 
require_once __DIR__ . "/../../config/auth.php"; 
requireLogin();
?>

<h2>⚠️ Riesgos del Proyecto</h2>

<!-- Botón para crear riesgo -->
<a href="/proyectum/controllers/RiesgoController.php?accion=crear&id_proyecto=<?= $idProyecto ?>">➕ Registrar Riesgo</a>

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
            <td><?= htmlspecialchars($r['descripcion']) ?></td>
            <td><?= htmlspecialchars($r['impacto']) ?></td>
            <td><?= htmlspecialchars($r['probabilidad']) ?></td>
            <td><?= htmlspecialchars($r['medidas_mitigacion']) ?></td>
            <td>
                <a href="/proyectum/controllers/RiesgoController.php?accion=editar&id=<?= $r['id'] ?>&id_proyecto=<?= $idProyecto ?>">editar</a>
                <a href="/proyectum/controllers/RiesgoController.php?accion=eliminar&id=<?= $r['id'] ?>&id_proyecto=<?= $idProyecto ?>" onclick="return confirm('¿Eliminar riesgo?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<!-- Volver al listado de proyectos -->
<a href="/proyectum/controllers/ProyectoController.php?accion=listar">⬅️ Volver a Proyectos</a>