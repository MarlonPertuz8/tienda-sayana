<?php 
class Campanas extends Controllers{
    public function __construct()
    {
        parent::__construct();
        session_start();
        if(empty($_SESSION['login']))
        {
            header('Location: '.base_url().'/login');
            die();
        }
        getPermisos(12); 
    }

    public function index()
    {
       if (empty($_SESSION['permiso_modulo']['r'])) {
            header("Location:" . base_url() . '/dashboard');
            die();
        }
        $data['page_id'] = 12;
        $data['page_tag'] = "Campañas - Sayana";
        $data['page_title'] = "Gestión de Campañas";
        $data['page_name'] = "campanas";
        $data['page_functions_js'] = "functions_campanas.js";
        $this->views->getView($this,"campanas",$data);
    }

    public function getCampanas()
    {
        if($_SESSION['permiso_modulo']['r']){
            $arrData = $this->model->selectCampanas();
            for ($i=0; $i < count($arrData); $i++) {
                
                $foto = $arrData[$i]['banner_landing'];
                // Validamos la ruta de la imagen
                if(!empty($foto)){
                    $urlFoto = media().'/images/uploads/'.$foto;
                }else{
                    $urlFoto = media().'/images/uploads/default.png';
                }

                // HTML de la imagen con estilo profesional de Slider
                $arrData[$i]['multimedia'] = '<img src="'.$urlFoto.'" class="img-thumbnail shadow-sm" style="width: 70px; height: 40px; object-fit: cover; border-radius: 4px;">';

                // Formato de vigencia más limpio
                $arrData[$i]['vigencia'] = '<div><small><b>Desde:</b> '.$arrData[$i]['fecha_inicio'].'</small><br><small><b>Hasta:</b> '.$arrData[$i]['fecha_fin'].'</small></div>';

                $arrData[$i]['estado'] = ($arrData[$i]['estado'] == 1) 
                    ? '<span class="badge badge-success">Activo</span>' 
                    : '<span class="badge badge-danger">Inactivo</span>';

                $btnView = '<button class="btn btnPermisoRol btn-sm" onClick="fntViewCampana('.$arrData[$i]['id_campana'].')" title="Ver"><i class="far fa-eye"></i></button>';
                $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditCampana('.$arrData[$i]['id_campana'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelCampana('.$arrData[$i]['id_campana'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';

                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

public function setCampana()
{
    if($_POST){
        if (ob_get_length()) ob_clean(); 

        $idCampana = intval($_POST['idCampana']);
        $strNombre = strClean($_POST['txtNombre']);
        $strDesc   = !empty($_POST['txtDescripcionCorta']) ? strClean($_POST['txtDescripcionCorta']) : "";
        
        // RELEVANTE: Recibimos el HTML y el JSON tal cual vienen (Base64 puro)
        $strHtml   = isset($_POST['txtContenidoHtml']) ? $_POST['txtContenidoHtml'] : ""; 
        $strJson   = isset($_POST['txtJsonContenido']) ? $_POST['txtJsonContenido'] : "[]"; 
        
        $strFechaInicio = $_POST['txtFechaInicio'];
        $strFechaFin    = $_POST['txtFechaFin'];
        $strEnlaceBtn   = !empty($_POST['txtEnlaceBoton']) ? strClean($_POST['txtEnlaceBoton']) : "";
        $intStatus      = intval($_POST['listStatus']);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $strNombre)));

        // Foto del Banner Principal (esta sigue siendo archivo, como te gusta)
        $foto = $_FILES['foto'];
        $nombre_foto = "";
        if($foto['name'] != ""){
            $nombre_foto = 'camp_'.time().'.jpg';
        }

        if($idCampana == 0){
            $request = $this->model->insertCampana($strNombre, $slug, $nombre_foto, $strDesc, $strHtml, $strJson, $strFechaInicio, $strFechaFin, $strEnlaceBtn, $intStatus);
        }else{
            if($nombre_foto == ""){ $nombre_foto = $_POST['foto_actual']; }
            $request = $this->model->updateCampana($idCampana, $strNombre, $slug, $nombre_foto, $strDesc, $strHtml, $strJson, $strFechaInicio, $strFechaFin, $strEnlaceBtn, $intStatus);
        }

        if($request > 0){
            // Solo subimos el banner principal, los bloques ya van dentro del JSON
            if($nombre_foto != "" && $foto['name'] != ""){ uploadImage($foto, $nombre_foto); }
            $arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente con imágenes persistentes.');
        }else{
            $arrResponse = array("status" => false, "msg" => 'No se pudo guardar la información.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function getCampana($idcampana)
    {
        if($_SESSION['permiso_modulo']['r']){
            $idcampana = intval($idcampana);
            if($idcampana > 0){
                $arrData = $this->model->selectCampana($idcampana);
                if(empty($arrData)){
                    $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
                }else{
                    // Aseguramos la URL para el Modal
                    $arrData['url_banner'] = (!empty($arrData['banner_landing'])) 
                        ? media().'/images/uploads/'.$arrData['banner_landing'] 
                        : media().'/images/uploads/default.png';
                    
                    $arrResponse = array('status' => true, 'data' => $arrData);
                }
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    public function delCampana()
    {
        if($_POST){
            $intIdCampana = intval($_POST['idCampana']);
            $requestDelete = $this->model->deleteCampana($intIdCampana);
            if($requestDelete){
                $arrResponse = array('status' => true, 'msg' => 'Eliminado correctamente.');
            }else{
                $arrResponse = array('status' => false, 'msg' => 'Error al eliminar.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}