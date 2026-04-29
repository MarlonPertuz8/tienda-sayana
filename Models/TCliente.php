<?php 
require_once("Libraries/Core/Mysql.php");

trait TCliente {
    private $con;
    private $strNombre;
    private $strApellido;
    private $strTelefono; 
    private $strEmail;
    private $strPassword;
    private $intTipoId;
    private $intIdUsuario;

    // AÑADIMOS $telefono COMO TERCER PARÁMETRO
    public function insertCliente(string $nombre, string $apellido, string $telefono, string $email, string $password, int $tipoid){
        $this->con = new Mysql();
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strTelefono = $telefono; // ASIGNAMOS EL TELÉFONO
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intTipoId = $tipoid;

        $return = "";
        $sql = "SELECT * FROM persona WHERE email_user = '{$this->strEmail}' ";
        $request = $this->con->select($sql);

        if(empty($request)){
            // AGREGAMOS 'telefono' AL INSERT Y UN '?' EXTRA EN VALUES
            $query_insert  = "INSERT INTO persona(nombre, apellido, telefono, email_user, password, rolid, status) 
                              VALUES(?,?,?,?,?,?,?)";
            
            // AGREGAMOS EL TELÉFONO AL ARRAY DE DATOS EN EL ORDEN CORRECTO
            $arrData = array($this->strNombre, 
                             $this->strApellido, 
                             $this->strTelefono, 
                             $this->strEmail, 
                             $this->strPassword, 
                             $this->intTipoId, 
                             1);
                             
            $request_insert = $this->con->insert($query_insert,$arrData);
            $return = $request_insert;
        }else{
            $return = "exist";
        }
        return $return;
    }

   public function getPedidosT(int $idpersona){
    $this->con = new Mysql();
    $this->intIdUsuario = $idpersona;
    // Agregamos tipopagoid a la consulta
    $sql = "SELECT idpedido, 
                   DATE_FORMAT(fecha, '%d/%m/%Y') as fecha, 
                   monto, 
                   tipopagoid, 
                   status 
            FROM pedido 
            WHERE personaid = $this->intIdUsuario 
            ORDER BY idpedido DESC";
            
    $request = $this->con->select_all($sql);
    return $request;
}  
}
 ?>