<?php 
class ClientesModel extends Mysql
{
    private $intIdUsuario;
    private $strIdentificacion;
    private $strNombre;
    private $strApellido;
    private $intTelefono;
    private $strEmail;
    private $strPassword;
    private $strNit;
    private $strNombreFiscal;
    private $strDirFiscal;
    private $intStatus;
    private $intTipoId;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectClientes()
    {
        $sql = "SELECT idpersona, identificacion, nombre, apellido, telefono, email_user, status 
                FROM persona 
                WHERE rolid = 5 AND status != 0 ";
        return $this->select_all($sql);
    }

    public function insertCliente(string $identificacion, string $nombre, string $apellido, int $telefono, string $email, string $password, int $tipoid, string $nit, string $nomFiscal, string $dirFiscal){

    $this->strIdentificacion = $identificacion;
    $this->strNombre = $nombre;
    $this->strApellido = $apellido;
    $this->intTelefono = $telefono;
    $this->strEmail = $email;
    $this->strPassword = $password;
    $this->intTipoId = $tipoid;
    $this->strNit = $nit;
    $this->strNombreFiscal = $nomFiscal;
    $this->strDirFiscal = $dirFiscal;

    // BLINDAJE: Usamos parámetros en lugar de variables directas
    $sql = "SELECT * FROM persona WHERE email_user = ? OR identificacion = ?";
    $arrVerificar = array($this->strEmail, $this->strIdentificacion);
    $request = $this->select_all($sql, $arrVerificar); // Asumiendo que tu clase Mysql tiene este método o usa select_all con parámetros

    if(empty($request))
    {
        $query_insert = "INSERT INTO persona(identificacion, nombre, apellido, telefono, email_user, password, rolid, nit, nombrefiscal, direccionfiscal) 
                         VALUES(?,?,?,?,?,?,?,?,?,?)";
        $arrData = array($this->strIdentificacion, $this->strNombre, $this->strApellido, $this->intTelefono, $this->strEmail, $this->strPassword, $this->intTipoId, $this->strNit, $this->strNombreFiscal, $this->strDirFiscal);
        return $this->insert($query_insert,$arrData);
    }else{
        return "exist";
    }
}

    public function selectCliente(int $idpersona)
    {
        $this->intIdUsuario = $idpersona;
        $sql = "SELECT idpersona, identificacion, nombre, apellido, telefono, email_user, nit, nombrefiscal, direccionfiscal, status, DATE_FORMAT(datecreated, '%d-%m-%Y') as fechaRegistro 
                FROM persona 
                WHERE idpersona = $this->intIdUsuario AND rolid = 5";
        return $this->select($sql);
    }

    // CORRECCIÓN: Reordenamos los parámetros para que coincidan con el Controlador
   public function updateCliente(int $idUsuario, string $identificacion, string $nombre, string $apellido, int $telefono, string $email, int $status, string $password, string $nit, string $nomFiscal, string $dirFiscal){

    $this->intIdUsuario = $idUsuario;
    $this->strIdentificacion = $identificacion;
    $this->strNombre = $nombre;
    $this->strApellido = $apellido;
    $this->intTelefono = $telefono;
    $this->strEmail = $email;
    $this->intStatus = $status;
    $this->strPassword = $password;
    $this->strNit = $nit;
    $this->strNombreFiscal = $nomFiscal;
    $this->strDirFiscal = $dirFiscal;

    // BLINDAJE: Verificación segura con parámetros
    $sql = "SELECT * FROM persona WHERE (email_user = ? AND idpersona != ?) OR (identificacion = ? AND idpersona != ?)";
    $arrParams = array($this->strEmail, $this->intIdUsuario, $this->strIdentificacion, $this->intIdUsuario);
    $request = $this->select_all($sql, $arrParams);

    if(empty($request))
    {
        if($this->strPassword != ""){
            $sql = "UPDATE persona SET identificacion=?, nombre=?, apellido=?, telefono=?, email_user=?, status=?, password=?, nit=?, nombrefiscal=?, direccionfiscal=? WHERE idpersona = ?";
            $arrData = array($this->strIdentificacion, $this->strNombre, $this->strApellido, $this->intTelefono, $this->strEmail, $this->intStatus, $this->strPassword, $this->strNit, $this->strNombreFiscal, $this->strDirFiscal, $this->intIdUsuario);
        }else{
            $sql = "UPDATE persona SET identificacion=?, nombre=?, apellido=?, telefono=?, email_user=?, status=?, nit=?, nombrefiscal=?, direccionfiscal=? WHERE idpersona = ?";
            $arrData = array($this->strIdentificacion, $this->strNombre, $this->strApellido, $this->intTelefono, $this->strEmail, $this->intStatus, $this->strNit, $this->strNombreFiscal, $this->strDirFiscal, $this->intIdUsuario);
        }
        return $this->update($sql,$arrData);
    }else{
        return "exist";
    }
}

    public function deleteCliente(int $intIdpersona)
    {
        $this->intIdUsuario = $intIdpersona;
        $sql = "UPDATE persona SET status = ? WHERE idpersona = $this->intIdUsuario ";
        $arrData = array(0);
        return $this->update($sql,$arrData);
    }
}
?>