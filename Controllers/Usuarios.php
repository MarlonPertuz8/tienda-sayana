<?php
class Usuarios extends Controllers {

    public function __construct() {

        parent::__construct();
        session_Start();
        if(empty($_SESSION['login'])){
            header('Location: '.base_url().'/login');
        }
    
        getPermisos(2);
        
    }

    public function index() {
        if(empty($_SESSION['permiso_modulo']['r'])){
            header("Location: ".base_url().'/dashboard');
        }
        $data['page_tag'] = "Usuarios";
        $data['page_title'] = "Usuarios";
        $data['page_name'] = "usuarios";
        $data['page_functions_js'] = "functions_usuarios.js";
        $this->views->getView($this, "usuarios", $data);
    }

    public function getSelectRoles(){
        $htmlOptions = "";
        $arrData = $this->model->selectRoles();

        if(count($arrData) > 0){
            for($i=0; $i < count($arrData); $i++){
                if($arrData[$i]['status'] == 1){
                    $htmlOptions .= '<option value="'.$arrData[$i]['idrol'].'">'.$arrData[$i]['nombrerol'].'</option>';
                }
            }
        }

        echo $htmlOptions;
        die();
    }

    public function getUsuarios()
    {
        $arrData = $this->model->selectUsuarios();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnView = '';
            $btnEdit = '';
            $btnDelete = '';

            if ($arrData[$i]['status'] == 1) {
                $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
            } else {
                $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
            }

            if($_SESSION['permiso_modulo']['r']) {
                $btnView ='<button class="btn btn-secondary btn-sm btnViewUsuario" rl="' . $arrData[$i]['idpersona'] . '" title="Ver Usuario"><i class="fas fa-eye"></i></button>';
            }
            if($_SESSION['permiso_modulo']['u']) {
               if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['idrol'] == 1) ||
							($_SESSION['userData']['idrol'] == 1 and $arrData[$i]['idrol'] != 1) ){
							$btnEdit = '<button class="btn btn-primary btn-sm btnEditUsuario" rl="'.$arrData[$i]['idpersona'].'" title="Editar usuario"><i class="fas fa-pencil-alt"></i></button>';
						}else{
							$btnEdit = '<button class="btn btn-secondary btn-sm" disabled ><i class="fas fa-pencil-alt"></i></button>';
						} 
            }
            if($_SESSION['permiso_modulo']['d']) {
                  if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['idrol'] == 1) ||
							($_SESSION['userData']['idrol'] == 1 and $arrData[$i]['idrol'] != 1) and 
                            ($_SESSION['userData']['idrol'] != $arrData[$i]['idpersona']) ){
                    $btnDelete ='<button class="btn btn-danger btn-sm btnDelUsuario" rl="' . $arrData[$i]['idpersona'] . '" title="Eliminar Usuario"><i class="fas fa-trash-alt"></i></button>';
                }else{
                    $btnDelete ='<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-trash-alt"></i></button>';
                }
            }

            $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.' </div>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getUsuario(int $idpersona) {
        $idUsuario = intval($idpersona);
        if($idUsuario > 0) {
            $arrData = $this->model->selectUsuario($idUsuario);
            if(empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
        }
        die();
    }

