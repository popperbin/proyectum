<?php
require_once __DIR__ . '/../models/Riesgo.php';
require_once __DIR__ . '/../config/auth.php';

$risk = new Riesgo();

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
        requireRole(["gestor", "administrador"]);
        $idProyecto = $_GET['id_proyecto'];
        $riesgos = $risk->listarPorProyecto($idProyecto);
        require __DIR__ . '/../views/riesgos/listar.php';
        break;

    case 'crear':
        requireRole(["gestor", "administrador"]);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $risk->crear($_POST);
            header("Location: RiesgoController.php?accion=listar&id_proyecto=" . $_POST['id_proyecto']);
            exit();
        }
        require __DIR__ . '/../views/riesgos/crear.php';
        break;

    case 'editar':
        requireRole(["gestor", "administrador"]);
        $id = $_GET['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $risk->actualizar($id, $_POST);
            header("Location: RiesgoController.php?accion=listar&id_proyecto=" . $_POST['id_proyecto']);
            exit();
        }
        $riesgo = $risk->obtener($id);
        require __DIR__ . '/../views/riesgos/editar.php';
        break;

    case 'eliminar':
        requireRole(["gestor", "administrador"]);
        $id = $_GET['id'];
        $idProyecto = $_GET['id_proyecto'];
        $risk->eliminar($id);
        header("Location: RiesgoController.php?accion=listar&id_proyecto=" . $idProyecto);
        break;
}
