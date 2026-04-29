<?php
require_once("Models/TCliente.php");
class Clientes extends Controllers
{
    use TCliente;
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        // Solo cargamos permisos de administrador si NO es un cliente
        if ($_SESSION['userData']['idrol'] != RCLIENTES) {
            getPermisos(3);
        }
    }


    // --- NUEVO MÉTODO: PERFIL PARA LA TIENDA ---
    public function perfil()
    {
        $data['tag_page'] = "Mi Perfil - Sayana Luxury";
        $data['page_title'] = "Mi Perfil";
        $data['page_name'] = "mi_perfil";
        $data['page_functions_js'] = "functions_cliente_perfil.js";

        // Carga la vista en Views/Clientes/perfil.php
        $this->views->getView($this, "perfil", $data);
    }
    public function index()
    {
        // Protección: Solo administradores ven el listado de clientes
        if ($_SESSION['userData']['idrol'] == RCLIENTES || empty($_SESSION['permiso_modulo']['r'])) {
            header("Location: " . base_url() . '/dashboard');
            die();
        }
        $data['page_tag'] = "Clientes";
        $data['page_title'] = "Clientes";
        $data['page_name'] = "clientes";
        $data['page_functions_js'] = "functions_clientes.js";
        $this->views->getView($this, "clientes", $data);
    }

    public function setCliente()
    {
        if ($_POST) {
            if (
                empty($_POST['txtNit']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) ||
                empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['listStatus'])
            ) {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            } else {
                $idUsuario = intval($_POST['idUsuario']);
                $strIdentificacion = strClean($_POST['txtNit']);
                $strNombre = ucwords(strClean($_POST['txtNombre']));
                $strApellido = ucwords(strClean($_POST['txtApellido']));
                $intTelefono = intval(strClean($_POST['txtTelefono']));
                $strEmail = strtolower(filter_var(strClean($_POST['txtEmail']), FILTER_SANITIZE_EMAIL));
                $strNomFiscal = strClean($_POST['txtNombreFiscal']);
                $strDirFiscal = strClean($_POST['txtDirFiscal']);
                $intStatus = intval($_POST['listStatus']);
                $intTipoId = 5;
                $request_user = "";

                if ($idUsuario == 0) {
                    $option = 1;
                    if ($_SESSION['permiso_modulo']['w']) {
                        // Generamos contraseña para el correo y luego el hash para la BD
                        $strPasswordInput = empty($_POST['txtPassword']) ? passGenerator() : $_POST['txtPassword'];
                        $strPasswordHash = hash("SHA256", $strPasswordInput);

                        $request_user = $this->model->insertCliente($strIdentificacion, $strNombre, $strApellido, $intTelefono, $strEmail, $strPasswordHash, $intTipoId, $strIdentificacion, $strNomFiscal, $strDirFiscal);
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No tienes permisos para realizar esta acción.');
                        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                        die();
                    }
                } else {
                    $option = 2;
                    if ($_SESSION['permiso_modulo']['u']) {
                        $strPasswordHash = empty($_POST['txtPassword']) ? "" : hash("SHA256", $_POST['txtPassword']);
                        $request_user = $this->model->updateCliente($idUsuario, $strIdentificacion, $strNombre, $strApellido, $intTelefono, $strEmail, $intStatus, $strPasswordHash, $strIdentificacion, $strNomFiscal, $strDirFiscal);
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No tienes permisos para actualizar estos datos.');
                        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                        die();
                    }
                }

                if ($request_user > 0) {
                    if ($option == 1) {
                        $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');

                        // --- BLINDAJE: ENVÍO DE CORREO SOLO PARA NUEVOS ---
                        $nombreUsuario = $strNombre . ' ' . $strApellido;
                        $dataUsuario = array(
                            'nombreUsuario' => $nombreUsuario,
                            'email' => $strEmail,
                            'password' => $strPasswordInput, // Contraseña real (no hash)
                            'asunto' => 'Bienvenido a ' . NOMBRE_EMPRESA,
                            'url_login' => base_url() . '/login'
                        );
                        sendEmail($dataUsuario, 'email_bienvenida');
                        // -------------------------------------------------

                    } else {
                        $arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
                    }
                } else if ($request_user == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! La identificación o el email ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getCliente($idpersona)
    {
        if ($_SESSION['permiso_modulo']['r']) {
            $idusuario = intval($idpersona);
            if ($idusuario > 0) {
                $arrData = $this->model->selectCliente($idusuario);
                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                } else {
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function getClientes()
    {
        if ($_SESSION['permiso_modulo']['r']) {
            $arrData = $this->model->selectClientes();

            for ($i = 0; $i < count($arrData); $i++) {
                $btnView = '';
                $btnEdit = '';
                $btnDelete = '';

                // 1. Botón Ver (Solo si tiene permiso 'r')
                if ($_SESSION['permiso_modulo']['r']) {
                    $btnView = '<button class="btn btn-secondary btn-sm btnPermisoRol" onClick="fntViewCliente(' . $arrData[$i]['idpersona'] . ')" title="Ver"><i class="fas fa-eye"></i></button>';
                }

                // 2. Botón Editar (Solo si tiene permiso 'u' de Update)
                if ($_SESSION['permiso_modulo']['u']) {
                    $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditCliente(' . $arrData[$i]['idpersona'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                }

                // 3. Botón Eliminar (Solo si tiene permiso 'd' de Delete)
                if ($_SESSION['permiso_modulo']['d']) {
                    $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelCliente(' . $arrData[$i]['idpersona'] . ')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }

                // Formatear Status con Badges
                $arrData[$i]['status'] = ($arrData[$i]['status'] == 1) ?
                    '<span class="badge badge-success">Activo</span>' :
                    '<span class="badge badge-danger">Inactivo</span>';

                // Agrupar botones en la columna de opciones
                $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // --- ESTA ES LA FUNCIÓN QUE TE FALTABA ---
    public function delCliente()
    {
        if ($_POST) {
            if ($_SESSION['permiso_modulo']['d']) {
                $intIdpersona = intval($_POST['idUsuario']);
                $requestDelete = $this->model->deleteCliente($intIdpersona);
                if ($requestDelete) {
                    $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el cliente');
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar al cliente.');
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function getPedidosTab()
    {
        $idpersona = $_SESSION['idUser'];
        $data['pedidos'] = $this->getPedidosT($idpersona);
        $this->views->getView($this, "tabla_pedidos", $data);
    }
}
