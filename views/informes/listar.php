<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



require_once __DIR__ . "/../../models/Informe.php";

$proyecto_id = $proyecto_id ?? $_GET['proyecto_id'] ?? null;
if (!$proyecto_id) die("Proyecto no especificado");

$usuario = $_SESSION['usuario'] ?? null;
$rol = $usuario['rol'] ?? null;



$informeModel = new Informe();
$informes = $informeModel->listarPorProyecto($proyecto_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informes del Proyecto</title>
    <link rel="stylesheet" href="../../assets/css/estilos.css">

   </head>
<body>
    <nav>
        <h2>📄 Informes del Proyecto</h2>
        <div>
            <a href="../proyectos/listar.php" class="btn btn-secondary">← Volver a proyectos</a>
            <?php if ($rol === "gestor" || $rol === "administrador"): ?>
                <a href="../informes/crear.php?proyecto_id=<?= $proyecto_id ?>" class="btn btn-primary">+ Crear Informe</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="contenido">
        <?php if (!empty($informes)): ?>
            <div>
                <?php if ($rol === "gestor" || $rol === "administrador"): ?>
                <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Generado por</th>
                        <th>Fecha</th>
                        <th>PDF</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($informes as $i): ?>
                        <tr>
                            <td><?= htmlspecialchars($i['titulo']) ?></td>
                            <td>
                                <span class="badge badge-<?= $i['tipo'] ?>">
                                    <?= ucfirst($i['tipo']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($i['autor_nombre'] . " " . $i['autor_apellido']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($i['fecha_generacion'])) ?></td>
                            <td>
                                <?php if (!empty($i['archivo_pdf'])): ?>
                                    <!-- Enlace en tu tabla -->

                                    <a href="../../controllers/InformeController.php?accion=descargar&id=<?= $i['id'] ?>" target="_blank">
                                        📄 Descargar PDF
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="acciones">
                                <?php if ($rol === "gestor" || $rol === "administrador"): ?>
                                    <a href="../../controllers/InformeController.php?accion=eliminar&id=<?= $i['id'] ?>&proyecto_id=<?= $proyecto_id ?>" 
                                       onclick="return confirm('¿Estás seguro de eliminar este informe?')" 
                                    >🗑️</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
        

        
    </div>

    <!-- Debug info - REMOVER EN PRODUCCIÓN -->
    <?php if (isset($_GET['debug'])): ?>
        <div style="background: #f8f9fa; border: 1px solid #ccc; padding: 10px; margin: 20px 0;">
            <h4>Debug Info:</h4>
            <p><strong>Proyecto ID:</strong> <?= $proyecto_id ?></p>
            <p><strong>Total informes:</strong> <?= count($informes) ?></p>
            <p><strong>Usuario rol:</strong> <?= $rol ?></p>
            <pre><?= print_r($informes, true) ?></pre>
        </div>
    <?php endif; ?>
</body>
</html> 