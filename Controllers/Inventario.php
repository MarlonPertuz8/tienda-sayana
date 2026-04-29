<?php
class Inventario extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        require_once("Models/InventarioModel.php");
        $this->model = new InventarioModel();
        getPermisos(4);
    }

    public function index()
    {
        if (empty($_SESSION['permisos'][4]['r'])) {
            header('Location: ' . base_url() . '/dashboard');
        }
        $data['permisos_modulo'] = $_SESSION['permisos'][4];
        $data['page_tag'] = "Inventario - Sayana Luxury";
        $data['page_title'] = "Gestión de Inventario";
        $data['page_name'] = "inventario";
        $data['page_functions_js'] = "functions_inventario.js";
        $this->views->getView($this, "inventario", $data);
    }

    // Registrar o Actualizar entrada
    public function setEntrada()
    {
        if ($_POST) {
            // CORRECCIÓN: Validamos que 'listProveedor' no esté vacío (antes era txtProveedor)
            if (empty($_POST['listProducto']) || empty($_POST['txtCantidad']) || empty($_POST['listProveedor'])) {
                $arrResponse = array("status" => false, "msg" => "Datos mal cargados o incompletos.");
            } else {
                $idEntrada = intval($_POST['idEntrada']);
                $idProducto = intval($_POST['listProducto']);
                $strColor = strClean($_POST['listColor']);
                $intCantidad = intval($_POST['txtCantidad']);
                $floatCosto = floatval($_POST['txtPrecioCosto']);

                // CORRECCIÓN: Ahora capturamos un ID (int), no un String
                $idProveedor = intval($_POST['listProveedor']);

                if ($idEntrada == 0) {
                    // CORRECCIÓN: Pasamos $idProveedor al modelo
                    $request_entrada = $this->model->insertarEntrada($idProducto, $strColor, $intCantidad, $floatCosto, $idProveedor);
                    $option = 1;
                } else {
                    // CORRECCIÓN: Pasamos $idProveedor al modelo
                    $request_entrada = $this->model->updateEntrada($idEntrada, $idProducto, $strColor, $intCantidad, $floatCosto, $idProveedor);
                    $option = 2;
                }

                if ($request_entrada > 0) {
                    if ($option == 1) {
                        $arrResponse = array("status" => true, "msg" => "Ingreso de stock registrado correctamente.");
                    } else {
                        $arrResponse = array("status" => true, "msg" => "Datos actualizados correctamente.");
                    }
                } else {
                    $arrResponse = array("status" => false, "msg" => "No se pudo procesar la solicitud.");
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Obtener una sola entrada para Ver/Editar
    public function getEntrada($id)
    {
        if ($_SESSION['permisos'][4]['r']) {
            $idEntrada = intval($id);
            if ($idEntrada > 0) {
                $arrData = $this->model->selectEntrada($idEntrada);
                if (empty($arrData)) {
                    $arrResponse = array("status" => false, "msg" => "Datos no encontrados.");
                } else {
                    // Formateamos el precio para el modal de 'Ver'
                    $arrData['precio_costo_format'] = SMONEY . ' ' . formatMoneda($arrData['precio_costo']);
                    $arrResponse = array("status" => true, "data" => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    // Eliminar / Anular entrada
    public function delEntrada()
    {
        if ($_POST) {
            if ($_SESSION['permisos'][4]['d']) {
                $idEntrada = intval($_POST['idEntrada']);
                $requestDelete = $this->model->deleteEntrada($idEntrada);
                if ($requestDelete) {
                    $arrResponse = array("status" => true, "msg" => "Se ha anulado la entrada y restaurado el stock.");
                } else {
                    $arrResponse = array("status" => false, "msg" => "Error al eliminar la entrada.");
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function getSelectProductos()
    {
        $htmlOptions = "<option value=''>Seleccione un producto</option>";
        $arrData = $this->model->selectProductosInventario();
        if (count($arrData) > 0) {
            foreach ($arrData as $producto) {
                $htmlOptions .= '<option value="' . $producto['idproducto'] . '" data-colores="' . $producto['colores'] . '">' . $producto['nombre'] . '</option>';
            }
        }
        echo $htmlOptions;
        die();
    }

   public function getEntradas()
{
    if ($_SESSION['permisos'][4]['r']) {
        // Llamamos al modelo que ahora trae el NOMBRE del producto y el NOMBRE del proveedor
        $arrData = $this->model->selectEntradas();

        for ($i = 0; $i < count($arrData); $i++) {
            // Botones de acción
            $btnView = '<button class="btn btn-info btnPermisoRol btn-sm" onclick="fntViewInfo(' . $arrData[$i]['identrada'] . ')" title="Ver detalle"><i class="far fa-eye"></i></button>';
            $btnEdit = ($_SESSION['permisos'][4]['u']) ? '<button class="btn btn-primary btn-sm" onclick="fntEditInfo(' . $arrData[$i]['identrada'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>' : '';
            $btnDelete = ($_SESSION['permisos'][4]['d']) ? '<button class="btn btn-danger btn-sm" onclick="fntDelInfo(' . $arrData[$i]['identrada'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>' : '';

            // Armamos la columna de opciones con los botones
            $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
            
            // Etiqueta visual para el tipo (Entrada)
            $arrData[$i]['tipo_label'] = '<span class="badge badge-success">Entrada</span>';
            
            // Formateo de moneda (Ej: $ 1.500,00)
            $arrData[$i]['precio_costo_format'] = SMONEY . ' ' . formatMoneda($arrData[$i]['precio_costo']);
            
            /* NOTA: Si tu consulta SQL en el modelo ya trae el nombre del usuario que registró 
               la entrada (ej. pr.nombre_usuario), cámbialo aquí. 
               Por ahora, mantenemos el de la sesión para no romper tu vista.
            */
            $arrData[$i]['usuario'] = $_SESSION['userData']['nombre'];
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function getResumenWidgets()
    {
        if ($_SESSION['permisos'][4]['r']) {
            $arrResumen = $this->model->selectResumenTotales();
            $arrResumen['total_inversion_format'] = SMONEY . ' ' . formatMoneda($arrResumen['total_inversion']);
            echo json_encode($arrResumen, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getSelectProveedores()
    {
        $htmlOptions = "<option value=''>Seleccione un proveedor</option>";
        $arrData = $this->model->selectProveedores();
        if (count($arrData) > 0) {
            foreach ($arrData as $proveedor) {
                $htmlOptions .= '<option value="' . $proveedor['idproveedor'] . '">' . $proveedor['nombre'] . '</option>';
            }
        }
        echo $htmlOptions;
        die();
    }
}
