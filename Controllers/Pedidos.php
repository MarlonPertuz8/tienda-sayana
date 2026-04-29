<?php
class Pedidos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header("Location: " . base_url() . '/login');
            die();
        }
    }

    public function index()
    {

        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        $idPersona = intval($_SESSION['idUser']);
        if ($idPersona <= 0) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        $data['tag_page'] = "Mis Pedidos | Sayana Luxury";
        $data['page_title'] = "Mis Pedidos";
        $data['page_name'] = "mis_pedidos";

        $data['pedidos'] = $this->model->selectPedidosUsuario($idPersona);
        $this->views->getView($this, "pedidos", $data);
    }

   public function orden($idpedido){
    $idpedido = intval($idpedido);

    if (empty($idpedido) || $idpedido <= 0) {
        header("Location: " . base_url() . '/pedidos');
        exit;
    }

    $data['tag_page'] = "Orden de Compra";
    $data['page_title'] = "Pedido detallado";
    $data['page_name'] = "orden";

    // 1. Obtener la información del pedido
    $arrPedido = $this->model->getPedido($idpedido);

    if (empty($arrPedido) || empty($arrPedido['pedido'])) {
        header("Location: " . base_url() . '/pedidos');
        exit;
    }

    // 2. NUEVA LÓGICA: Obtener el número secuencial para el cliente
    $idPersona = $arrPedido['pedido']['personaid'];
    $numeroSecuencial = $this->model->getNumeroSecuencial($idPersona, $idpedido);
    
    // Guardamos el "39" en el array para la vista
    $arrPedido['numero_secuencial'] = $numeroSecuencial;

    $data['orden'] = $arrPedido;
    $this->views->getView($this, "orden", $data);
}

   public function setConfirmarPedido(){
    if ($_POST) {
        if (empty($_POST['idpedido']) || empty($_POST['status'])) {
            $arrResponse = array('status' => false, 'msg' => 'Datos incorrectos.');
        } else {
            $idpedido = intval($_POST['idpedido']); // El 69 (ID Real)
            $status   = intval($_POST['status']);

            $request = $this->model->updateStatusPedido($idpedido, $status);

            if ($request) {
                // --- INICIO DE LA CORRECCIÓN ---
                // 1. Obtenemos el ID del usuario y el número secuencial (ej: 41)
                $idUsuario = $this->model->getPersonaIdByPedido($idpedido);
                $nroAmigable = $this->model->getNumeroSecuencial($idUsuario, $idpedido);

                $msgAlerta = "";
                $msgNotificacion = "";

                switch ($status) {
                    case 1:
                        $msgAlerta = 'Pedido recibido.';
                        $msgNotificacion = "¡Tu compra ha sido exitosa! Pronto iniciaremos el proceso.";
                        break;
                    case 2:
                        $msgAlerta = '¡Pedido confirmado! El cliente ya puede ver el progreso.';
                        // USAMOS $nroAmigable en lugar de $idpedido
                        $msgNotificacion = "Tu pedido #$nroAmigable está siendo procesado ahora mismo.";
                        break;
                    case 3:
                        $msgAlerta = '¡Pedido marcado como ENVIADO con éxito!';
                        $infoPedido = $this->model->getPedido($idpedido);

                        $esLocal = !empty($infoPedido['pedido']['barrioid']) ? true : false;
                        if ($esLocal) {
                            $msgNotificacion = "¡Tu pedido #$nroAmigable va en camino! Nuestro repartidor local te entregará pronto.";
                        } else {
                            $msgNotificacion = "¡Tu compra #$nroAmigable ha sido enviada! Revisa tu número de guía en los detalles.";
                        }
                        break;
                    case 4:
                        $msgAlerta = '¡Pedido marcado como ENTREGADO correctamente!';
                        $msgNotificacion = "¡Tu pedido #$nroAmigable ha sido entregado! Gracias por confiar en Sayana.";
                        break;
                    default:
                        $msgAlerta = 'Estado actualizado.';
                        $msgNotificacion = "Tu pedido #$nroAmigable tiene una nueva actualización de estado.";
                        break;
                }

                if ($idUsuario) {
                    $this->model->insertNotificacion(
                        $idUsuario,
                        "Actualización de Pedido #$nroAmigable", // Título corregido
                        $msgNotificacion,
                        $idpedido // Pasamos el ID real para que el enlace funcione
                    );
                }
                // --- FIN DE LA CORRECCIÓN ---

                $arrResponse = array('status' => true, 'msg' => $msgAlerta);
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al actualizar en la base de datos.');
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}
}
