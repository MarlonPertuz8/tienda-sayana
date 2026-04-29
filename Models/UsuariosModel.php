<?php

class UsuariosModel extends Mysql
{
    private $intIdUsuario;
    private $strIdentificacion; 
    private $strNombre; 
    private $strApellido;
    private $intTelefono;
    private $strEmail;
    private $strPassword;
    private $strToken;
    private $intRolid;
    private $intStatus;
    private $strNit;
    private $strNomFiscal;
    private $strDirFiscal;

    public function __construct()
    {
        parent::__construct();
    }

    /* ===============================
       ROLES
    =============================== */
    public function selectRoles()
    {
        $sql = "SELECT * FROM rol WHERE status != 0";
        return $this->select_all($sql);
    }

    /* ===============================
       USUARIOS - LISTAR
    =============================== */
    public function selectUsuarios()
{
    $whereAdmin = "";
    if ($_SESSION['idUser'] != 1) {
        $whereAdmin = " AND p.idpersona != 1"; // ← espacio antes de AND
    }

    $sql = "SELECT 
                p.idpersona,
                p.identificacion,
                p.nombre,
                p.apellido,
                p.telefono,
                p.email_user,
                p.rolid AS idrol,
                p.status,
                r.idrol,
                r.nombrerol
            FROM persona p
            INNER JOIN rol r ON p.rolid = r.idrol
            WHERE p.status != 0".$whereAdmin;

    return $this->select_all($sql);
}
    public function selectUsuario(int $idpersona)
    {
        $this->intIdUsuario = $idpersona;

        $sql = "SELECT 
                    p.idpersona,
                    p.identificacion,
                    p.nombre,
                    p.apellido,
                    p.telefono,
                    p.email_user,
                    p.rolid,
                    r.nombrerol,
                    p.status,
                    DATE_FORMAT(p.datecreated, '%d-%m-%Y') AS fecharegistro  
                FROM persona p
                INNER JOIN rol r ON p.rolid = r.idrol
                WHERE p.idpersona = {$this->intIdUsuario}";

        return $this->select($sql);
    }

    /* ===============================
       INSERTAR USUARIO
    =============================== */
    public function insertUsuario(
        string $identificacion,
        string $nombre,
        string $apellido,
        int $telefono,
        string $email,
        string $password,
        int $rolid,
        int $status
    ) {
        $this->strIdentificacion = $identificacion;
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->intTelefono = $telefono;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intRolid = $rolid;
        $this->intStatus = $status;

        $sql = "SELECT * FROM persona 
                WHERE email_user = '{$this->strEmail}' 
                OR identificacion = '{$this->strIdentificacion}'";

        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO persona(
                identificacion,
                nombre,
                apellido,
                telefono,
                email_user,
                password,
                rolid,
                status
            ) VALUES(?,?,?,?,?,?,?,?)";

            $arrData = [
                $this->strIdentificacion,
                $this->strNombre,
                $this->strApellido,
                $this->intTelefono,
                $this->strEmail,
                $this->strPassword,
                $this->intRolid,
                $this->intStatus
            ];

            return $this->insert($query_insert, $arrData);
        }

