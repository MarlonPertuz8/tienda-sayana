<?php
class Cupones extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();

        // Cargamos el modelo manualmente para evitar el error de 'null'
        require_once("Models/CuponModel.php");
        $this->model = new CuponModel();

        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(8);
    }

    public function index()
    {
        if (empty($_SESSION['permiso_modulo']['r'])) {
            header("Location:" . base_url() . '/dashboard');
            die();
        }
        $data['page_id'] = 8;
        $data['page_tag'] = "Cupones | Sayana";
        $data['page_title'] = "Cupones De Descuento";
        $data['page_name'] = "cupones";
        $data['page_functions_js'] = "functions_cupones.js";
        $this->views->getView($this, "cupones", $data);
    }

    public function getCupones()
    {
        $arrData = $this->model->selectCupones();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnView = '';
            $btnEdit = '';
            $btnDelete = '';

            if ($arrData[$i]['status'] == 1) {
                $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            // Botón Ver (Añade esta línea)
            $btnView = '<button class="btn btn-info btn-sm btnPermisoRol" onClick="fntViewCupon(' . $arrData[$i]['idcupon'] . ')" title="Ver"><i class="far fa-eye"></i></button>';

            $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditCupon(' . $arrData[$i]['idcupon'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
            $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelCupon(' . $arrData[$i]['idcupon'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

            // Agregamos los botones a la columna options
            $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getCupon($idcupon)
    {
        if ($_SESSION['permiso_modulo']['r']) {
            $idcupon = intval($idcupon);
            if ($idcupon > 0) {
                $arrData = $this->model->selectCupon($idcupon);
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

    public function setCupon()
    {
        if ($_POST) {
            // CORRECCIÓN: Validamos los nuevos campos que pide tu DB
            if (empty($_POST['txtCodigo']) || empty($_POST['txtDescuento']) || empty($_POST['txtLimite']) || empty($_POST['txtFechaVencimiento'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            } else {
                $idCupon = intval($_POST['idCupon']);
                $strCodigo = strClean(strtoupper($_POST['txtCodigo']));
                $intDescuento = intval($_POST['txtDescuento']);
                $intLimite = intval($_POST['txtLimite']);
                $strFechaVenc = strClean($_POST['txtFechaVencimiento']);
                $intStatus = intval($_POST['listStatus']);

                if ($idCupon == 0) {
                    // Crear nuevo (Enviamos los 5 parámetros que definiste en el Model)
                    if ($_SESSION['permiso_modulo']['w']) {
                        $request_cupon = $this->model->insertCupon($strCodigo, $intDescuento, $intLimite, $strFechaVenc, $intStatus);
                        $option = 1;
                    }
                } else {
                    // Actualizar (Enviamos los 6 parámetros)
                    if ($_SESSION['permiso_modulo']['u']) {
                        $request_cupon = $this->model->updateCupon($idCupon, $strCodigo, $intDescuento, $intLimite, $strFechaVenc, $intStatus);
                        $option = 2;
                    }
                }

                if ($request_cupon > 0) {
                    $msg = ($option == 1) ? 'Datos guardados correctamente.' : 'Datos Actualizados correctamente.';
                    $arrResponse = array('status' => true, 'msg' => $msg);
                } else if ($request_cupon == 'exist') {
                    $arrResponse = array('status' => false, 'msg' => '¡Atención! El código ya existe.');
                } else {
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function aplicarCupon()
    {
        if ($_POST) {
            if (empty($_SESSION['login'])) {
                echo json_encode(array("status" => false, "msg" => 'Debes iniciar sesión para usar cupones.'), JSON_UNESCAPED_UNICODE);
                die();
            }

            $strCupon = strClean(strtoupper($_POST['cupon']));
            $idUsuario = $_SESSION['idUser'];

            require_once("Models/CuponModel.php");
            $objCupon = new CuponModel();

            // 1. Validamos si el cupón existe y está vigente
            $requestCupon = $objCupon->consultarCupon($strCupon);

            if (!empty($requestCupon)) {
                $idCupon = $requestCupon['idcupon'];

                // 2. Validamos si el usuario ya lo usó
                $yaUsado = $objCupon->verificarCuponUsuario($idUsuario, $idCupon);

                if (empty($yaUsado)) {
                    $arrResponse = array(
                        "status" => true,
                        "msg" => 'Cupón aplicado.',
                        "descuento" => $requestCupon['descuento']
                    );
                    $_SESSION['descuento_aplicado'] = $requestCupon['descuento'];
                    $_SESSION['id_cupon_uso'] = $idCupon; // Guardamos el ID para registrarlo al final
                } else {
                    $arrResponse = array("status" => false, "msg" => 'Ya has utilizado este cupón anteriormente.');
                }
            } else {
                $arrResponse = array("status" => false, "msg" => 'Cupón no válido, agotado o expirado.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function delCupon()
    {
        if ($_POST) {
            if ($_SESSION['permiso_modulo']['d']) {
                $intIdCupon = intval($_POST['idCupon']);
                $requestDelete = $this->model->deleteCupon($intIdCupon);
                if ($requestDelete) {
                    $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el cupón');
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el cupón.');
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
}
