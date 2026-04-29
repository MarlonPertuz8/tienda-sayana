<?php 
class Categorias extends Controllers{
    public function __construct()
    {
        parent::__construct();
        session_start();
        session_regenerate_id(true);
        if(empty($_SESSION['login']))
        {
            header('Location: '.base_url().'/login');
            die();
        }
        // Asegúrate de que el ID del módulo sea el correcto en tu BD
        getPermisos(6);
    }

    public function index()
    {
        if(empty($_SESSION['permiso_modulo']['r'])){
            header("Location:".base_url().'/dashboard');
            die();
        }
        
        $data['page_tag'] = "Categorías";
        $data['page_title'] = "Categorías";
        $data['page_name'] = "categorias";
        $data['page_functions_js'] = "functions_categorias.js";
        
        $this->views->getView($this, "categorias", $data);
    }

public function setCategoria(){
    if ($_POST) {
        if (empty($_POST['txtNombre']) || empty($_POST['txtDescripcion']) || empty($_POST['listStatus'])) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos o incompletos.');
        } else {
            $intIdcategoria = intval($_POST['idCategoria']);
            $strCategoria = strClean($_POST['txtNombre']);
            $strDescripcion = strClean($_POST['txtDescripcion']);
            $intStatus = intval($_POST['listStatus']);
            $request_categoria = 0;

            $strRuta = strtolower(str_replace(" ", "-", $strCategoria));
            $strRuta = preg_replace('/[^A-Za-z0-9\-]/', '', $strRuta);

            $foto = $_FILES['foto'] ?? null;
            $nombre_foto = $foto['name'] ?? '';
            $imgPortada = 'default.png';

            if (!empty($nombre_foto)) {
                if ($foto['error'] > 0) {
                    $mensaje_error = "Error de servidor (Código: ".$foto['error']."). Verifica php.ini.";
                    echo json_encode(array("status" => false, "msg" => $mensaje_error), JSON_UNESCAPED_UNICODE);
                    die();
                }
                // Definimos el nombre base sin extensión, uploadImage se encargará del resto
                $imgPortada = 'img_' . time() . '_' . rand(100, 999);
            }

            if ($intIdcategoria == 0) {
                if (!empty($_SESSION['permiso_modulo']['w'])) {
                    $request_categoria = $this->model->insertCategoria($strCategoria, $strDescripcion, $imgPortada, $strRuta, $intStatus);
                    $option = 1;
                }
            } else {
                if (!empty($_SESSION['permiso_modulo']['u'])) {
                    if (empty($nombre_foto)) {
                        if ($_POST['foto_actual'] != 'default.png' && $_POST['foto_remove'] == 0) {
                            $imgPortada = $_POST['foto_actual'];
                        }
                    }
                    $request_categoria = $this->model->updateCategoria($intIdcategoria, $strCategoria, $strDescripcion, $imgPortada, $strRuta, $intStatus);
                    $option = 2;
                }
            }

            if (is_numeric($request_categoria) && $request_categoria > 0) {
                
                $idParaImagen = ($option == 2) ? $intIdcategoria : $request_categoria;
                $uploadOk = true;

                if (!empty($nombre_foto)) {
                    // La función uploadImage ahora retornará el nombre final con la extensión correcta (ej: .avif)
                    $uploadResult = uploadImage($foto, $imgPortada);
                    
                    if ($uploadResult) {
                        $imgPortada = $uploadResult; 
                        // Actualizamos la base de datos con el nombre real del archivo movido
                        $this->model->updateImageCategoria($idParaImagen, $imgPortada);
                    } else {
                        $uploadOk = false;
                    }
                }

                if ($uploadOk) {
                    $msg = ($option == 1) ? 'Categoría guardada con éxito.' : 'Categoría actualizada con éxito.';
                    $arrResponse = array('status' => true, 'msg' => $msg);
                    
                    if ($option == 2 && !empty($nombre_foto) && $_POST['foto_actual'] != 'default.png') {
                        deleteFile($_POST['foto_actual']);
                    }
                } else {
                    // Personalizamos el error para que sepas qué revisar
                    $arrResponse = array('status' => false, 'msg' => 'El texto se guardó, pero hubo un error al mover la imagen (revisa permisos o formato AVIF).');
                }

            } else if ($request_categoria == 'exist') {
                $arrResponse = array('status' => false, 'msg' => '¡Atención! Ya existe una categoría con ese nombre.');
            } else {
                $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function getCategorias()
    {
        if($_SESSION['permiso_modulo']['r']){
            $arrData = $this->model->selectCategorias();
            for ($i=0; $i < count($arrData); $i++) {
                $btnView = ''; $btnEdit = ''; $btnDelete = '';

                if($arrData[$i]['status'] == 1) {
                    $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
                } else {
                    $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
                }

                $btnView = '<button class="btn btn-info btn-sm btnPermisoRol" onClick="fntViewInfo('.$arrData[$i]['idcategoria'].')" title="Ver"><i class="far fa-eye"></i></button>';
                
                if($_SESSION['permiso_modulo']['u']){
                    $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idcategoria'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                }
                if($_SESSION['permiso_modulo']['d']){  
                    $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idcategoria'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                }
                
                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
    public function getSelectCategorias() {
    // 1. Consultamos los datos al modelo
    $arrData = $this->model->selectCategorias();
    $htmlOptions = "";

    // 2. Construimos solo las opciones HTML
    if(count($arrData) > 0){
        for ($i=0; $i < count($arrData); $i++) { 
            if($arrData[$i]['status'] == 1){
                $htmlOptions .= '<option value="'.$arrData[$i]['idcategoria'].'">'.$arrData[$i]['nombre'].'</option>';
            }
        }
    }

    // 3. Limpiamos y enviamos la respuesta pura
    ob_clean(); // Evita basura en la respuesta
    echo $htmlOptions; 
    exit; 
}

    public function getCategoria(int $idcategoria)
    {
        if($_SESSION['permiso_modulo']['r']){
            $intIdcategoria = intval($idcategoria);
            if($intIdcategoria > 0)
            {
                $arrData = $this->model->selectCategoria($intIdcategoria);
                if(empty($arrData))
                {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                }else{
                    $arrData['url_portada'] = media().'/images/uploads/'.$arrData['portada'];
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function delCategoria()
    {
        if($_POST){
            if($_SESSION['permiso_modulo']['d']){
                $intIdcategoria = intval($_POST['idCategoria']);
                $requestDelete = $this->model->deleteCategoria($intIdcategoria);
                if($requestDelete == 'ok')
                {
                    $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la categoría');
                }else if($requestDelete == 'exist'){
                    $arrResponse = array('status' => false, 'msg' => 'No es posible eliminar una categoría con productos asociados.');
                }else{
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar la categoría.');
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
}