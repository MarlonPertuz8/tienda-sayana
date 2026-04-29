<?php

class LoginModel extends Mysql
{

	private $intIdUsuario;
	private $strUsuario;
	private $strPassword;
	private $strToken;
	private $strGoogleId;

	public function __construct()
	{
		parent::__construct();
	}

	public function loginUser($strUsuario, $strPassword)
	{
		$this->strUsuario = $strUsuario;
		$this->strPassword = $strPassword;
		$sql = "SELECT idpersona,status FROM persona WHERE
		email_user = '{$this->strUsuario}' AND
		password = '{$this->strPassword}' AND
		status != 0";
		$request = $this->select($sql);
		return $request;
	}

	public function sessionLogin(int $iduser)
	{

		$sql = "SELECT p.idpersona,
            p.identificacion,
            p.nombre,
            p.apellido,
            p.telefono,
            p.email_user,
            p.nit,
            p.nombrefiscal,
            p.direccionfiscal,
            r.idrol,
            r.nombrerol,
            p.status
            FROM persona p
            INNER JOIN rol r ON p.rolid = r.idrol
            WHERE p.idpersona = ?";

		return $this->select($sql, [$iduser]);
	}

	public function getUserEmail(string $email)
	{
		$this->strUsuario = $email;
		$sql = "SELECT idpersona, nombre, apellido, status FROM persona WHERE email_user = '{$this->strUsuario}'
		AND status != 0";
		$request = $this->select($sql);
		return $request;
	}

	public function setTokenUser(int $idpersona, string $token)
	{
		$this->intIdUsuario = $idpersona;
		$this->strToken = $token;
		$sql = "UPDATE persona SET token = ? WHERE idpersona = $this->intIdUsuario";
		$arrData = array($this->strToken);
		$request = $this->update($sql, $arrData);
		return $request;
	}

	public function getUsuario(string $email, string $token)
	{
		$this->strUsuario = $email;
		$this->strToken = $token;
		$sql = "SELECT idpersona FROM persona WHERE 
					email_user = '$this->strUsuario' and 
					token = '$this->strToken' and 					
					status = 1 ";
		$request = $this->select($sql);
		return $request;
	}
	public function insertPassword(int $idPersona, string $password)
	{
		$this->intIdUsuario = $idPersona;
		$this->strPassword = $password;
		$sql = "UPDATE persona SET password = ?, token = ? WHERE idpersona = $this->intIdUsuario ";
		$arrData = array($this->strPassword, "");
		$request = $this->update($sql, $arrData);
		return $request;
	}

	// public function loginGoogle(array $datos)
	// {
	// 	$this->strUsuario = $datos['email'];
	// 	$this->strGoogleId = $datos['google_id'];

	// 	// 1. Buscamos si el usuario ya existe por email o google_id
	// 	$sql = "SELECT idpersona, rolid, status FROM persona WHERE 
    //         email_user = '$this->strUsuario' OR google_id = '$this->strGoogleId'";
	// 	$request = $this->select($sql);

	// 	if (empty($request)) {
	// 		// 2. Si no existe, lo registramos (Rol 7 suele ser 'Cliente')
	// 		$query_insert = "INSERT INTO persona(nombre, email_user, google_id, rolid, status) 
    //                      VALUES(?,?,?,5,1)";
	// 		$arrData = array($datos['nombre'], $datos['email'], $datos['google_id']);
	// 		$request_insert = $this->insert($query_insert, $arrData);

	// 		$sql = "SELECT idpersona, rolid, status FROM persona WHERE idpersona = $request_insert";
	// 		$request = $this->select($sql);
	// 	}
	// 	return $request;
	// }
}
