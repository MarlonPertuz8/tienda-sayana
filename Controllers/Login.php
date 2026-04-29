<?php
use Google\Client as Google_Client;
use Google\Service\Oauth2 as Google_Service_Oauth2;

class Login extends Controllers
{
    public function __construct()
    {
        session_start();

        if (isset($_SESSION['login']) && empty($_POST)) {

            $origen = $_SESSION['login_origen'] ?? 'admin';

            if ($origen == 'tienda') {
                header('Location: ' . base_url() . '/tienda');
            } else {
                header('Location: ' . base_url() . '/dashboard');
            }
            exit;
        }
        parent::__construct();
    }

    /**
     * Vista de Login para el Administrador
     */
    public function index()
    {
        $data['tag_page'] = "Login - Sayana Tienda Virtual";
        $data['page_title'] = "Login";
        $data['page_name'] = "login";
        $data['page_functions_js'] = "functions_login.js";
        $this->views->getView($this, "login", $data);
    }

    /**
     * Vista de Login para la Tienda (Sayana Luxury)
     */
    public function tienda()
    {
        $data['tag_page'] = "Login - Sayana Luxury";
        $data['page_title'] = "Iniciar Sesión";
        $data['page_name'] = "login_tienda";
        $data['page_functions_js'] = "functions_login.js";

        // Carga la vista Views/Login/loginTienda.php
        $this->views->getView($this, "loginTienda", $data);
    }

// public function google()
// {
//     // Carga de la librería de Google
//     require_once 'vendor/autoload.php';

//     $client = new Google_Client();
//     $client->setClientId('904034245815-15ldd38euv8nip5h41ui1fcsf6jv6a3j.apps.googleusercontent.com');
//     $client->setClientSecret('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');
//     $client->setRedirectUri(base_url() . '/login/google');
//     $client->addScope("email");
//     $client->addScope("profile");

//     if (!isset($_GET['code'])) {
//         // PASO 1: Redirigir al usuario a Google para autorizar
//         $authUrl = $client->createAuthUrl();
//         header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
//         exit;
//     } else {
//         // PASO 2: Google nos devuelve un código, lo intercambiamos por un token
//         $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        
//         if (isset($token['error'])) {
//             header('Location: ' . base_url() . '/login');
//             exit;
//         }
        
//         $client->setAccessToken($token);

//         // PASO 3: Obtener la información del perfil del usuario
//         $google_oauth = new Google_Service_Oauth2($client);
//         $google_info = $google_oauth->userinfo->get();

//         // Estructura de datos que espera tu modelo loginGoogle()
//         $arrDatosGoogle = array(
//             'google_id' => $google_info->id,
//             'nombre'    => $google_info->name,
//             'email'     => $google_info->email
//         );

//         // PASO 4: Llamada al modelo para autenticar o registrar
//         $requestLogin = $this->model->loginGoogle($arrDatosGoogle);

//         if (!empty($requestLogin)) {
//             if ($requestLogin['status'] == 1) {
//                 // PASO 5: Crear la sesión (Lógica idéntica a loginUser)
//                 session_regenerate_id(true);
//                 $_SESSION['idUser'] = $requestLogin['idpersona'];
//                 $_SESSION['login'] = true;
//                 $_SESSION['timeout'] = time();
                
//                 // Obtenemos los datos completos del usuario para el sistema
//                 $_SESSION['userData'] = $this->model->sessionLogin($_SESSION['idUser']);

//                 /* * REDIRECCIÓN BASADA EN ROL (Basado en tu tabla 'rol')
//                 * ID 5 = Cliente -> Va a la Tienda
//                 * Otros IDs (1,2,3,4) -> Van al Dashboard
//                 */
//                 if ($requestLogin['rolid'] == 5) {
//                     $_SESSION['login_origen'] = 'tienda';
//                     header('Location: ' . base_url() . '/tienda');
//                 } else {
//                     $_SESSION['login_origen'] = 'admin';
//                     header('Location: ' . base_url() . '/dashboard');
//                 }
//             } else {
//                 // Usuario existe pero está inactivo (status != 1)
//                 header('Location: ' . base_url() . '/login?error=inactivo');
//             }
//         } else {
//             // Error al procesar los datos en el modelo
//             header('Location: ' . base_url() . '/login?error=error_data');
//         }
//         exit;
//     }
// }

