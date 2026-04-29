<?php
class PedidosA extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();

        if (empty($_SESSION['login']) || $_SESSION['userData']['idrol'] != 1) {
            header('Location: ' . base_url() . '/login');
            die();
        }

        getPermisos(5);
    }

    public function index()
    {
        if (empty($_SESSION['permisos'][5]['r'])) {
            header("Location: " . base_url() . '/dashboard');
            die();
        }

        $data['page_tag'] = "Gestión de Pedidos - Sayana";
        $data['page_title'] = "Administración de Pedidos";
        $data['page_name'] = "pedidos_admin";
        // Esta línea carga el JS en el index (la tabla)
        $data['page_functions_js'] = "functions_pedidos_a.js";

        $this->views->getView($this, "pedidosa", $data);
    }

    public function getPedidosA()
    {
        if (empty($_SESSION['permisos'][5]['r'])) {
            die();
        }

        $arrData = $this->model->selectPedidosTotal();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnView = '<a href="' . base_url() . '/pedidosA/orden/' . $arrData[$i]['idpedido'] . '" class="btn btn-dark btn-sm" title="Ver Detalle"><i class="far fa-eye"></i></a>';
            $btnEstado = '';

            if ($arrData[$i]['status'] == 1) {
                $btnEstado = '<button class="btn btn-sm btn-success" title="Confirmar Pedido" onclick="fntCambiarStatus(' . $arrData[$i]['idpedido'] . ', 2)"><i class="fas fa-check"></i></button>';
            } elseif ($arrData[$i]['status'] == 2) {
                $btnEstado = '<button class="btn btn-sm btn-info" title="Marcar como Enviado" onclick="fntCambiarStatus(' . $arrData[$i]['idpedido'] . ', 3)"><i class="fas fa-truck"></i></button>';
            } elseif ($arrData[$i]['status'] == 3) {
                $btnEstado = '<button class="btn btn-sm btn-primary" title="Confirmar Entrega" onclick="fntCambiarStatus(' . $arrData[$i]['idpedido'] . ', 4)"><i class="fas fa-box-open"></i></button>';
            }

            $arrData[$i]['options'] = '<div class="text-center">' . $btnEstado . ' ' . $btnView . '</div>';

            if ($arrData[$i]['status'] == 1) {
                $arrData[$i]['status'] = '<span class="badge badge-danger">Pendiente</span>';
            } elseif ($arrData[$i]['status'] == 2) {
                $arrData[$i]['status'] = '<span class="badge badge-warning">Procesando</span>';
            } elseif ($arrData[$i]['status'] == 3) {
                $arrData[$i]['status'] = '<span class="badge badge-info">Enviado</span>';
            } elseif ($arrData[$i]['status'] == 4) {
                $arrData[$i]['status'] = '<span class="badge badge-success">Entregado</span>';
            }

            $arrData[$i]['monto'] = formatMoneda($arrData[$i]['monto']);
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function orden($idpedido)
    {
        if (!is_numeric($idpedido)) {
            header("Location: " . base_url() . '/pedidosA');
            die();
        }

        if (empty($_SESSION['permisos'][5]['r'])) {
            header("Location: " . base_url() . '/dashboard');
            die();
        }

        $data['page_tag'] = "Pedido - Sayana Luxury";
        $data['page_title'] = "DETALLE DEL PEDIDO #" . $idpedido;
        $data['page_name'] = "detalle_pedido";
        
        // ✅ CORRECCIÓN CRÍTICA: Debes incluir el JS también en la función orden
        // Sin esta línea, el navegador no carga tus funciones en la vista del detalle
        $data['page_functions_js'] = "functions_pedidos_a.js";

        $data['pedido'] = $this->model->selectPedido($idpedido);

        if (empty($data['pedido'])) {
            header("Location: " . base_url() . '/pedidosA');
            die();
        }

        $this->views->getView($this, "orden", $data);
    }

    public function setGuia()
    {
        if ($_POST) {
            $idpedido = intval($_POST['idpedido']);
            $strGuia = strClean($_POST['guia']);

            if ($idpedido > 0 && !empty($strGuia)) {
                $request = $this->model->updateGuia($idpedido, $strGuia);
                if ($request) {
                    echo json_encode(["status" => true, "msg" => "Guía guardada correctamente."]);
                } else {
                    echo json_encode(["status" => false, "msg" => "No se pudo guardar la guía."]);
                }
            }
            die();
        }
    }
}