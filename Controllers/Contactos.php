<?php
class Contactos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
    }

    public function index()
    {
        $data['page_tag'] = "Buzón de Contacto";
        $data['page_title'] = "Conctactos - Sayana Tienda Virtual";
        $data['page_name'] = "contactos";
        $data['page_functions_js'] = "functions_contactos.js";

        $this->views->getView($this, "contactos", $data);
    }

    // 1. Obtener todos los contactos para la tabla
    public function getContactos()
    {
        $arrData = $this->model->selectContactos();

        for ($i = 0; $i < count($arrData); $i++) {
            // Verificamos si existe la llave status para evitar el Warning
            $status = isset($arrData[$i]['status']) ? $arrData[$i]['status'] : 1;

            if ($status == 1) {
                $arrData[$i]['status'] = '<span class="badge badge-danger">Pendiente</span>';
            } else {
                $arrData[$i]['status'] = '<span class="badge badge-success">Atendido</span>';
            }

            $arrData[$i]['options'] = '<div class="text-center">
                <button class="btn btn-info btn-sm" onClick="fntViewContacto(' . $arrData[$i]['id'] . ')" title="Ver"><i class="far fa-eye"></i></button>
                <button class="btn btn-primary btn-sm" onClick="fntRespuesta(' . $arrData[$i]['id'] . ')" title="Responder"><i class="fas fa-reply"></i></button>
                <button class="btn btn-danger btn-sm" onClick="fntDelContacto(' . $arrData[$i]['id'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>
            </div>';
        }

        if (ob_get_length()) ob_clean(); // Limpieza preventiva
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    // 2. Obtener un solo contacto para mostrarlo en los modales
    public function getContacto($idcontacto)
    {
        $id = intval($idcontacto);
        if ($id > 0) {
            $arrData = $this->model->selectContacto($id);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }

            if (ob_get_length()) ob_clean();
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // 3. Procesar la respuesta y enviar email
    public function setRespuesta()
    {
        if ($_POST) {
            if (empty($_POST['idContacto']) || empty($_POST['txtRespuesta']) || empty($_POST['txtEmail'])) {
                $arrResponse = array('status' => false, 'msg' => 'Datos incompletos.');
            } else {
                $idContacto = intval($_POST['idContacto']);
                $emailCliente = strClean($_POST['txtEmail']);
                $mensajeRespuesta = strClean($_POST['txtRespuesta']);

                // Datos para la plantilla de email que creamos (email_respuesta.php)
                $dataEmail = array(
                    'email' => $emailCliente,
                    'asunto' => "Respuesta de Sayana.col",
                    'respuesta' => $mensajeRespuesta
                );

                // Enviamos el correo usando tu función global
                $sendMail = sendEmail($dataEmail, "email_respuesta");

                if ($sendMail) {
                    // Si el correo sale, actualizamos estado a Atendido (2)
                    $request = $this->model->updateContactoStatus($idContacto);
                    if ($request) {
                        $arrResponse = array('status' => true, 'msg' => 'Respuesta enviada correctamente.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Correo enviado, pero error al actualizar estado.');
                    }
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al enviar el correo. Revisa SMTP.');
                }
            }
            
            if (ob_get_length()) ob_clean(); // Evita que basura de PHP rompa el JSON.parse del JS
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

  public function getContactosCount() {
    ob_clean(); 
    $totalMensajes = $this->model->selectTotalContactos(); 
    $arrData = array(
        "status" => true,
        "total" => intval($totalMensajes)
    );

    echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
    die(); 
}
}