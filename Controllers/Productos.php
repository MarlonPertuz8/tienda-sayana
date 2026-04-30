<?php
class Productos extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        getPermisos(4);
    }

    public function index()
    {
        if (empty($_SESSION['permiso_modulo']['r'])) {
            header("Location:" . base_url() . '/dashboard');
        }
        $data['page_tag'] = "Productos";
        $data['page_title'] = "Gestión de Productos";
        $data['page_name'] = "productos";
        $data['page_functions_js'] = "functions_productos.js";
        $this->views->getView($this, "productos", $data);
    }

    public function getProductos()
    {
        if ($_SESSION['permiso_modulo']['r']) {
            $arrData = $this->model->selectProductos();
            if (empty($arrData)) {
                $arrData = array();
            } else {
                for ($i = 0; $i < count($arrData); $i++) {
                    $btnView = $btnEdit = $btnDelete = '';

                    $statusClass = ($arrData[$i]['status'] == 1) ? 'badge-success' : 'badge-danger';
                    $statusText = ($arrData[$i]['status'] == 1) ? 'Activo' : 'Inactivo';
                    $arrData[$i]['status'] = '<span class="badge ' . $statusClass . ' px-2 py-1">' . $statusText . '</span>';

                    $arrData[$i]['precio'] = SMONEY . ' ' . formatMoneda($arrData[$i]['precio']);

                    if ($_SESSION['permiso_modulo']['r']) {
                        $btnView = '<button class="btn btnPermisoRol btn-sm" onClick="fntViewInfo(' . $arrData[$i]['idproducto'] . ')" title="Ver detalle"><i class="far fa-eye"></i></button>';
                    }
                    if ($_SESSION['permiso_modulo']['u']) {
                        $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditInfo(this,' . $arrData[$i]['idproducto'] . ')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                    }
                    if ($_SESSION['permiso_modulo']['d']) {
                        $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo(' . $arrData[$i]['idproducto'] . ')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                    }

                    $arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
                }
            }
            ob_clean();
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function setProducto()
    {
        if ($_POST) {
            // 1. Validación (Añadido txtPrecioOferta como opcional o validado)
            if (empty($_POST['txtNombre']) || empty($_POST['txtCodigo']) || empty($_POST['listCategoria']) || empty($_POST['listMaterial']) || empty($_POST['txtPrecio'])) {
                $arrResponse = ["status" => false, "msg" => 'Datos incompletos. Los campos con (*) son obligatorios.'];
            } else {
                // 2. Captura y limpieza
                $idProducto = intval($_POST['idProducto']);
                $strNombre = strClean($_POST['txtNombre']);
                $strDescripcion = strClean($_POST['txtDescripcion']);
                $strCodigo = strClean($_POST['txtCodigo']);
                $intCategoriaId = intval($_POST['listCategoria']);
                $intMaterialId = intval($_POST['listMaterial']);
                $strPrecio = strClean($_POST['txtPrecio']);

                // CAPTURA DEL PRECIO DE OFERTA
                $strPrecioOferta = (!empty($_POST['txtPrecioOferta'])) ? strClean($_POST['txtPrecioOferta']) : "0";

                $intStock = intval($_POST['txtStock']);
                $intStatus = intval($_POST['listStatus']);
                $strColores = !empty($_POST['txtColores']) ? trim(strClean($_POST['txtColores']), ",") : "";
                $ruta = clear_cadena($strNombre);

                $request_producto = "";

                // IMPORTANTE: El orden de los argumentos debe coincidir con ProductosModel.php
                if ($idProducto == 0) {
                    $option = 1;
                    if ($_SESSION['permiso_modulo']['w']) {
                        $request_producto = $this->model->insertProducto(
                            $strNombre,
                            $strDescripcion,
                            $strCodigo,
                            $intCategoriaId,
                            $intMaterialId,
                            $strPrecio,
                            $strPrecioOferta, // <--- 7mo parámetro
                            $intStock,
                            $intStatus,
                            $ruta,
                            $strColores
                        );
                    }
                } else {
                    $option = 2;
                    if ($_SESSION['permiso_modulo']['u']) {
                        $request_producto = $this->model->updateProducto(
                            $idProducto,
                            $strNombre,
                            $strDescripcion,
                            $strCodigo,
                            $intCategoriaId,
                            $intMaterialId,
                            $strPrecio,
                            $strPrecioOferta, // <--- 8vo parámetro
                            $intStock,
                            $intStatus,
                            $ruta,
                            $strColores
                        );
                    }
                }

                if ($request_producto > 0) {
                    $msg = ($option == 1) ? 'Producto registrado con éxito.' : 'Producto actualizado con éxito.';
                    $arrResponse = [
                        'status' => true,
                        'idproducto' => ($idProducto == 0 ? $request_producto : $idProducto),
                        'msg' => $msg
                    ];
                } else if ($request_producto == 'exist') {
                    $arrResponse = ['status' => false, 'msg' => '¡Atención! El código ya existe.'];
                } else {
                    $arrResponse = ["status" => false, "msg" => 'No es posible almacenar los datos.'];
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

  public function getProducto(int $idproducto)
{
    if ($_SESSION['permiso_modulo']['r']) {
        $idproducto = intval($idproducto);
        if ($idproducto > 0) {
            $arrData = $this->model->selectProducto($idproducto);
            if (empty($arrData)) {
                $arrResponse = ['status' => false, 'msg' => 'Datos no encontrados.'];
            } else {
                $arrImg = $this->model->selectImages($idproducto);
                
                if (count($arrImg) > 0) {
                    for ($i = 0; $i < count($arrImg); $i++) {
                        $arrImg[$i]['url_image'] = media() . '/images/uploads/' . $arrImg[$i]['img'];
                    }
                } else {
                    // SI NO HAY IMÁGENES, AGREGAMOS LA POR DEFECTO
                    $arrImg[0]['url_image'] = media() . '/images/uploads/default.png';
                    $arrImg[0]['img'] = 'default.png';
                }
                
                $arrData['images'] = $arrImg;
                $arrResponse = ['status' => true, 'data' => $arrData];
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
    }
    die();
}

  public function setImage()
{
    if ($_POST) {
        if (empty($_POST['idproducto']) || empty($_FILES['foto'])) {
            $arrResponse = ['status' => false, 'msg' => 'Datos incompletos.'];
        } else {
            $idProducto = intval($_POST['idproducto']);
            $foto = $_FILES['foto'];

            if ($foto['error'] !== UPLOAD_ERR_OK) {
                // ... (aquí va tu switch de errores actual)
                $arrResponse = ['status' => false, 'msg' => 'Error en la carga.'];
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            // Generamos la extensión y el nombre completo UNA SOLA VEZ
            $extension = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
            $imgNombre = 'prod_' . $idProducto . '_' . bin2hex(random_bytes(5)) . '.' . $extension;

            // Intentamos subir
            $upload = uploadImage($foto, $imgNombre);

            if ($upload) {
                $request_image = $this->model->insertImage($idProducto, $imgNombre);

                if ($request_image > 0) {
                    $arrResponse = [
                        'status' => true,
                        'imgname' => $imgNombre,
                        'msg' => 'Imagen cargada correctamente.'
                    ];
                } else {
                    deleteFile($imgNombre);
                    $arrResponse = ['status' => false, 'msg' => 'Error al registrar en BD.'];
                }
            } else {
                $arrResponse = ['status' => false, 'msg' => 'Error al mover el archivo físico.'];
            }
        }
        // Aquí se envía la respuesta que armamos arriba
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function delFile()
    {
        if ($_POST) {
            if (empty($_POST['idproducto']) || empty($_POST['file'])) {
                $arrResponse = array('status' => false, 'msg' => 'Datos incorrectos.');
            } else {
                $idProducto = intval($_POST['idproducto']);
                $strPhoto   = strClean($_POST['file']);
                $requestDb  = $this->model->deleteImage($idProducto, $strPhoto);

                if ($requestDb) {
                    // RUTA FÍSICA CORREGIDA: Ya no busca la carpeta /productos/
                    $rutaFile = "Assets/images/uploads/" . $strPhoto;
                    if (file_exists($rutaFile)) {
                        unlink($rutaFile);
                    }
                    $arrResponse = array('status' => true, 'msg' => 'Archivo eliminado correctamente.');
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el registro de la base de datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function delProducto()
    {
        if ($_POST) {
            if ($_SESSION['permiso_modulo']['d']) {
                $intIdproducto = intval($_POST['idProducto']);
                $requestDelete = $this->model->deleteProducto($intIdproducto);
                if ($requestDelete) {
                    $arrResponse = ['status' => true, 'msg' => 'Producto eliminado.'];
                } else {
                    $arrResponse = ['status' => false, 'msg' => 'Error al eliminar el producto.'];
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function getStockCritico()
    {
        if ($_SESSION['permiso_modulo']['r']) {
            $arrData = $this->model->selectProductosStockCritico();
            if (empty($arrData)) {
                // Enviamos total 0 para que el JS sepa que debe ocultar el badge
                echo json_encode(['status' => true, 'total' => 0], JSON_UNESCAPED_UNICODE);
            } else {
                // Enviamos el conteo de cuántos productos están en stock crítico
                echo json_encode(['status' => true, 'total' => count($arrData), 'data' => $arrData], JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
    public function setImport()
    {
        if ($_FILES) {
            if (empty($_FILES['fileProductos'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de archivo.');
            } else {
                require 'vendor/autoload.php';

                $file = $_FILES['fileProductos']['tmp_name'];
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $data = $spreadsheet->getActiveSheet()->toArray();

                $count = 0;
                $errores = 0;

                // Empezamos en el índice 1 para saltar la cabecera del Excel
                for ($i = 1; $i < count($data); $i++) {
                    // Captura de datos según las columnas de tu Excel
                    $strCodigo      = strClean($data[$i][0]);
                    $strNombre      = strClean($data[$i][1]);

                    // 1. Convertimos texto normal a formato <p> automáticamente
                    $strDescripcion = "<p>" . strClean($data[$i][2]) . "</p>";

                    $strPrecio      = strClean($data[$i][3]);

                    // 2. Capturamos el precio de oferta del Excel
                    $strPrecioOferta = strClean($data[$i][4]);

                    $intStock       = intval($data[$i][5]);
                    $nombreCat      = strClean($data[$i][6]);
                    $nombreMat      = strClean($data[$i][7]);
                    $strColores     = strClean($data[$i][8]);

                    // --- Buscar IDs por Nombre ---
                    $intCategoriaId = $this->model->getIdByName("categoria", "nombre", $nombreCat);
                    $intMaterialId  = $this->model->getIdByName("material", "nombre", $nombreMat);

                    // Si existen ambos y el nombre no está vacío, insertamos
                    if ($intCategoriaId > 0 && $intMaterialId > 0 && !empty($strNombre)) {
                        $ruta = clear_cadena($strNombre);

                        $request = $this->model->insertProducto(
                            $strNombre,
                            $strDescripcion,
                            $strCodigo,
                            $intCategoriaId,
                            $intMaterialId,
                            $strPrecio,
                            $strPrecioOferta, // <--- Pasamos el precio de oferta real
                            $intStock,
                            1, // Status activo por defecto
                            $ruta,
                            $strColores
                        );
                        if ($request > 0) $count++;
                    } else {
                        $errores++;
                    }
                }

                if ($count > 0) {
                    $arrResponse = array('status' => true, 'msg' => "Se importaron $count productos correctamente. Errores detectados: $errores");
                } else {
                    $arrResponse = array('status' => false, 'msg' => "No se pudo importar. Verifique que los nombres de categorías/materiales existan en el sistema.");
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
