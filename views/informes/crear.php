<?php
require_once __DIR__ . "/../../config/auth.php";
requireLogin();

$proyecto_id = $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) {
    die("Proyecto no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Informe</title>
    <link rel="stylesheet" href="../../assets/css/estilos.css">
</head>
<body>
    <nav>
        <h2>Crear Informe</h2>
        <a href="../proyectos/listar.php">⬅ Volver a proyectos</a>
    </nav>

    <div class="formulario">
        <form method="POST" action="../../controllers/InformeController.php?accion=crear">
            <input type="hidden" name="proyecto_id" value="<?= $proyecto_id ?>">

            <label>Título del Informe:</label>
            <input type="text" name="titulo" required>

            <label>Contenido / Comentarios:</label>
            <textarea name="contenido" rows="6"></textarea>

            <label>Tipo de Informe:</label>
            <select name="tipo">
                <option value="progreso" selected>Progreso</option>
                <option value="final">Final</option>
                <option value="riesgos">Riesgos</option>
                <option value="personalizado">Personalizado</option>
            </select>

            <div style="margin-top: 10px;">
                <button type="submit">Generar PDF y Guardar</button>
                <a href="listar.php?proyecto_id=<?= $proyecto_id ?>"><button type="button">Cancelar</button></a>
            </div>
        </form>
    </div>
</body>
</html>
