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
            $strTipo = $_POST['listTipo'] ?? 'imagen'; 
            
            $strVideo = ""; 
            $request_slider = 0;

            $archivo = $_FILES['foto'] ?? null;
            $nombre_archivo = $archivo['name'] ?? '';
            $imgPortada = 'slider_default.png';

            // 1. PREPARACIÓN DE NOMBRE PARA BASE DE DATOS
            if ($strTipo == "imagen") {
                if (!empty($nombre_archivo)) {
                    // Generamos el prefijo. El Helper se encargará de ponerle el .jpg/.png
                    $imgPortada = 'banner_' . time() . '_' . rand(100, 999);
                } else {
                    $imgPortada = ($intIdslider != 0) ? $_POST['foto_actual'] : 'slider_default.png';
                }
            } else {
                // Si es video, mantenemos la imagen de portada actual
                $imgPortada = ($intIdslider != 0) ? $_POST['foto_actual'] : 'slider_default.png';
            }

            // 2. INSERTAR O ACTUALIZAR INICIAL
            if ($intIdslider == 0) {
                if (!empty($_SESSION['permiso_modulo']['w'])) {
                    $request_slider = $this->model->insertSlider($strNombre, $strDescripcion, $imgPortada, $strLink, $intStatus, $strTipo, $strVideo);
                    $option = 1;
                }
            } else {
                if (!empty($_SESSION['permiso_modulo']['u'])) {
                    $request_slider = $this->model->updateSlider($intIdslider, $strNombre, $strDescripcion, $imgPortada, $strLink, $intStatus, $strTipo, $strVideo);
                    $option = 2;
                }
            }

            // 3. PROCESAMIENTO DE ARCHIVOS FÍSICOS
            if (is_numeric($request_slider) && $request_slider > 0) {
                $idParaProcesar = ($option == 2) ? $intIdslider : $request_slider;
                $uploadOk = true;

                if (!empty($nombre_archivo)) {
                    $arrExtension = explode(".", $nombre_archivo);
                    $extension = strtolower(end($arrExtension));

                    // --- MANEJO DE VIDEO ---
                    if ($strTipo == "video") {
                        $extensionesVideo = array("mp4", "webm");
                        if (in_array($extension, $extensionesVideo)) {
                            $nombreFinalVideo = 'video_' . time() . '_' . rand(10, 99) . '.' . $extension;
                            $rutaDestino = 'Assets/images/uploads/' . $nombreFinalVideo;

                            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                                $this->model->updateVideoSlider($idParaProcesar, $nombreFinalVideo);
                                if ($option == 2 && !empty($_POST['video_actual'])) {
                                    deleteFile($_POST['video_actual']);
                                }
                            } else { $uploadOk = false; }
                        }
                    } 
                    // --- MANEJO DE IMAGEN ---
                    else {
                        // Llamamos al helper. El helper retorna el nombre con extensión (ej: banner_123.jpg)
                        $uploadResult = uploadImage($archivo, $imgPortada);
                        
                        if ($uploadResult) {
                            // Actualizamos la DB con el nombre REAL que incluye el punto y la extensión
                            $this->model->updateImageSlider($idParaProcesar, $uploadResult);
                            
                            if ($option == 2 && $_POST['foto_actual'] != 'slider_default.png') {
                                deleteFile($_POST['foto_actual']);
                            }
                        } else { 
                            $uploadOk = false; 
                        }
                    }
                }

                if ($uploadOk) {
                    $msg = ($option == 1) ? 'Slider guardado con éxito.' : 'Slider actualizado con éxito.';
                    $arrResponse = array('status' => true, 'msg' => $msg);
                } else {
                    $arrResponse = array('status' => false, 'msg' => 'Error al subir el archivo.');
                }
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

                if($arrData[$i]['status'] == 1) {
                    $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
                } else {
                    $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
                }

                $btnView = '<button class="btn btnPermisoRol btn-sm" onClick="fntViewInfo('.$arrData[$i]['idslider'].')"><i class="far fa-eye"></i></button>';

                $btnEdit = '';
                if($_SESSION['permiso_modulo']['u']){
                    $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditInfo(this,'.$arrData[$i]['idslider'].')"><i class="fas fa-pencil-alt"></i></button>';
                }

                $btnDelete = '';
                if($_SESSION['permiso_modulo']['d']){
                    $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelInfo('.$arrData[$i]['idslider'].')"><i class="far fa-trash-alt"></i></button>';
                }

                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // ============================
    // GET UNO
    // ============================
    public function getSlider(int $idslider)
    {
        if($_SESSION['permiso_modulo']['r']){
            $arrData = $this->model->selectSlider($idslider);

            if(empty($arrData)){
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            }else{
                $arrData['url_portada'] = media().'/images/uploads/'.$arrData['portada'];
                $arrResponse = array('status' => true, 'data' => $arrData);
            }

            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // ============================
    // DELETE
    // ============================
    public function delSlider()
    {
        if($_POST){
            if($_SESSION['permiso_modulo']['d']){
                $intIdslider = intval($_POST['idSlider']);
                $requestDelete = $this->model->deleteSlider($intIdslider);

                if($requestDelete == 'ok'){
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