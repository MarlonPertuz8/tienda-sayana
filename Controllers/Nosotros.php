<?php 
// Requerimos el Trait
require_once("Models/TNosotros.php");

class Nosotros extends Controllers {
    // Usamos el Trait aquí
    use TNosotros;

    public function __construct() {
        parent::__construct();
        // Aseguramos que la sesión esté iniciada para leer el carrito
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function nosotros() {
        $data['tag_page'] = "Nosotros | Sayana";
        $data['page_title'] = "Nuestra Esencia";
        $data['page_name'] = "nosotros";
        
        // 1. Cargamos la información del Trait
        $data['info'] = $this->getNosotrosT();

        // 2. IMPORTANTE: Pasamos los datos del carrito de la sesión al arreglo $data
        // Esto permite que headerTienda($data) reconozca los productos
        $data['carrito'] = isset($_SESSION['arrCarrito']) ? $_SESSION['arrCarrito'] : [];
        
        // 3. Si tu header depende de categorías para el menú, asegúrate de cargarlas también
        // $data['categorias'] = $this->getCategorias(); 
        
        $this->views->getView($this, "nosotros", $data);
    }
}