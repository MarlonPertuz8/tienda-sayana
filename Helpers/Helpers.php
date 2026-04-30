<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'Libraries/PhpMailer/Exception.php';
require_once 'Libraries/PhpMailer/PHPMailer.php';
require_once 'Libraries/PhpMailer/SMTP.php';
// 1. Retornar la url base del proyecto
function base_url()
{
    return BASE_URL;
}

// 2. Control de sesión e inactividad
function sessionStart()
{
    session_start();
    $inactive = 1800; // 30 minutos

    if (isset($_SESSION['timeout'])) {
        $session_life = time() - $_SESSION['timeout'];
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: " . base_url() . "/login");
            exit;
        }
    }
    $_SESSION['timeout'] = time();
}

// 3. Recursos y Assets
function media()
{
    return BASE_URL . "/Assets";
}

function headerAdmin($data = "")
{
    $view_header = "Views/Template/header_admin.php";
    require_once($view_header);
}

function footerAdmin($data = "")
{
    $view_footer = "Views/Template/footer_admin.php";
    require_once($view_footer);
}

function headerTienda($data = "")
{
    $view_header = "Views/Template/header_tienda.php";
    require_once($view_header);
}

function footerTienda($data = "")
{
    $view_footer = "Views/Template/footer_tienda.php";
    require_once($view_footer);
}

function dep($data)
{
    $format  = '<pre>';
    $format .= print_r($data, true);
    $format .= '</pre>';
    return $format;
}

function getModal(string $nameModal, $data)
{
    $view_modal = "Views/Template/Modals/{$nameModal}.php";
    require_once $view_modal;
}

function sendEmail($data, $template){
    $asunto = $data['asunto'];
    $emailDestino = $data['email'];
    $empresa = NOMBRE_REMITENTE;
    $remitente = EMAIL_REMITENTE;

    // Cargar la plantilla HTML
    ob_start();
    require_once("Views/Template/Email/" . $template . ".php");
    $mensaje = ob_get_clean();

    // Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();                                            
        $mail->Host       = MAIL_HOST;                    
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = MAIL_USER;               
        $mail->Password   = MAIL_PASS;               
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Usa SSL
        $mail->Port       = MAIL_PORT;                              

        // Remitente y Destinatario
        $mail->setFrom($remitente, $empresa);
        $mail->addAddress($emailDestino);     

        // Contenido del correo
        $mail->isHTML(true);                                  
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;
        $mail->CharSet = 'UTF-8';

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Si hay error, puedes verlo con: echo "Error: {$mail->ErrorInfo}";
        return false;
    }
}

function getPermisos(int $idmodulo)
{
    require_once("Models/PermisosModel.php");
    $objPermisos = new PermisosModel();

    if (!empty($_SESSION['userData'])) {
        // 1. Identificar el ROL correctamente
        $idrol = $_SESSION['userData']['rolid'] ?? $_SESSION['userData']['idrol'] ?? 3;
        
        // 2. Obtener TODOS los permisos del rol
        $arrPermisos = $objPermisos->permisosModulo($idrol);
        $permisos = array();

        if (!empty($arrPermisos)) {
            foreach ($arrPermisos as $permiso) {
                $permisos[$permiso['moduloid']] = $permiso;
            }
        }

        // 3. Guardar en sesión
        $_SESSION['permisos'] = $permisos;
        
        // 4. Asignar el permiso específico del módulo actual
        // Si no hay permisos definidos para ese ID, asignamos un array con ceros para no romper la vista
        if(isset($permisos[$idmodulo])){
            $_SESSION['permiso_modulo'] = $permisos[$idmodulo];
        } else {
            $_SESSION['permiso_modulo'] = array('r' => 0, 'w' => 0, 'u' => 0, 'd' => 0);
        }

    } else {
        // Evitar bucles en el login
        $url = isset($_GET['url']) ? strtolower($_GET['url']) : "";
        if ($url != "" && $url != "login" && $url != "login/loginuser") {
            header('Location: '.base_url().'/login');
            die();
        }
    }
}

