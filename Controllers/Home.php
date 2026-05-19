<?php
require_once("Models/TCategoria.php");
require_once("Models/TProducto.php");
require_once("Models/TSlider.php");
require_once("Models/TBlog.php");
// Agregamos el Trait de Campaña
require_once("Models/TCampana.php");

class Home extends Controllers
{
    // Añadimos TCampana al uso de Traits
    use TCategoria, TProducto, TSlider, TBlog, TCampana {
        TCategoria::getCategoriasT insteadof TBlog;
        TBlog::getCategoriasT as getCategoriasBlogT;
    }

    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function index()
    {
        $data['page_id'] = 1;
        $data['tag_page'] = NOMBRE_EMPRESA;
        $data['page_title'] = NOMBRE_EMPRESA;
        $data['page_name'] = "home";

        $data['slider'] = $this->getSlidersT();
        $data['banner'] = $this->getCategoriasT(CAT_BANNER);
        $data['productos'] = $this->getProductosT();
        $data['posts'] = $this->getPostsT(3);
        $data['categorias_footer'] = $this->getCategoriasT();

        // --- LÓGICA DEL POPUP PROTEGIDA ---
        $data['campana_popup'] = false; // Por defecto no se muestra

        // Si el usuario está logueado, verificamos si YA participó
        if (!empty($_SESSION['login'])) {
            $idUsuario = $_SESSION['idUser'];
            require_once("Models/CuponModel.php");
            $objCupones = new CuponModel();

            // Consultamos si ya tiene registros en cupon_usuario
            $participacion = $objCupones->verificarParticipacionRuleta($idUsuario);

            // SOLO si NO ha participado, buscamos la campaña activa
            if (empty($participacion)) {
                $data['campana_popup'] = $this->getPopupActiveT();
            }
        } else {
            $data['campana_popup'] = $this->getPopupActiveT();
        }
        // ----------------------------------

        $this->views->getView($this, "home", $data);
    }
}
