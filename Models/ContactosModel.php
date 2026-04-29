<?php
class ContactosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Seleccionar todos los contactos incluyendo el campo status
    public function selectContactos()
    {
        // Importante: La tabla debe tener la columna 'status'
        $sql = "SELECT id, nombre, email, DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') as fecha, mensaje, status 
                FROM contacto WHERE status != 0";
        $request = $this->select_all($sql);
        return $request;
    }

    // Seleccionar un contacto por ID
    public function selectContacto(int $id)
    {
        $sql = "SELECT id, nombre, email, DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') as fecha, mensaje, status 
                FROM contacto WHERE id = $id";
        $request = $this->select($sql);
        return $request;
    }

    // Actualizar estado a 2 (Atendido) cuando se envía la respuesta
    public function updateContactoStatus(int $id)
    {
        // Actualizamos a status 2 (Atendido)
        $sql = "UPDATE contacto SET status = ? WHERE id = ?";
        $arrData = array(2, $id);
        $request = $this->update($sql, $arrData);
        return $request;
    }
    // Obtener el total de contactos para el monitoreo en tiempo real
    public function selectTotalContactos()
    {
        // Contamos todos los que no han sido eliminados (status != 0)
        $sql = "SELECT COUNT(*) as total FROM contacto WHERE status != 0";
        $request = $this->select($sql);
        return $request['total'];
    }
}
