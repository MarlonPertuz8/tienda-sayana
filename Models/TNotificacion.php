<?php
trait TNotificacion
{
    private $con_t;

    /**
     * Obtiene las últimas 5 notificaciones específicas de UN usuario
     */
    public function getNotificacionesT(int $idpersona)
    {
        $this->con_t = new Mysql();
        // Filtramos estrictamente por usuarioid para evitar que se mezclen pedidos ajenos
        $sql = "SELECT idnotificacion, titulo, mensaje as descripcion, 
                       DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') as fecha, leido as status 
                FROM notificacion 
                WHERE usuarioid = $idpersona 
                ORDER BY idnotificacion DESC LIMIT 5";
        
        $request = $this->con_t->select_all($sql);
        return $request;
    }

    /**
     * Cuenta SOLO las notificaciones no leídas del usuario actual
     */
    public function getCantNotificacionesT(int $idpersona)
    {
        $this->con_t = new Mysql();
        // Si el resultado es 61 es porque hay 61 filas con este usuarioid en la tabla notificacion.
        // Asegúrate de que no estás contando la tabla 'pedido' por error.
        $sql = "SELECT COUNT(*) as total FROM notificacion 
                WHERE usuarioid = $idpersona AND leido = 0";
        
        $request = $this->con_t->select($sql);
        return $request['total'] ?? 0;
    }

    /**
     * Marca como leídas las notificaciones de un usuario específico
     */
    public function updateNotificacionesStatus(int $idpersona)
    {
        $this->con_t = new Mysql();

        // Actualizamos a leido = 1 solo para el usuario logueado y solo las que estaban en 0
        $sql = "UPDATE notificacion SET leido = ? 
                WHERE usuarioid = ? AND leido = ?";

        $arrData = array(1, $idpersona, 0);
        $request = $this->con_t->update($sql, $arrData);
        
        return $request;
    }
}