<?php
class Roles extends Controllers {

    public function __construct() {

        parent::__construct();
          session_Start();
        if(empty($_SESSION['login'])){
            header('Location: '.base_url().'/login');
        }
        getPermisos(2);
    }

    // MÉTODO POR DEFECTO
    public function index() {
         if(empty($_SESSION['permiso_modulo']['r'])){
            header("Location: ".base_url().'/dashboard');
        }
        $data['page_id'] = 3;
        $data['page_tag'] = "Roles usuario ";
        $data['page_name'] = "roles";
        $data['page_title'] = "Roles Usuario ";
        $data['page_functions_js'] = "functions_roles.js";
    
        $this->views->getView($this, "roles", $data);
    }

    public function getRoles() {
    // 1. VALIDACIÓN DE SESIÓN: ¿Tiene permiso de lectura para este módulo?
    if (empty($_SESSION['permiso_modulo']['r'])) {
        echo json_encode(array("status" => false, "msg" => "Acceso denegado"), JSON_UNESCAPED_UNICODE);
        die();
    }

    $arrData = $this->model->selectRoles();

    for ($i = 0; $i < count($arrData); $i++) {
        $btnView = '';
        $btnEdit = '';
        $btnDelete = '';

        // Formateo de Status
        if ($arrData[$i]['status'] == 1) {
            $arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
        } else {
            $arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
        }

        // 2. LÓGICA DE BOTONES SEGÚN PERMISOS Y JERARQUÍA
        
        // --- BOTONES DE EDICIÓN Y PERMISOS ('u') ---
        if ($_SESSION['permiso_modulo']['u']) {
            // Regla: Solo el Usuario ID 1 puede editar o ver permisos del Rol ID 1 (Administrador)
            if (($_SESSION['idUser'] == 1) || ($arrData[$i]['idrol'] != 1)) {
                $btnView = '<button class="btn btn-secondary btn-sm btnPermisoRol" rl="'. $arrData[$i]['idrol'] . '" title="Permisos"><i class="fas fa-key"></i></button>';
                $btnEdit = '<button class="btn btn-primary btn-sm btnEditRol" rl="' . $arrData[$i]['idrol'] . '" title="Editar Rol"><i class="fas fa-pencil-alt"></i></button>';
            } else {
                // Si no es el jefe, botones deshabilitados para el Rol 1
                $btnView = '<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-key"></i></button>';
                $btnEdit = '<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-pencil-alt"></i></button>';
            }
        }

        // --- BOTÓN DE ELIMINAR ('d') ---
        if ($_SESSION['permiso_modulo']['d']) {
            // Regla: NADIE (ni el ID 1) debería poder eliminar el Rol Administrador (ID 1)
            // porque el sistema colapsaría. También bloqueamos si no es el jefe.
            if ($arrData[$i]['idrol'] != 1 && ($_SESSION['idUser'] == 1 || $arrData[$i]['idrol'] != 1)) {
                $btnDelete = '<button class="btn btn-danger btn-sm btnDelRol" rl="' . $arrData[$i]['idrol'] . '" title="Eliminar Rol"><i class="fas fa-trash-alt"></i></button>';
            } else {
                $btnDelete = '<button class="btn btn-secondary btn-sm" disabled><i class="fas fa-trash-alt"></i></button>';
            }
        }

        $arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.' </div>';
    }

    echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
    die();
}

    public function getSelectRoles() {
    // 1. VALIDACIÓN DE SESIÓN
    // Solo usuarios logueados con permisos de crear (w) o editar (u) usuarios deberían ver esto
    if (empty($_SESSION['permiso_modulo']['w']) && empty($_SESSION['permiso_modulo']['u'])) {
        echo ""; // Devolvemos vacío si no tiene permisos
        die();
    }

    $htmlOptions = "";
    $arrData = $this->model->selectRoles();

    if (count($arrData) > 0) {
        for ($i = 0; $i < count($arrData); $i++) {
            if ($arrData[$i]['status'] == 1) {
                
                // 2. PROTECCIÓN DE JERARQUÍA
                // Si el usuario logueado NO es el ID 1, ocultamos el Rol de Administrador (ID 1)
                // para que no pueda crear otros administradores.
                if ($_SESSION['idUser'] != 1 && $arrData[$i]['idrol'] == 1) {
                    continue; // Salta esta iteración
                }

                $htmlOptions .= '<option value="' . $arrData[$i]['idrol'] . '">' . $arrData[$i]['nombrerol'] . '</option>';
            }
        }
    }
    
    echo $htmlOptions;
    die();
}



