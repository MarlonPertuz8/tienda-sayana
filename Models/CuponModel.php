<?php
class CuponModel extends Mysql
{
    private $intIdCupon;
    private $strCodigo;
    private $intDescuento;
    private $intLimite;
    private $strFechaVenc;
    private $intStatus;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectCupones()
    {
        $sql = "SELECT idcupon, codigo, descuento, status FROM cupon WHERE status != 0";
        return $this->select_all($sql);
    }

    public function selectCupon(int $idcupon)
    {
        $this->intIdCupon = $idcupon;
        $sql = "SELECT * FROM cupon WHERE idcupon = $this->intIdCupon";
        return $this->select($sql);
    }

    public function insertCupon(string $codigo, int $descuento, int $limite, string $fechaVenc, int $status)
    {
        $this->strCodigo = $codigo;
        $this->intDescuento = $descuento;
        $this->intLimite = $limite;
        $this->strFechaVenc = $fechaVenc;
        $this->intStatus = $status;

        $sql = "SELECT * FROM cupon WHERE codigo = '{$this->strCodigo}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO cupon(codigo, descuento, limite_uso, fecha_vencimiento, status) VALUES(?,?,?,?,?)";
            $arrData = array($this->strCodigo, $this->intDescuento, $this->intLimite, $this->strFechaVenc, $this->intStatus);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    public function updateCupon(int $idcupon, string $codigo, int $descuento, int $limite, string $fechaVenc, int $status)
    {
        $this->intIdCupon = $idcupon;
        $this->strCodigo = $codigo;
        $this->intDescuento = $descuento;
        $this->intLimite = $limite;
        $this->strFechaVenc = $fechaVenc;
        $this->intStatus = $status;

        $sql = "SELECT * FROM cupon WHERE codigo = '$this->strCodigo' AND idcupon != $this->intIdCupon";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE cupon SET codigo=?, descuento=?, limite_uso=?, fecha_vencimiento=?, status=? WHERE idcupon = $this->intIdCupon";
            $arrData = array($this->strCodigo, $this->intDescuento, $this->intLimite, $this->strFechaVenc, $this->intStatus);
            return $this->update($sql, $arrData);
        } else {
            return "exist";
        }
    }

    public function deleteCupon(int $idcupon)
    {
        $this->intIdCupon = $idcupon;
        $sql = "UPDATE cupon SET status = ? WHERE idcupon = $this->intIdCupon";
        $arrData = array(0);
        return $this->update($sql, $arrData);
    }

    // ESTA ES LA FUNCIÓN QUE TE FALTA:
    public function consultarCupon(string $codigo)
    {
        $sql = "SELECT * FROM cupon 
                WHERE codigo = '$codigo' 
                AND status = 1 
                AND fecha_vencimiento >= CURDATE() 
                AND limite_uso > 0";
        $request = $this->select($sql);
        return $request;
    }
    // Verifica si un usuario específico ya usó el cupón
    public function verificarCuponUsuario(int $idUsuario, int $idCupon)
    {
        $sql = "SELECT * FROM cupon_usuario WHERE usuario_id = $idUsuario AND cupon_id = $idCupon";
        $request = $this->select($sql);
        return $request; // Si devuelve algo, es que ya lo usó
    }

    // Registra el uso del cupón (llamar esto al finalizar la orden)
    public function registrarUsoCupon(int $idUsuario, int $idCupon)
    {
        $query = "INSERT INTO cupon_usuario(usuario_id, cupon_id) VALUES(?,?)";
        $arrData = array($idUsuario, $idCupon);
        $request = $this->insert($query, $arrData);

        // De paso, restamos 1 al límite general de la tabla cupon
        $sql_update = "UPDATE cupon SET limite_uso = limite_uso - 1 WHERE idcupon = $idCupon";
        $this->update($sql_update, array());

        return $request;
    }
}
