<?php
class Proveedores extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }

        getPermisos(4); // Asegúrate de que el ID 4 tenga permisos
    }

    public function index()
    {

        if (empty($_SESSION['permisos'][4]['r'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Proveedores - Sayana Luxury";
        $data['page_title'] = "Gestión de Proveedores";
        $data['page_name'] = "proveedores";
        $data['page_functions_js'] = "functions_proveedores.js";
        $this->views->getView($this, "proveedores", $data);
    }

    public function setProveedor()
    {
        if ($_POST) {
            if (empty($_POST['txtNombre'])) {
                $arrResponse = array("status" => false, "msg" => "El nombre es obligatorio.");
            } else {
                $idProveedor = intval($_POST['idProveedor']);
                $strNombre = strClean($_POST['txtNombre']);
                $strNit = strClean($_POST['txtNit']);
                $strTelefono = strClean($_POST['txtTelefono']);
                $strDireccion = strClean($_POST['txtDireccion']);

                if ($idProveedor == 0) {
                    $request = $this->model->insertProveedor($strNombre, $strNit, $strTelefono, $strDireccion);
                    $option = 1;
                } else {
                    $request = $this->model->updateProveedor($idProveedor, $strNombre, $strNit, $strTelefono, $strDireccion);
                    $option = 2;
                }

                if ($request > 0) {
                    $arrResponse = array('status' => true, 'msg' => ($option == 1) ? 'Proveedor guardado correctamente.' : 'Datos actualizados.');
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'No se pudo almacenar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getProveedores()
    {
        $arrData = $this->model->selectProveedores();
        for ($i = 0; $i < count($arrData); $i++) {
            // Botón Ver (Amarillo/Dorado)
            $btnView = '<button class="btn btn-info btn-sm btnPermisoRol" onclick="fntViewInfo(' . $arrData[$i]['idproveedor'] . ')" title="Ver Proveedor" style="border-radius: 10px; background-color: #f39c12; border: none;"><i class="far fa-eye"></i></button>';
            // Botón Editar (Azul Oscuro/Pro)
            $btnEdit = '<button class="btn btn-primary btn-sm shadow-sm" onclick="fntEditInfo(' . $arrData[$i]['idproveedor'] . ')" title="Editar" style="border-radius: 10px; background-color: #2c3e50; border: none;"><i class="fas fa-pencil-alt"></i></button>';
            // Botón Eliminar (Rojo)
            $btnDelete = '<button class="btn btn-danger btn-sm shadow-sm" onclick="fntDelInfo(' . $arrData[$i]['idproveedor'] . ')" title="Eliminar" style="border-radius: 10px; border: none;"><i class="far fa-trash-alt"></i></button>';

            $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getProveedor($id)
    {
        $idp = intval($id);
        if ($idp > 0) {
            $arrData = $this->model->selectProveedor($idp);
            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delProveedor()
    {
        if ($_POST) {
            $idProveedor = intval($_POST['idProveedor']);
            $requestDelete = $this->model->deleteProveedor($idProveedor);
            if ($requestDelete) {
                $arrResponse = array('status' => true, 'msg' => 'Proveedor eliminado.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
