<?php
require_once __DIR__ . "/../models/Informe.php";
require_once __DIR__ . "/../models/Proyecto.php";
require_once __DIR__ . "/../config/auth.php";


requireLogin();

class InformeController {
    private $informeModel;
    private $proyectoModel;

    public function __construct() {
        $this->informeModel = new Informe();
        $this->proyectoModel = new Proyecto();
    }

    private function validarProyectoActivo($proyecto_id) {
        $proyecto = $this->proyectoModel->obtenerPorId($proyecto_id);

        if (!$proyecto) {
            die("Proyecto no encontrado.");
        }
        if (strtolower(trim($proyecto['estado'])) !== 'activo') {
            die("Este proyecto está inactivo. No puedes realizar esta acción.");
        }

        return $proyecto;
    }

    public function listar($proyecto_id) {
        $proyecto = $this->validarProyectoActivo($proyecto_id);
        $informes = $this->informeModel->listarPorProyecto($proyecto_id);
        require "../views/informes/listar.php";
    }

    public function crear($data) {
        requireRole(["gestor"]);

        $usuario_id = $_SESSION['usuario']['id'];
        $this->validarProyectoActivo($data['proyecto_id']);

        try {
            // Insertar informe en BD (sin PDF físico)
            $informe_id = $this->informeModel->crear([
                "proyecto_id"   => $data['proyecto_id'],
                "titulo"        => $data['titulo'],
                "contenido"     => $data['contenido'],
                "tipo"          => $data['tipo'],
                "archivo_pdf"   => null,
                "generado_por"  => $usuario_id,
                "comentarios"   => $data['comentarios'],
                "observaciones" => $data['observaciones']
            ]);

              $pdfUrl = "router.php?page=informes/acciones&accion=descargar&id=" . $informe_id;

            return [
                "ok"     => true,
                "informe_id" => $informe_id,
                "pdfUrl"     => $pdfUrl,
                "url" => "router.php?page=informes/listar&proyecto_id=" . $data['proyecto_id']
            ];

        } catch (Exception $e) {
            return [
                "ok"    => false,
                "error" => $e->getMessage()
            ];
        }
    }

    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $this->informeModel->eliminar($id);

        return [
            "ok"  => true,
            "url" => "router.php?page=informes/listar&proyecto_id=" . $proyecto_id
        ];
    }
    public function descargar($id) {
        requireRole(["gestor", "admin"]);

        // 1. Buscar informe
        $informe = $this->informeModel->obtenerPorId($id);
        if (!$informe) {
            die("Informe no encontrado");
        }

        // 3. Preparar el PDF (aquí con Dompdf como ejemplo)
        require_once __DIR__ . "/../vendor/autoload.php";
        $dompdf = new Dompdf\Dompdf();
        $html = "<h1>{$informe['titulo']}</h1>
        <p>{$informe['contenido']}</p>
        <p>Proyecto ID: {$informe['proyecto_id']}</p>
        <p>{$informe['comentarios']}</p>
        <p>{$informe['observaciones']}</p>";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if (ob_get_length()) ob_end_clean();

        // 4. Forzar descarga inmediata
        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=informe_" . $id . ".pdf");
        echo $dompdf->output();
        exit;
    }

}

// --- Router interno de este controlador ---
$controller = new InformeController();

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {

        case 'eliminar':
            if (isset($_GET['id'], $_GET['proyecto_id'])) {
                $controller->eliminar($_GET['id'], $_GET['proyecto_id']);
            }
            break;

        case 'listar':
            if (isset($_GET['proyecto_id'])) {
                $controller->listar($_GET['proyecto_id']);
            }
            break;

        case 'descargar':
            if (isset($_GET['id'])) {
                $controller->descargar($_GET['id']);
            }
            break;
    }
}