    public function getRol(int $idrol) {
    // 1. VALIDACIÓN DE SESIÓN Y PERMISO DE LECTURA ('r')
        if (empty($_SESSION['permiso_modulo']['r'])) {
            $arrResponse = array('status' => false, 'msg' => 'No tiene permisos para ver esta información.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }

        // 2. SANEAMIENTO (intval es suficiente para IDs, strClean sobra si es int)
        $intIdrol = intval($idrol);

        if ($intIdrol > 0) {
            // 3. PROTECCIÓN DE JERARQUÍA (Opcional pero recomendado)
            // Si no eres el Super Admin (ID 1), no deberías poder consultar el Rol 1 (Administrador) 
            // para evitar que alguien vea configuraciones sensibles.
            if ($_SESSION['idUser'] != 1 && $intIdrol == 1) {
                $arrResponse = array('status' => false, 'msg' => 'No tiene permisos para consultar este rol.');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            $arrData = $this->model->selectRol($intIdrol);

            if (empty($arrData)) {
                $arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
            } else {
                $arrResponse = array('status' => true, 'data' => $arrData);
            }
            
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function setRol() {
        if ($_POST) {
            // 1. VALIDACIÓN GENERAL DE PERMISOS
            if (empty($_SESSION['permiso_modulo']['w']) && empty($_SESSION['permiso_modulo']['u'])) {
                $arrResponse = array("status" => false, "msg" => 'No tiene permisos para realizar esta acción.');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            // 2. CAPTURA Y SANEAMIENTO
            $intIdrol = intval($_POST['idRol']);
            $strRol = strClean($_POST['txtNombre']);
            $strDescripcion = strClean($_POST['txtDescripcion']);
            $intStatus = intval($_POST['listStatus']);
            $request_rol = "";

            // 3. VALIDACIÓN DE CAMPOS VACÍOS
            if (empty($strRol) || empty($strDescripcion) || !isset($_POST['listStatus'])) {
                $arrResponse = array("status" => false, "msg" => 'Datos incorrectos o incompletos.');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            if ($intIdrol == 0) {
                // --- ACCIÓN: CREAR ---
                if (empty($_SESSION['permiso_modulo']['w'])) {
                    $arrResponse = array("status" => false, "msg" => 'No tiene permiso para crear roles.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }
                $option = 1;
                $request_rol = $this->model->insertRol($strRol, $strDescripcion, $intStatus);

            } else {
                // --- ACCIÓN: ACTUALIZAR ---
                if (empty($_SESSION['permiso_modulo']['u'])) {
                    $arrResponse = array("status" => false, "msg" => 'No tiene permiso para editar roles.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }

                // 🔒 PROTECCIÓN CRÍTICA: El Rol Administrador (ID 1) es intocable
                // Solo el Usuario ID 1 podría modificarlo (y aun así es arriesgado)
                if ($intIdrol == 1 && $_SESSION['idUser'] != 1) {
                    $arrResponse = array("status" => false, "msg" => 'El rol principal no puede ser modificado por este usuario.');
                    echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                    die();
                }

                $option = 2;
                $request_rol = $this->model->updateRol($intIdrol, $strRol, $strDescripcion, $intStatus);
            }

            // 4. RESPUESTAS SEGÚN EL RESULTADO DEL MODELO
            if ($request_rol > 0) {
                $msg = ($option == 1) ? 'Datos guardados correctamente.' : 'Datos actualizados correctamente.';
                $arrResponse = array('status' => true, 'msg' => $msg);
            } else if ($request_rol == 'exist') {
                $arrResponse = array('status' => false, 'msg' => '¡Atención! El rol ya existe.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'No es posible almacenar los datos.');
            }

            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
        
        public function delRol() {
        if ($_POST) {
            // 1. VALIDACIÓN DE PERMISOS (Permiso 'd' de Delete)
            if (empty($_SESSION['permiso_modulo']['d'])) {
                $arrResponse = array('status' => false, 'msg' => 'No tiene permisos para eliminar roles.');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            $intIdrol = intval($_POST['idrol']);

            // 2. PROTECCIÓN INVIOLABLE DEL ROL ADMINISTRADOR (ID 1)
            // Ningún usuario, ni siquiera el ID 1, debería poder borrar el rol principal 
            // ya que es la base de la estructura de permisos.
            if ($intIdrol == 1) {
                $arrResponse = array('status' => false, 'msg' => 'El Rol Principal no puede ser eliminado del sistema.');
                echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
                die();
            }

            // 3. EJECUCIÓN DEL PROCESO EN EL MODELO
            $requestDelete = $this->model->deleteRol($intIdrol);

            if ($requestDelete == 'ok') {
                $arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el rol correctamente.');
            } else if ($requestDelete == 'exist') {
                // Esta validación es excelente: evita errores de integridad referencial
                $arrResponse = array('status' => false, 'msg' => 'No es posible eliminar un rol asociado a usuarios activos.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Error al intentar eliminar el rol.');
            }

            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
