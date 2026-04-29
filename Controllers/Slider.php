<?php 
class Slider extends Controllers{
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
        // Asegúrate de asignar el ID del módulo correspondiente en tu tabla de módulos
        getPermisos(7); 
    }

    public function index()
    {
        if(empty($_SESSION['permiso_modulo']['r'])){
            header("Location:".base_url().'/dashboard');
            die();
        }
        
        $data['page_tag'] = "Slider";
        $data['page_title'] = "Slider - Sayana";
        $data['page_name'] = "slider";
        $data['page_functions_js'] = "functions_slider.js";
        
        $this->views->getView($this, "slider", $data);
    }

  public function setSlider()
{
    if ($_POST) {
        if (empty($_POST['txtNombre']) || empty($_POST['txtLink']) || empty($_POST['listStatus'])) {
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos o incompletos.');
        } else {
            $intIdslider = intval($_POST['idSlider']);
            $strNombre = strClean($_POST['txtNombre']);
            $strDescripcion = strClean($_POST['txtDescripcion']);
            $strLink = strClean($_POST['txtLink']);
            $intStatus = intval($_POST['listStatus']);
            $request_slider = 0;

            $foto = $_FILES['foto'] ?? null;
            $nombre_foto = $foto['name'] ?? '';
            $imgPortada = 'slider_default.png';

            // 1. Generar nombre de imagen con extensión si se subió una
            if (!empty($nombre_foto)) {
                if ($foto['error'] > 0) {
                    $mensaje_error = "Error de servidor (Código: ".$foto['error'].").";
                    echo json_encode(array("status" => false, "msg" => $mensaje_error), JSON_UNESCAPED_UNICODE);
                    die();
                }
                $ext = pathinfo($nombre_foto, PATHINFO_EXTENSION);
                // No concatenamos la extensión aquí porque tu función uploadImage ya lo hace
                $imgPortada = 'banner_' . time() . '_' . rand(100, 999);
            }

            if ($intIdslider == 0) {
                // Nuevo Slider
                if (!empty($_SESSION['permiso_modulo']['w'])) {
                    $request_slider = $this->model->insertSlider($strNombre, $strDescripcion, $imgPortada, $strLink, $intStatus);
                    $option = 1;
                }
            } else {
                // Actualizar Slider
                if (!empty($_SESSION['permiso_modulo']['u'])) {
                    if (empty($nombre_foto)) {
                        if ($_POST['foto_actual'] != 'slider_default.png' && $_POST['foto_remove'] == 0) {
                            $imgPortada = $_POST['foto_actual'];
                        }
                    }
                    $request_slider = $this->model->updateSlider($intIdslider, $strNombre, $strDescripcion, $imgPortada, $strLink, $intStatus);
                    $option = 2;
                }
            }

            if (is_numeric($request_slider) && $request_slider > 0) {
                
                $idParaImagen = ($option == 2) ? $intIdslider : $request_slider;
                $uploadOk = true;

                if (!empty($nombre_foto)) {
                    // 2. Intentar subir la imagen
                    $uploadResult = uploadImage($foto, $imgPortada);
                    
                    if ($uploadResult) {
                        // Si se subió con éxito, actualizamos el nombre real (con extensión) en la BD
                        $this->model->updateImageSlider($idParaImagen, $uploadResult);
                        
                        // Borrar foto vieja si es actualización
                        if ($option == 2 && $_POST['foto_actual'] != 'slider_default.png') {
                            deleteFile($_POST['foto_actual']);
                        }
                    } else {
                        // IMPORTANTE: Si la imagen falla, pero el registro se creó (tu caso actual)
                        // Podrías elegir borrar el registro recién creado para no dejar basura,
                        // o simplemente notificar que el texto se guardó pero la imagen no.
                        $uploadOk = false;
                    }
                }

                if ($uploadOk) {
                    $msg = ($option == 1) ? 'Slider guardado con éxito.' : 'Slider actualizado con éxito.';
                    $arrResponse = array('status' => true, 'msg' => $msg);
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'El registro se guardó, pero hubo un error al mover la imagen. Revisa los permisos de la carpeta uploads.');
                }

            } else if ($request_slider == 'exist') {
                $arrResponse = array('status' => false, 'msg' => '¡Atención! Ya existe un slider con ese nombre.');
            } else {
                $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function getSliders()
    {
        if($_SESSION['permiso_modulo']['r']){
            $arrData = $this->model->selectSliders();
            for ($i=0; $i < count($arrData); $i++) {
                $btnView = ''; $btnEdit = ''; $btnDelete = '';

                if($arrData[$i]['status'] == 1) {
                    $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
                } else {
                    $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
                }

                $btnView = '<button class="btn btn-info btn-sm btnPermisoRol" onClick="fntViewInfo('.$arrData[$i]['idslider'].')" title="Ver"><i class="far fa-eye"></i></button>';
                
                if($_SESSION['permiso_modulo']['u']){
                    $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idslider'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                }
                if($_SESSION['permiso_modulo']['d']){  
                    $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idslider'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                }
                
                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getSlider(int $idslider)
    {
        if($_SESSION['permiso_modulo']['r']){
            $intIdslider = intval($idslider);
            if($intIdslider > 0)
            {
                $arrData = $this->model->selectSlider($intIdslider);
                if(empty($arrData))
                {
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                }else{
                    // Especificamos la subcarpeta banners
                    $arrData['url_portada'] = media().'/images/uploads/d'.$arrData['portada'];
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function delSlider()
    {
        if($_POST){
            if($_SESSION['permiso_modulo']['d']){
                $intIdslider = intval($_POST['idSlider']);
                $requestDelete = $this->model->deleteSlider($intIdslider);
                if($requestDelete == 'ok')
                {
                    $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el slider');
                }else{
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el slider.');
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
}