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

    public function listar($proyecto_id) {
        $informes = $this->informeModel->listarPorProyecto($proyecto_id);

        // echo"<pre>DEBUG: " print_r($informes); echo "</pre>";
        require "../views/informes/listar.php";
    }

    public function crear($data) {
        requireRole(["gestor"]);

        $usuario_id = $_SESSION['usuario']['id'];
        $proyecto = $this->proyectoModel->obtenerPorId($data['proyecto_id']);

        // Generar PDF
        $dompdf = new Dompdf();
        $html = "<h1>{$data['titulo']}</h1>
                 <p>{$data['contenido']}</p>
                 <p>Proyecto: {$proyecto['nombre']}</p>";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "informes/informe_".time().".pdf";
        file_put_contents("../".$filename, $dompdf->output());

        // Guardar en DB
        $this->informeModel->crear([
            'proyecto_id' => $data['proyecto_id'],
            'titulo' => $data['titulo'],
            'contenido' => $data['contenido'] ?? null,
            'tipo' => $data['tipo'] ?? 'progreso',
            'archivo_pdf' => $filename,
            'generado_por' => $usuario_id
        ]);

        header("Location: ../views/informes/listar.php?proyecto_id=" . $data['proyecto_id']);
        exit();
    }

    public function eliminar($id, $proyecto_id) {
        requireRole(["gestor"]);
        $informe = $this->informeModel->obtenerPorId($id);
        if ($informe && $informe['archivo_pdf'] && file_exists("../".$informe['archivo_pdf'])) {
            unlink("../".$informe['archivo_pdf']);
        }
        $this->informeModel->eliminar($id);
        header("Location: ../views/informes/listar.php?proyecto_id=" . $proyecto_id);
        exit();
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
    }
}

