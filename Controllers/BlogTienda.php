<?php 
require_once("Models/TBlog.php");

class BlogTienda extends Controllers {
    use TBlog;

    public function __construct() {
        parent::__construct();
        sessionStart(); 
    }

    // Este es el que te funciona, lo dejamos tal cual
    public function blog() {
        $data['tag_page'] = "Blog - Sayana";
        $data['page_title'] = "Blog informativo";
        $data['page_name'] = "blog";
        $data['posts'] = $this->getPostsT(); 
        $data['categorias'] = $this->getCategoriasT(); 
        $this->views->getView($this, "blog", $data);
    }

    public function articulo($params) {
        if (empty($params)) {
            header("Location: " . base_url() . '/blogtienda/blog');
            die();
        }

        $strRuta = strClean($params);
        $data['post'] = $this->getPostT($strRuta); 

        if (empty($data['post'])) {
            header("Location: " . base_url() . '/blogtienda/blog');
            die();
        }

        $data['tag_page'] = $data['post']['titulo'] . " - Sayana";
        $data['page_title'] = $data['post']['titulo'];
        $data['page_name'] = "articulo";
        $data['recientes'] = $this->getPostsT(); 

        $this->views->getView($this, "articulo", $data);
    }
}