<?php

class SliderModel extends Mysql
{
    private $intIdSlider;
    private $strNombre;
    private $strDescripcion;
    private $strPortada;
    private $strLink;
    private $intStatus;
    private $strTipo;
    private $strVideo;

    public function __construct()
    {
        parent::__construct();
    }

    // ================= INSERT =================
    public function insertSlider(string $nombre, string $descripcion, string $portada, string $link, int $status, string $tipo = "imagen", string $video = null)
    {

        $this->strNombre = $nombre;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strLink = $link;
        $this->intStatus = $status;
        $this->strTipo = $tipo;
        $this->strVideo = $video;

        $return = 0;

        $sql = "SELECT * FROM slider WHERE nombre = ?";
        $request = $this->select_all($sql, array($this->strNombre));

        if (empty($request)) {
            // 🔥 AGREGAMOS tipo y video (sin quitar nada)
            $query_insert  = "INSERT INTO slider(nombre, descripcion, portada, link, status, tipo, video) VALUES(?,?,?,?,?,?,?)";
            $arrData = array(
                $this->strNombre,
                $this->strDescripcion,
                $this->strPortada,
                $this->strLink,
                $this->intStatus,
                $this->strTipo,
                $this->strVideo
            );
            $request_insert = $this->insert($query_insert, $arrData);
            $return = $request_insert;
        } else {
            $return = "exist";
        }
        return $return;
    }

    // ================= SELECT =================
    public function selectSliders()
    {
        $sql = "SELECT * FROM slider WHERE status != 0";
        return $this->select_all($sql);
    }

    public function selectSlider(int $idslider)
    {
        $this->intIdSlider = $idslider;
        $sql = "SELECT * FROM slider WHERE idslider = $this->intIdSlider";
        return $this->select($sql);
    }

    // ================= UPDATE =================
    public function updateSlider(int $idslider, string $nombre, string $descripcion, string $portada, string $link, int $status, string $tipo = "imagen", string $video = null)
    {

        $this->intIdSlider = $idslider;
        $this->strNombre = $nombre;
        $this->strDescripcion = $descripcion;
        $this->strPortada = $portada;
        $this->strLink = $link;
        $this->intStatus = $status;
        $this->strTipo = $tipo;
        $this->strVideo = $video;

        $sql = "SELECT * FROM slider WHERE nombre = ? AND idslider != ?";
        $arrParams = array($this->strNombre, $this->intIdSlider);
        $request = $this->select_all($sql, $arrParams);

        if (empty($request)) {
            $sql = "UPDATE slider SET nombre = ?, descripcion = ?, portada = ?, link = ?, status = ?, tipo = ?, video = ? WHERE idslider = ?";
            $arrData = array(
                $this->strNombre,
                $this->strDescripcion,
                $this->strPortada,
                $this->strLink,
                $this->intStatus,
                $this->strTipo,
                $this->strVideo,
                $this->intIdSlider
            );
            $request = $this->update($sql, $arrData);

            return ($request || $request >= 0) ? 1 : 0;
        } else {
            return "exist";
        }
    }

    // ================= DELETE =================
    public function deleteSlider(int $idslider)
    {
        $this->intIdSlider = $idslider;
        $sql = "UPDATE slider SET status = ? WHERE idslider = ?";
        $arrData = array(0, $this->intIdSlider);
        $request = $this->update($sql, $arrData);
        return ($request) ? 'ok' : 'error';
    }

    // ================= UPDATE IMAGE =================
    public function updateImageSlider(int $idslider, string $portada)
    {
        $this->intIdSlider = $idslider;
        $this->strPortada = $portada;
        $sql = "UPDATE slider SET portada = ? WHERE idslider = ?";
        $arrData = array($this->strPortada, $this->intIdSlider);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function updateVideoSlider(int $id, string $video)
    {
        $sql = "UPDATE slider SET video = ? WHERE idslider = ?";
        $arrData = array($video, $id);
        return $this->update($sql, $arrData);
    }
}
