<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid black; padding:5px; text-align:center; }
        th { background-color:#eee; }
    </style>
</head>
<body>
    <h2>📋 Informe de Proyectos</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th>Inicio</th><th>Fin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= $p['nombre'] ?></td>
                    <td><?= $p['descripcion'] ?></td>
                    <td><?= $p['estado'] ?></td>
                    <td><?= $p['fecha_inicio'] ?></td>
                    <td><?= $p['fecha_fin'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