function sessionUser(int $idpersona)
{
    require_once("Models/LoginModel.php");
    $objLogin = new LoginModel();
    $request = $objLogin->sessionLogin($idpersona);

    if (!empty($request)) {
        $_SESSION['userData'] = $request;
    }
    return $request;
}

function uploadImage(array $data, string $name){
    if (empty($data['tmp_name'])) return false;
    
    $url_temp = $data['tmp_name'];
    $destino = 'Assets/images/uploads/' . $name;
    if (move_uploaded_file($url_temp, $destino)) {
        return true; // Retornamos true si se movió con éxito
    }
    
    return false;
}
function deleteFile(string $name){
    $filepath = 'Assets/images/uploads/' . $name;
    if (file_exists($filepath)) {
        $imagenesProtegidas = ['portada_categoria.png', 'default.jpg'];
        if (!in_array($name, $imagenesProtegidas)) {
            unlink($filepath);
        }
    }
}

function strClean($strCadena){
    $string = preg_replace(['/\s+/','/^\s|\s$/'],[' ',''], $strCadena); // Limpia espacios
    $string = trim($string); //Elimina espacios en blanco al inicio y al final
    $string = stripslashes($string); // Elimina las \ invertidas
    $string = str_ireplace("<script>","",$string);
    $string = str_ireplace("</script>","",$string);
    $string = str_ireplace("<script src","",$string);
    $string = str_ireplace("<script type=","",$string);
    $string = str_ireplace("SELECT * FROM","",$string);
    $string = str_ireplace("DELETE FROM","",$string);
    $string = str_ireplace("INSERT INTO","",$string);
    $string = str_ireplace("SELECT COUNT(*) FROM","",$string);
    $string = str_ireplace("DROP TABLE","",$string);
    $string = str_ireplace("OR '1'='1","",$string); // <-- NUEVO: Evita bypass de login
    $string = str_ireplace('OR "1"="1"',"",$string); // <-- NUEVO
    $string = str_ireplace('OR ´1´=´1´',"",$string); // <-- NUEVO
    $string = str_ireplace("is NULL; --","",$string);
    $string = str_ireplace("LIKE '","",$string);
    $string = str_ireplace('LIKE "',"",$string);
    $string = str_ireplace("LIKE ´","",$string);
    $string = str_ireplace("OR 'a'='a","",$string);
    $string = str_ireplace('OR "a"="a',"",$string);
    $string = str_ireplace("OR ´a´=´a","",$string);
    $string = str_ireplace("--","",$string);
    $string = str_ireplace("^","",$string);
    $string = str_ireplace("[","",$string);
    $string = str_ireplace("]","",$string);
    $string = str_ireplace("==","",$string);
    return $string;
}

function clear_cadena(string $cadena)
{
    $string = trim($cadena);
    $string = str_replace(array('Á','á','É','é','Í','í','Ó','ó','Ú','ú','Ñ','ñ'), array('A','a','E','e','I','i','O','o','U','u','N','n'), $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s]/', '', $string);
    $string = preg_replace('/\s+/', '-', $string);
    return $string;
}

function passGenerator($length = 10)
{
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $longitudCadena = strlen($cadena);
    $pass = "";
    for ($i = 1; $i <= $length; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}

function token()
{
    return bin2hex(random_bytes(10)).'-'.bin2hex(random_bytes(10));
}

function formatMoneda($cantidad){
    $cantidad = str_replace([' ', '$', ','], '', $cantidad);
    $decimal = defined('SPD') ? SPD : ".";
    $millar  = defined('SPM') ? SPM : ",";
    if (is_numeric($cantidad)) {
        return number_format((float)$cantidad, 0, $decimal, $millar);
    }
    return $cantidad;
}

function getFile(string $url, $data)
{
    ob_start();
    $view_file = "Views/{$url}.php";
    if (file_exists($view_file)) {
        require_once($view_file);
    } else {
        echo "Error: El archivo {$view_file} no existe.";
    }
    return ob_get_clean();
}
function meses() {
    return array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
}