<?php
require_once __DIR__ . "/../config/auth.php";
requireRole(["administrador", "gestor"]);

require_once __DIR__ . "/../models/Proyecto.php";

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InformeController {

    public function proyectosPDF() {
        $proyectos = Proyecto::listar();

        ob_start();
        include __DIR__ . "/../views/informes/proyectos_pdf.php";
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("proyectos.pdf", ["Attachment" => true]);
    }

    public function proyectosExcel() {
        $proyectos = Proyecto::listar();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Descripción');
        $sheet->setCellValue('D1', 'Estado');
        $sheet->setCellValue('E1', 'Fecha Inicio');
        $sheet->setCellValue('F1', 'Fecha Fin');

        // Datos
        $row = 2;
        foreach ($proyectos as $p) {
            $sheet->setCellValue("A$row", $p['id']);
            $sheet->setCellValue("B$row", $p['nombre']);
            $sheet->setCellValue("C$row", $p['descripcion']);
            $sheet->setCellValue("D$row", $p['estado']);
            $sheet->setCellValue("E$row", $p['fecha_inicio']);
            $sheet->setCellValue("F$row", $p['fecha_fin']);
            $row++;
        }

        // Exportar
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="proyectos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save("php://output");
    }
}

// --- Router simple ---
$accion = $_GET['accion'] ?? '';
$controller = new InformeController();

switch ($accion) {
    case 'proyectos_pdf':
        $controller->proyectosPDF();
        break;
    case 'proyectos_excel':
        $controller->proyectosExcel();
        break;
    default:
        echo "Acción no válida";
}
