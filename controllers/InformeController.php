<?php
require_once __DIR__ . "/../models/Informe.php";
require_once __DIR__ . "/../models/Proyecto.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../vendor/autoload.php"; // <-- IMPORTANTE
use Dompdf\Dompdf;

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
            die(" Proyecto no encontrado.");
        }
        if (strtolower(trim($proyecto['estado'])) !== 'activo') {
            die(" Este proyecto está inactivo. No puedes realizar esta acción.");
        }

        return $proyecto;
    }
    /**
     * Listar informes de un proyecto
     */
    public function listar($proyecto_id) {
        $proyecto = $this->validarProyectoActivo($proyecto_id);
        $informes = $this->informeModel->listarPorProyecto($proyecto_id);
        require "../views/informes/listar.php";
    }

  public function crear($data) {
        requireRole(["gestor"]);

        $usuario_id = $_SESSION['usuario']['id'];
        $proyecto = $this->validarProyectoActivo($data['proyecto_id']);

        // Generar PDF
        $dompdf = new Dompdf();
        $html = "<h1>{$data['titulo']}</h1>
             <p>{$data['contenido']}</p>
             <p>Proyecto: {$proyecto['nombre']}</p>
             <p>{$data['comentarios']}</p>
             <p>{$data['observaciones']}</p>";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombreArchivo = "informe_" . time() . ".pdf";
        $rutaArchivo   = "informes/" . $nombreArchivo; 
        
        $this->informeModel->crear([
            "proyecto_id" => $data['proyecto_id'],
            "titulo"      => $data['titulo'],
            "contenido"   => $data['contenido'],
            "tipo"        => $data['tipo'],
            "archivo_pdf" => $rutaArchivo,
            "generado_por"=> $usuario_id,
            "comentarios" => $data['comentarios'],
            "observaciones" => $data['observaciones']
        ]);

        // Limpiar buffer y descargar
        if (ob_get_length()) {
         ob_end_clean();
        }
        $dompdf->stream(basename($nombreArchivo), ["Attachment" => true]);
        exit;
    }


    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $informe = $this->informeModel->validarProyectoActivo($id);
        if ($informe && $informe['archivo_pdf'] && file_exists("../".$informe['archivo_pdf'])) {
            unlink("../".$informe['archivo_pdf']);
        }
        $this->informeModel->eliminar($id);
        header("Location: ../views/informes/listar.php?proyecto_id=" . $proyecto_id);
        exit();
    }
    public function descargar($id) {
        $informe = $this->informeModel->obtenerPorId($id);
        $proyecto = $this->validarProyectoActivo($informe['proyecto_id']);

        if (!$informe) {
            die("Informe no encontrado.");
        }

        // Generar el PDF con Dompdf
        $dompdf = new Dompdf();
        $html = "<h1>{$informe['titulo']}</h1>
             <p><strong>Contenido:</strong> {$informe['contenido']}</p>
             <p><strong>Proyecto:</strong> {$proyecto['nombre']}</p>
             <p><strong>Comentarios:</strong> {$informe['comentarios']}</p>
             <p><strong>Observaciones:</strong> {$informe['observaciones']}</p>
             <p><small>Generado por usuario ID: {$informe['generado_por']}</small></p>";

         $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar directamente (sin guardarlo en servidor)
        $dompdf->stream("informe_{$id}.pdf", ["Attachment" => true]);
    exit;
}

}

// --- Router básico ---
$controller = new InformeController();

if (isset($_GET['accion'])) {
    switch ($_GET['accion']) {
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->crear($_POST);
            }
            break;

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

