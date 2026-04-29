<?php
class AdminNosotros extends Controllers {
    public function __construct() {
        parent::__construct();
        session_start();
        if(empty($_SESSION['login'])) { header('Location: '.base_url().'/login'); die(); }
    }

    public function index() {
        $data['page_tag'] = "Nosotros - Sayana";
        $data['page_title'] = " Nosotros - Sayana Tienda Virtual";
        $data['page_name'] = "admin_nosotros";
        $data['page_functions_js'] = "functions_admin_nosotros.js?v=" . time(); 
        $data['info'] = $this->model->selectNosotros(); 
        $this->views->getView($this, "admin_nosotros", $data);
    }

    public function getNosotros() {
        $data = $this->model->selectNosotros();
        if(empty($data)) {
            $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
        } else {
            $arrResponse = array('status' => true, 'data' => $data);
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

   public function setNosotros() {
    if($_POST) {
        $id = intval($_POST['idNosotros']);
        $titulo = strClean($_POST['txtTituloHistoria']);
        $contenido = $_POST['txtContenidoHistoria']; // Se guarda el HTML completo aquí

        // Imagen 1: Portada
        $fotoPortada = $_FILES['foto_historia'];
        $nombrePortada = ($fotoPortada['name'] != "") ? 'portada_'.md5(date('d-m-Y H:i:s')).'.jpg' : $_POST['foto_actual_historia'];

        // Imagen 2: Secundaria
        $fotoSecundaria = $_FILES['foto_mision'];
        $nombreSecundaria = ($fotoSecundaria['name'] != "") ? 'secundaria_'.md5(date('d-m-Y H:i:s')).'.jpg' : $_POST['foto_actual_mision'];

        // IMPORTANTE: Tu modelo debe recibir estos nombres exactos
        $request = $this->model->updateNosotros($id, $titulo, $contenido, $nombrePortada, $nombreSecundaria);

        if($request) {
            if($fotoPortada['name'] != "") uploadImage($fotoPortada, $nombrePortada);
            if($fotoSecundaria['name'] != "") uploadImage($fotoSecundaria, $nombreSecundaria);
            $arrResponse = array('status' => true, 'msg' => 'Contenido de Sayana actualizado.');
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Error al actualizar.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}
}