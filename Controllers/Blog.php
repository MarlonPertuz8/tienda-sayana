<?php 

class Blog extends Controllers{
    public function __construct()
    {
        parent::__construct();
        sessionStart(); 
        if(empty($_SESSION['login']))
        {
            header('Location: '.base_url().'/login');
            die();
        }
        getPermisos(9); // Módulo de Blog
    }

    public function index()
    {
        if(empty($_SESSION['permiso_modulo']['r'])){
            header("Location:".base_url().'/dashboard');
            die();
        }
        $data['page_tag'] = "Blog - Sayana";
        $data['page_title'] = "Blog - Sayana Tienda Virtual";
        $data['page_name'] = "blog";
        $data['page_functions_js'] = "functions_blog.js";
        $this->views->getView($this,"blog",$data);
    }

    

    public function getPost($idpost){
        if($_SESSION['permiso_modulo']['r']){
            $idpost = intval($idpost); // Seguridad: Forzar entero
            if($idpost > 0){
                $arrData = $this->model->selectPost($idpost);
                if(empty($arrData)){
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

    public function setPost(){
        if($_POST){
            // Validación estricta de campos obligatorios
            if(empty($_POST['txtTitulo']) || empty($_POST['txtContenido']) || empty($_POST['listStatus']))
            {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos o incompletos.');
            }else{ 
                $idPost = intval($_POST['idPost']);
                $strTitulo = strClean($_POST['txtTitulo']);
                $strContenido = $_POST['txtContenido']; // Permitimos HTML de TinyMCE
                $intStatus = intval($_POST['listStatus']);
                
                // Crear URL amigable (Slug)
                $ruta = strtolower(clear_cadena($strTitulo));
                $ruta = str_replace(" ","-",$ruta);

                $foto          = $_FILES['foto'];
                $nombre_foto   = $foto['name'];
                $imgPortada    = ''; 
                $request_post = "";

                if($nombre_foto != ""){
                    $imgPortada = 'img_'.md5(date('d-m-Y H:i:s')).'.jpg';
                }

                if($idPost == 0)
                {
                    $option = 1;
                    if($_SESSION['permiso_modulo']['w']){
                        if($imgPortada == "") $imgPortada = "portada_categoria.png";
                        $request_post = $this->model->insertPost($strTitulo, $strContenido, $imgPortada, $ruta, $intStatus);
                    }
                }else{
                    $option = 2;
                    if($_SESSION['permiso_modulo']['u']){
                        if($nombre_foto == ""){
                            $imgPortada = ($_POST['foto_actual'] != "" && $_POST['foto_remove'] == 0) ? $_POST['foto_actual'] : "portada_categoria.png";
                        }
                        $request_post = $this->model->updatePost($idPost, $strTitulo, $strContenido, $imgPortada, $ruta, $intStatus);
                    }
                }

                if($request_post > 0 )
                {
                    if($option == 1){
                        $arrResponse = array('status' => true, 'msg' => 'Entrada de blog guardada.');
                        if($nombre_foto != ""){ uploadImage($foto,$imgPortada); }
                    }else{
                        $arrResponse = array('status' => true, 'msg' => 'Entrada de blog actualizada.');
                        if($nombre_foto != ""){ uploadImage($foto,$imgPortada); }
                        
                        // Borrar archivo físico anterior si se cambió o eliminó
                        if(($nombre_foto != "" && $_POST['foto_actual'] != "portada_categoria.png") || ($_POST['foto_remove'] == 1 && $_POST['foto_actual'] != "portada_categoria.png")){
                            deleteFile($_POST['foto_actual']);
                        }
                    }
                }else{
                    $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                }
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function getPosts()
    {
        if($_SESSION['permiso_modulo']['r']){
            $arrData = $this->model->selectPosts();
            for ($i=0; $i < count($arrData); $i++) {
                $btnView = ''; $btnEdit = ''; $btnDelete = '';

                $statusClass = ($arrData[$i]['status'] == 1) ? 'badge-success' : 'badge-danger';
                $statusText = ($arrData[$i]['status'] == 1) ? 'Activo' : 'Inactivo';
                $arrData[$i]['status'] = '<span class="badge '.$statusClass.'">'.$statusText.'</span>';

                // Botones de acción con validación de permisos
                if($_SESSION['permiso_modulo']['r']){
                    $btnView = '<button class="btn btn-info btnPermisoRol btn-sm" onClick="fntViewPost('.$arrData[$i]['idpost'].')" title="Ver"><i class="far fa-eye"></i></button>';
                }
                if($_SESSION['permiso_modulo']['u']){
                    $btnEdit = '<button class="btn btn-primary btn-sm" onClick="fntEditPost('.$arrData[$i]['idpost'].')" title="Editar"><i class="fas fa-pencil-alt"></i></button>';
                }
                if($_SESSION['permiso_modulo']['d']){
                    $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelPost('.$arrData[$i]['idpost'].')" title="Eliminar"><i class="far fa-trash-alt"></i></button>';
                }
                
                $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
            }
            echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function delPost(){
        if($_POST){
            if($_SESSION['permiso_modulo']['d']){
                $idPost = intval($_POST['idPost']); // Seguridad: Validar que sea número
                $requestDelete = $this->model->deletePost($idPost);
                if($requestDelete){
                    $arrResponse = array('status' => true, 'msg' => 'El artículo ha sido eliminado.');
                }else{
                    $arrResponse = array('status' => false, 'msg' => 'Error al eliminar el artículo.');
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }
}
