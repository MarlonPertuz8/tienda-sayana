<?php
    require_once("Models/TCategoria.php");
    require_once("Models/TProducto.php");
    require_once("Models/TSlider.php"); 
    require_once("Models/TBlog.php"); 

    class Home extends Controllers {
        // 1. Aquí resolvemos la colisión
        use TCategoria, TProducto, TSlider, TBlog {
            // Le decimos que para "getCategoriasT" use la de TCategoria (la de los banners)
            TCategoria::getCategoriasT insteadof TBlog;
            
            // Y a la de TBlog le ponemos un apodo para que no se pierda
            TBlog::getCategoriasT as getCategoriasBlogT;
        }

        public function __construct() {
            parent::__construct();
            session_Start();
        }

        public function index() {
            $data['page_id'] = 1;
            $data['tag_page'] = NOMBRE_EMPRESA;
            $data['page_title'] = NOMBRE_EMPRESA;
            $data['page_name'] = "home";

            $data['slider'] = $this->getSlidersT();
            
            // Esto ahora funcionará sin error usando TCategoria
            $data['banner'] = $this->getCategoriasT(CAT_BANNER); 
            
            $data['productos'] = $this->getProductosT();

            // 2. Traemos los posts con el nuevo parámetro de límite
            $data['posts'] = $this->getPostsT(3); 
            $data['categorias_footer'] = $this->getCategoriasT();

            $this->views->getView($this, "home", $data);
        }
    }