<?php
class Reportes extends Controllers {
    public function __construct() {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        require_once("Models/ReportesModel.php");
        $this->model = new ReportesModel();
        getPermisos(4);
    }

    public function movimientosPDF($fInicio = null, $fFin = null) {
        if ($_SESSION['permisos'][4]['r']) {
            // Captura de fechas (Parámetro o GET)
            $f1 = $fInicio ?: ($_GET['f1'] ?? null);
            $f2 = $fFin ?: ($_GET['f2'] ?? null);

            if (!$f1 || !$f2) {
                header('Location: ' . base_url() . '/inventario');
                die();
            }

            $data['page_title'] = "Reporte de Movimientos";
            $data['fecha_inicio'] = $f1;
            $data['fecha_fin'] = $f2;
            $data['reporteData'] = $this->model->selectMovimientosFiltrado($f1, $f2);

            if (empty($data['reporteData'])) {
                echo "No hay movimientos registrados en este rango de fechas.";
                die();
            }

            $this->generarPDF("Views/Reportes/movimientosPDF.php", $data);
        } else {
            header('Location: ' . base_url() . '/dashboard');
        }
        die();
    }

    public function inventarioPDF($fInicio = null, $fFin = null) {
        if ($_SESSION['permisos'][4]['r']) {
            $f1 = $fInicio ?: ($_GET['f1'] ?? null);
            $f2 = $fFin ?: ($_GET['f2'] ?? null);

            $data['page_title'] = "Reporte de Inventario Valorizado";
            $data['reporteData'] = $this->model->selectInventarioValorizado($f1, $f2);
            
            $this->generarPDF("Views/Reportes/inventarioPDF.php", $data);
        }
        die();
    }

    private function generarPDF($view, $data) {
        require_once("Libraries/Core/TCPDF/tcpdf.php");
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetTitle($data['page_title']);
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        ob_start();
        require_once($view);
        $html = ob_get_clean();

        $pdf->writeHTML($html, true, false, true, false, '');
        if (ob_get_length()) ob_end_clean();
        $pdf->Output('reporte_sayana.pdf', 'I');
    }
}