        return "exist";
    }

    /* ===============================
       ACTUALIZAR USUARIO
    =============================== */
   public function updateUsuario(
    int $idUsuario,
    string $identificacion,
    string $nombre,
    string $apellido,
    int $telefono,
    string $email,
    string $password,
    int $tipoid,
    int $status
) {
    $this->intIdUsuario = $idUsuario;
    $this->strIdentificacion = $identificacion;
    $this->strNombre = $nombre;
    $this->strApellido = $apellido;
    $this->intTelefono = $telefono;
    $this->strEmail = $email;
    $this->strPassword = $password;
    $this->intRolid = $tipoid;
    $this->intStatus = $status;

    // 🔒 1. Validar que el EMAIL o la IDENTIFICACIÓN no pertenezcan a OTRO usuario
    $sql = "SELECT idpersona FROM persona 
            WHERE (LOWER(email_user) = LOWER(?) OR identificacion = ?) 
            AND idpersona != ?";
    
    $arrParams = [$this->strEmail, $this->strIdentificacion, $this->intIdUsuario];
    $request = $this->select_all($sql, $arrParams); // Asumiendo que usas prepares en select_all

    if (!empty($request)) {
        return "exist";
    }

    // 2. Determinar si se actualiza con o sin contraseña
    if ($this->strPassword != "") {
        // ACTUALIZAR CON CONTRASEÑA
        $sql = "UPDATE persona SET 
                    identificacion = ?,
                    nombre = ?,
                    apellido = ?,
                    telefono = ?,
                    email_user = ?,
                    password = ?,
                    rolid = ?,
                    status = ?
                WHERE idpersona = ?";

        $arrData = [
            $this->strIdentificacion,
            $this->strNombre,
            $this->strApellido,
            $this->intTelefono,
            $this->strEmail,
            $this->strPassword,
            $this->intRolid,
            $this->intStatus,
            $this->intIdUsuario
        ];

    } else {
        // ACTUALIZAR SIN CONTRASEÑA
        $sql = "UPDATE persona SET 
                    identificacion = ?,
                    nombre = ?,
                    apellido = ?,
                    telefono = ?,
                    email_user = ?,
                    rolid = ?,
                    status = ?
                WHERE idpersona = ?";

        $arrData = [
            $this->strIdentificacion,
            $this->strNombre,
            $this->strApellido,
            $this->intTelefono,
            $this->strEmail,
            $this->intRolid,
            $this->intStatus,
            $this->intIdUsuario
        ];
    }

    return $this->update($sql, $arrData);
}
    /* ===============================
       ELIMINAR USUARIO
    =============================== */
    public function deleteUsuario(int $intIdpersona) {
        $this->intIdUsuario = $intIdpersona;
        $sql = "UPDATE persona SET status = ? WHERE idpersona = {$this->intIdUsuario}";
        $arrData = [0];
        $request = $this->update($sql, $arrData);
        return $request ? 'ok' : 'error';
    }

   public function updatePerfil(int $idUsuario, string $identificacion, string $nombre, string $apellido, int $telefono, string $password){
        $this->intIdUsuario = $idUsuario;
        $this->strIdentificacion = $identificacion;
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->intTelefono = $telefono;
        $this->strPassword = $password;

        if($this->strPassword != "")
        {
            // CORRECCIÓN: nombre y apellido (sin la 's' final)
            $sql = "UPDATE persona SET identificacion=?, nombre=?, apellido=?, telefono=?, password=? 
                    WHERE idpersona = $this->intIdUsuario ";
            $arrData = array($this->strIdentificacion,
                            $this->strNombre,
                            $this->strApellido,
                            $this->intTelefono,
                            $this->strPassword);
        }else{
            // CORRECCIÓN: nombre y apellido (sin la 's' final)
            $sql = "UPDATE persona SET identificacion=?, nombre=?, apellido=?, telefono=? 
                    WHERE idpersona = $this->intIdUsuario ";
            $arrData = array($this->strIdentificacion,
                            $this->strNombre,
                            $this->strApellido,
                            $this->intTelefono);
        }
        $request = $this->update($sql,$arrData);
        return $request;
    }

    public function updateDataFiscal(int $idUsuario, string $strNit, string $strNomFiscal, string $strDirFiscal){
        $this->intIdUsuario = $idUsuario;
        $this->strNit = $strNit;
        $this->strNomFiscal = $strNomFiscal;
        $this->strDirFiscal = $strDirFiscal;

        $sql = "UPDATE persona SET nit=?, nombrefiscal=?, direccionfiscal=? WHERE idpersona = ?";
        $arrData = array($this->strNit, $this->strNomFiscal, $this->strDirFiscal, $this->intIdUsuario);
        $request = $this->update($sql, $arrData);
        return $request;
    }
}

?>