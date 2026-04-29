<?php 
class Contacto extends Controllers {
    public function __construct() {
        parent::__construct();
        // Iniciar sesión solo si es necesario
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

public function index() {
    $data['tag_page'] = "Contacto | Sayana";
    $data['page_title'] = "Contacto";
    $data['page_name'] = "contacto";
    // ESTA LÍNEA ES VITAL:
    $data['page_functions_js'] = "functions_contacto.js"; 
    
    $data['carrito'] = isset($_SESSION['arrCarrito']) ? $_SESSION['arrCarrito'] : [];
    $this->views->getView($this, "contacto", $data);
}

   public function enviarMensaje() {
    if ($_POST) {
        if (empty($_POST['nombreContacto']) || empty($_POST['emailContacto']) || empty($_POST['mensaje'])) {
            $arrResponse = array("status" => false, "msg" => "Todos los campos son obligatorios.");
        } else {
            $nombre = strClean($_POST['nombreContacto']);
            $email  = strClean($_POST['emailContacto']);
            $mensaje = strClean($_POST['mensaje']);
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $ip = $_SERVER['REMOTE_ADDR'];
            $dispositivo = "Web"; // Puedes personalizar esto

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $arrResponse = array("status" => false, "msg" => "El formato del correo no es válido.");
            } else {
                // Guardar en la tabla 'contacto' que creamos
                $request = $this->model->insertContacto($nombre, $email, $mensaje, $useragent, $ip);
                
                if ($request > 0) {
                    $arrResponse = array("status" => true, "msg" => "¡Gracias! Tu mensaje ha sido recibido en nuestro buzón.");
                    
                    // Opcional: Si tienes configurado PHPMailer, podrías avisarte a ti mismo aquí
                    // $dataMail = array('nombre' => $nombre, 'email' => $email, 'mensaje' => $mensaje);
                    // sendEmail($dataMail, "asunto_nuevo_contacto");
                } else {
                    $arrResponse = array("status" => false, "msg" => "Error al registrar el mensaje.");
                }
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}
}