    /**
     * Proceso de autenticación único para Admin y Tienda
     */
    public function loginUser()
{
    if ($_POST) {

        // 🔥 ORIGEN REAL DEL FORM
        $origen = isset($_POST['origen']) ? $_POST['origen'] : 'admin';

        if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
            $arrResponse = array('status' => false, 'msg' => 'Datos incorrectos.');
        } else {

            $strUsuario = strtolower(strClean($_POST['txtEmail']));
            $strPassword = hash("SHA256", $_POST['txtPassword']);
            $requestUser = $this->model->loginUser($strUsuario, $strPassword);

            if (empty($requestUser)) {
                $arrResponse = array('status' => false, 'msg' => 'Usuario o contraseña incorrectos.');
            } else {

                if ($requestUser['status'] == 1) {

                    session_regenerate_id(true);
                    $_SESSION['idUser'] = $requestUser['idpersona'];
                    $_SESSION['login'] = true;
                    $_SESSION['timeout'] = time();
                    $_SESSION['userData'] = $this->model->sessionLogin($_SESSION['idUser']);

                    // 🔥 GUARDAMOS ORIGEN CORRECTO
                    $_SESSION['login_origen'] = $origen;

                    // 🔥 REDIRECCIÓN CLARA
                    if ($origen === 'admin') {
                        $urlRedirect = base_url() . '/dashboard';
                    } else {
                        $urlRedirect = base_url() . '/tienda';
                    }

                    $arrResponse = array(
                        'status' => true,
                        'url' => $urlRedirect
                    );

                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                }
            }
        }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}

    public function resetPass()
    {
        if ($_POST) {
            if (empty($_POST['txtEmailReset'])) {
                $arrResponse = array('status' => false, 'msg' => 'Ingrese un correo.');
            } else {
                $token = token();
                $strEmail = strtolower(strClean($_POST['txtEmailReset']));
                $arrData = $this->model->getUserEmail($strEmail);

                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Usuario no existente.');
                } else {
                    $idpersona = $arrData['idpersona'];
                    $nombreUsuario = $arrData['nombre'] . ' ' . $arrData['apellido'];

                    $url_recovery = base_url() . '/login/confirmUser/' . $strEmail . '/' . $token;
                    $requestUpdate = $this->model->setTokenUser($idpersona, $token);

                    $dataUsuario = array(
                        'nombreUsuario' => $nombreUsuario,
                        'email' => $strEmail,
                        'asunto' => 'Recuperar cuenta - ' . NOMBRE_REMITENTE,
                        'url_recovery' => $url_recovery
                    );

                    if ($requestUpdate) {
                        $sendEmail = sendEmail($dataUsuario, 'email_cambioPassword');
                        if ($sendEmail) {
                            $arrResponse = array('status' => true, 'msg' => 'Hemos enviado un correo para restablecer tu contraseña.');
                        } else {
                            $arrResponse = array('status' => false, 'msg' => 'Error al enviar correo, inténtelo nuevamente.');
                        }
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Error al procesar la solicitud.');
                    }
                }
            }
            sleep(1);
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
    }

    public function confirmUser(string $params)
    {
        if (empty($params)) {
            header('Location: ' . base_url());
        } else {
            $arrParams = explode(',', $params);
            $strEmail = strClean($arrParams[0]);
            $strToken = strClean($arrParams[1]);
            $arrResponse = $this->model->getUsuario($strEmail, $strToken);
            if (empty($arrResponse)) {
                header("Location: " . base_url());
            } else {
                $data['page_tag'] = "Cambiar contraseña";
                $data['page_name'] = "cambiar_contrasenia";
                $data['page_title'] = "Cambiar Contraseña";
                $data['email'] = $strEmail;
                $data['token'] = $strToken;
                $data['idpersona'] = $arrResponse['idpersona'];
                $data['page_functions_js'] = "functions_login.js";
                $this->views->getView($this, "cambiar_password", $data);
            }
        }
        die();
    }

    public function setPassword()
    {
        if (empty($_POST['idUsuario']) || empty($_POST['txtEmail']) || empty($_POST['txtToken']) || empty($_POST['txtPassword']) || empty($_POST['txtPasswordConfirm'])) {
            $arrResponse = array('status' => false, 'msg' => 'Error de datos');
        } else {
            $intIdpersona = intval($_POST['idUsuario']);
            $strPassword = $_POST['txtPassword'];
            $strPasswordConfirm = $_POST['txtPasswordConfirm'];
            $strEmail = strClean($_POST['txtEmail']);
            $strToken = strClean($_POST['txtToken']);

            if ($strPassword != $strPasswordConfirm) {
                $arrResponse = array('status' => false, 'msg' => 'Las contraseñas no son iguales.');
            } else {
                $arrResponseUser = $this->model->getUsuario($strEmail, $strToken);

                if (empty($arrResponseUser)) {
                    $arrResponse = array('status' => false, 'msg' => 'El enlace ha expirado o los datos son incorrectos.');
                } else {
                    $strPassword = hash("SHA256", $strPassword);
                    $requestPass = $this->model->insertPassword($intIdpersona, $strPassword);

                    if ($requestPass) {
                        $arrResponse = array('status' => true, 'msg' => 'Contraseña actualizada con éxito.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso, intente más tarde.');
                    }
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