public function setUsuario() {
    if($_POST) {
        // 1. VALIDACIÓN GENERAL DE PERMISOS
        if(empty($_SESSION['permiso_modulo']['w']) && empty($_SESSION['permiso_modulo']['u'])){
            $arrResponse = array("status" => false, "msg" => 'No tiene permisos para realizar esta acción.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // 2. VALIDACIÓN DE CAMPOS OBLIGATORIOS
        if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || 
           empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['listRolid']) || 
           empty($_POST['listStatus'])) {
            
            $arrResponse = array("status" => false, "msg" => 'Datos incorrectos o incompletos.');
        } else {
            // 3. CAPTURA Y SANEAMIENTO DE DATOS
            $idUsuario = intval($_POST['idUsuario']);
            $strIdentificacion = strClean($_POST['txtIdentificacion']);
            $strNombre = ucwords(strClean($_POST['txtNombre']));
            $strApellido = ucwords(strClean($_POST['txtApellido']));
            $strTelefono = intval($_POST['txtTelefono']);
            $strEmail = strtolower(strClean($_POST['txtEmail']));
            $intRolid = intval($_POST['listRolid']);
            $intStatus = intval($_POST['listStatus']);
            $request_user = "";

            // 4. LÓGICA SEGÚN ACCIÓN (INSERTAR O ACTUALIZAR)
            if($idUsuario == 0) {
                // --- ACCIÓN: INSERTAR ---
                if(empty($_SESSION['permiso_modulo']['w'])){
                    $arrResponse = array("status" => false, "msg" => 'No tiene permiso para crear usuarios.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }
                
                $option = 1;
                $strPasswordClean = ""; // Variable para la clave real

                // Lógica de contraseña para el email
                if(empty($_POST['txtPassword'])){
                    $strPasswordClean = passGenerator(); // Generamos clave aleatoria
                    $strPassword = hash("SHA256", $strPasswordClean);
                } else {
                    $strPasswordClean = $_POST['txtPassword']; // Usamos la que puso el admin
                    $strPassword = hash("SHA256", $strPasswordClean);
                }

                $request_user = $this->model->insertUsuario(
                    $strIdentificacion, $strNombre, $strApellido, 
                    $strTelefono, $strEmail, $strPassword, $intRolid, $intStatus
                );

            } else {
                // --- ACCIÓN: ACTUALIZAR ---
                if(empty($_SESSION['permiso_modulo']['u'])){
                    $arrResponse = array("status" => false, "msg" => 'No tiene permiso para editar usuarios.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }

                // PROTECCIÓN DE JERARQUÍA
                if($_SESSION['idUser'] != 1 && ($idUsuario == 1 || $intRolid == 1)){
                    $arrResponse = array("status" => false, "msg" => 'No tienes rango suficiente para editar este usuario.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }

                $option = 2;
                $strPassword = empty($_POST['txtPassword']) 
                    ? "" 
                    : hash("SHA256", $_POST['txtPassword']);

                $request_user = $this->model->updateUsuario(
                    $idUsuario, $strIdentificacion, $strNombre, $strApellido, 
                    $strTelefono, $strEmail, $strPassword, $intRolid, $intStatus
                );
            }

            // 5. RESPUESTAS DEL MODELO Y ENVÍO DE EMAIL
            if ($request_user > 0) {
                if ($option == 1) {
                    $msg = "Datos guardados correctamente.";
                    
                    // --- ENVÍO DE EMAIL DE BIENVENIDA ---
                    $dataUsuario = array(
                        'nombreUsuario' => $strNombre.' '.$strApellido,
                        'email'         => $strEmail,
                        'password'      => $strPasswordClean, // Clave legible para el usuario
                        'asunto'        => 'Bienvenido a '.NOMBRE_EMPRESA
                    );
                    sendEmail($dataUsuario, 'email_bienvenida');

                } else {
                    $msg = "Datos actualizados correctamente.";
                    
                    // --- OPCIONAL: EMAIL DE CAMBIO DE CLAVE ---
                    if(!empty($_POST['txtPassword'])){
                        $dataUsuario = array(
                            'nombreUsuario' => $strNombre.' '.$strApellido,
                            'email'         => $strEmail,
                            'asunto'        => 'Seguridad: Tu contraseña ha sido cambiada - '.NOMBRE_EMPRESA
                        );
                        sendEmail($dataUsuario, 'email_cambioclave');
                    }
                }
                $arrResponse = array("status" => true, "msg" => $msg);

            } else if ($request_user === 'exist') {
                $arrResponse = array("status" => false, "msg" => '¡Atención! El email o la identificación ya existen.');
            } else {
                $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function delUsuario() {
    if($_POST) {
        // 1. VALIDACIÓN DE PERMISOS DE SESIÓN (Permiso 'd' de Delete)
        if(empty($_SESSION['permiso_modulo']['d'])){
            $arrResponse = array("status" => false, "msg" => 'No tiene permisos para eliminar usuarios.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        $intIdPersona = intval($_POST['idUsuario']);

        // 2. PROTECCIÓN CONTRA AUTO-ELIMINACIÓN
        // Evita que el usuario logueado se borre a sí mismo por error o malicia
        if($intIdPersona == $_SESSION['idUser']){
            $arrResponse = array("status" => false, "msg" => 'No puedes eliminar tu propia cuenta mientras estás en sesión.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // 3. PROTECCIÓN DE JERARQUÍA (EL "MURO" DEL SUPER ADMIN)
        // Regla: Solo el Usuario ID 1 (Dueño) puede borrar a otros Administradores.
        // Un Administrador común NO puede borrar al ID 1 ni a otros de su mismo rango.
        if($_SESSION['idUser'] != 1 && $intIdPersona == 1){
            $arrResponse = array("status" => false, "msg" => 'El Super Administrador no puede ser eliminado del sistema.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // 4. EJECUCIÓN DEL PROCESO
        $requestDelete = $this->model->deleteUsuario($intIdPersona);

        if($requestDelete == 'ok') {
            $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario correctamente.');
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Error al intentar eliminar el usuario (posiblemente tiene registros asociados).');
        }

        header('Content-Type: application/json');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}

    public function perfil() {
        $data['page_tag'] = "Perfil";
        $data['page_title'] = "Perfil de usuario";
        $data['page_name'] = "perfil";
        $data['page_functions_js'] = "functions_usuarios.js";
        $this->views->getView($this, "perfil", $data);
    }


    public function putPerfil(){
        if($_POST){
            if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']))
            {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
            }else{
                $idUsuario = $_SESSION['idUser'];
                $strIdentificacion = strClean($_POST['txtIdentificacion']);
                $strNombre = strClean($_POST['txtNombre']);
                $strApellido = strClean($_POST['txtApellido']);
                $intTelefono = intval(strClean($_POST['txtTelefono']));
                $strPassword = "";

                if(!empty($_POST['txtPassword'])){
                    // Nota: Asegúrate de que SHA256 es el mismo método usado en el Login
                    $strPassword = hash("SHA256", $_POST['txtPassword']);
                }

                $request_user = $this->model->updatePerfil(
                    $idUsuario,
                    $strIdentificacion, 
                    $strNombre,
                    $strApellido, 
                    $intTelefono, 
                    $strPassword
                );

                if($request_user)
                {
                    // Actualiza los datos de la sesión actual para reflejar los cambios de inmediato
                    sessionUser($_SESSION['idUser']);
                    $arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
                }else{
                    $arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die(); // Finaliza la ejecución para que no se envíe nada más accidentalmente
    }

    public function updateFiscal(){
        if($_POST){
            if(empty($_POST['txtNit']) || empty($_POST['txtNombreFiscal']) || empty($_POST['txtDirFiscal']))
            {
                $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
            }else{
                $idUsuario = $_SESSION['idUser'];
                $strNit = strClean($_POST['txtNit']);
                $strNomFiscal = strClean($_POST['txtNombreFiscal']);
                $strDirFiscal = strClean($_POST['txtDirFiscal']);

                $request_datafiscal = $this->model->updateDataFiscal($idUsuario,
                                                                    $strNit,
                                                                    $strNomFiscal, 
                                                                    $strDirFiscal);
                if($request_datafiscal)
                {
                    sessionUser($_SESSION['idUser']); 
                    $arrResponse = array('status' => true, 'msg' => 'Datos actualizados correctamente.');
                }else{
                    $arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }



}
