<?php
require_once __DIR__ . '/../models/Riesgo.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../config/auth.php';

$risk = new Riesgo();
$accion = $_GET['accion'] ?? '';

switch ($accion) {

    case 'listar':
        requireRole(["gestor", "administrador"]);
        $idProyecto = $_GET['id_proyecto'] ?? null;
        if (!$idProyecto) die("Proyecto no especificado.");

        $riesgos = $risk->listarPorProyecto($idProyecto);
        require __DIR__ . '/../views/riesgos/listar.php';
        break;

    case 'crear':
        requireRole(["gestor", "administrador"]);
        $idProyecto = $_GET['id_proyecto'] ?? null;
        if (!$idProyecto) die("Proyecto no especificado.");

        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->listar();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST['estado'] = $_POST['estado'] ?? 'pendiente';
            $risk->crear($_POST);
            header("Location: RiesgoController.php?accion=listar&id_proyecto=" . $_POST['proyecto_id']);
            exit();
        }

        require __DIR__ . '/../views/riesgos/crear.php';
        break;

        $risk->crear($_POST);
       header("Location: /proyectum/router.php?page=riesgo/crear&proyecto_id=" . $_POST['proyecto_id']);
        exit();
    }

    $idProyecto = $_GET['id_proyecto'] ?? null;
    if (!$idProyecto) {
        die("Proyecto no especificado.");
    }

    // Traer lista de usuarios para el select
    $usuarioModel = new Usuario();
    $usuarios = $usuarioModel->listar();

    require __DIR__ . '/../views/riesgos/crear.php';
    break;
    case 'editar':
        requireRole(["gestor", "administrador"]);
        $id = $_GET['id'] ?? null;
        if (!$id) die("Riesgo no especificado.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $_SESSION['usuario'] ?? null;
            if (!$usuario) die("Debe estar logueado.");

            $_POST['responsable_id'] = $usuario['id'];
            $risk->actualizar($id, $_POST);
            header("Location: /proyectum/router.php?page=riesgos/listar&id_proyecto=" . $_POST['proyecto_id']);
            exit();
        }

        $riesgo = $risk->obtener($id);
        require __DIR__ . '/../views/riesgos/editar.php';
        break;

    case 'eliminar':
        requireRole(["gestor", "administrador"]);
        $id = $_GET['id'] ?? null;
        $idProyecto = $_GET['id_proyecto'] ?? null;
        if (!$id || !$idProyecto) die("Faltan datos.");

        $risk->eliminar($id);
        header("Location: /proyectum/router.php?page=riesgos/listar&id_proyecto=" . $idProyecto);
        exit();
        break;

    default:
        echo "Acción no válida.";
        break;
}
