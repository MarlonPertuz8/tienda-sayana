<?php
require_once("Models/TCategoria.php");
require_once("Models/TProducto.php");
require_once("Models/TNotificacion.php");

class Tienda extends Controllers
{
    use TCategoria, TProducto, TNotificacion;

    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    private function formatPrecios(&$productos)
    {
        if (empty($productos)) return;

        // Si es un solo producto (array asociativo simple)
        if (isset($productos['idproducto'])) {
            $this->aplicarLogica($productos);
        } else {
            // Si es una lista de productos
            foreach ($productos as $key => $value) {
                $this->aplicarLogica($productos[$key]);
            }
        }
    }

    private function aplicarLogica(&$item)
    {
        $precioReg = $item['precio'];
        $precioOferta = $item['precio_oferta'] ?? 0;

        if ($precioOferta > 0 && $precioOferta < $precioReg) {
            $item['precio_final'] = $precioOferta;
            $item['precio_viejo'] = formatMoneda($precioReg);
            $item['precio_actual'] = formatMoneda($precioOferta);
            $item['on_sale'] = true;
            // Cálculo del porcentaje de descuento
            $item['descuento'] = round((1 - ($precioOferta / $precioReg)) * 100) . "% OFF";
        } else {
            $item['precio_final'] = $precioReg;
            $item['precio_actual'] = formatMoneda($precioReg);
            $item['precio_viejo'] = "";
            $item['on_sale'] = false;
            $item['descuento'] = "";
        }
    }

   public function index($pagina = 1)
{
    // Paginación
    $porPagina = 8; 
    $numPagina = is_numeric($pagina) ? (int)$pagina : 1;
    $inicio = ($numPagina - 1) * $porPagina;

    $data['tag_page'] = NOMBRE_EMPRESA . " | Tienda";
    $data['page_title'] = "Nuestra Tienda";
    $data['page_name'] = "tienda";

    if (!empty($_GET['s'])) {
        $busqueda = strClean($_GET['s']);
        $data['productos'] = $this->getBusquedaT($busqueda);
        $data['page_title'] = "Resultados para: " . $busqueda;
    } else {
        // Traemos el conteo y los productos con la nueva función
        $totalProductos = $this->cantProductosT();
        $data['productos'] = $this->getProductosPageT($inicio, $porPagina);
        
        $data['total_paginas'] = ceil($totalProductos / $porPagina);
        $data['pagina'] = $numPagina;
    }

    // Tu lógica de formato que ya tienes
    if(!empty($data['productos'])){
        $this->formatPrecios($data['productos']);
    }

    $data['categorias'] = $this->getCategoriasT(null);
    $data['materiales'] = $this->getMaterialesT();

    $this->views->getView($this, "tienda", $data);
}

    public function categoria($params)
    {
        if (empty($params)) {
            header('Location: ' . base_url() . '/tienda');
            die();
        } else {
            $arrParams = explode("/", $params);
            $idcategoria = intval($arrParams[0]);

            if ($idcategoria <= 0) {
                header('Location: ' . base_url() . '/tienda');
                die();
            }

            $data['info'] = $this->getProductosCategoriaT($idcategoria);

            if (empty($data['info']) || empty($data['info']['categoria'])) {
                header('Location: ' . base_url() . '/tienda');
                die();
            }

            $data['page_title'] = $data['info']['categoria'];
            $data['tag_page'] = NOMBRE_EMPRESA . " | " . $data['page_title'];
            $data['page_name'] = "categoria";
            $data['productos'] = $data['info']['productos'];

            // PROCESAR PRECIOS EN CATEGORÍA
            $this->formatPrecios($data['productos']);

            $data['categorias'] = $this->getCategoriasT();
            $this->views->getView($this, "categoria", $data);
        }
    }


    public function getProducto($idproducto)
    {
        if (ob_get_length()) ob_clean();
        $id = intval($idproducto);

        if ($id > 0) {
            $dataProducto = $this->getProductoIDT($id);

            if (!empty($dataProducto)) {
                // Encriptar ID
                $dataProducto['idproducto'] = openssl_encrypt($dataProducto['idproducto'], METHODENCRIPT, KEY);

                // Formato de precios
                $dataProducto['precio_formateado'] = "$" . number_format($dataProducto['precio'], 0, ',', '.');

                // 🔥 AÑADE ESTA LÍNEA para que el JS no reciba "undefined"
                $dataProducto['precio_oferta'] = $dataProducto['precio_oferta'];

                $arrResponse = array(
                    'status' => true,
                    'data' => $dataProducto
                );
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Producto no encontrado');
            }
        } else {
            $arrResponse = array('status' => false, 'msg' => 'ID inválido');
        }

        header('Content-Type: application/json');
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

public function producto($params)
{
    // 1. Verificación inicial de parámetros
    if (empty($params)) {
        header("Location:" . base_url() . '/tienda');
        die();
    }

    $strRuta = strClean($params);

    // 2. Intento de búsqueda: Primero por RUTA (URL amigable)
    $data['producto'] = $this->getProductoT($strRuta);

    // 3. Fallback: Si no lo encuentra por ruta, intentamos por ID numérico
    if (empty($data['producto']) && is_numeric($strRuta)) {
        $data['producto'] = $this->getProductoIDT(intval($strRuta));
    }

    // 4. Redirección final si el producto definitivamente no existe
    if (empty($data['producto'])) {
        header("Location:" . base_url() . '/tienda');
        die();
    }

    // 5. Procesar precios y lógica de visualización
    // Usamos el método formatPrecios que ya definiste en tu controlador
    $this->formatPrecios($data['producto']);

    $idProducto = $data['producto']['idproducto'];
    $idCategoria = $data['producto']['categoriaid'];

    // 6. Obtener y formatear productos relacionados
    $data['productos_relacionados'] = $this->getProductosRelacionadosT($idCategoria, $idProducto);
    $this->formatPrecios($data['productos_relacionados']);

    // 7. Configuración de meta-datos de la página
    $data['tag_page'] = NOMBRE_EMPRESA . " - " . $data['producto']['nombre'];
    $data['page_title'] = $data['producto']['nombre'];
    $data['page_name'] = "producto";

    // 8. Carga de la vista
    $this->views->getView($this, "producto", $data);
}


    // 1. ELIMINAR UN PRODUCTO DEL JOYERO
    public function delCarrito()
    {

        if ($_POST) {

            if (ob_get_length()) ob_clean();

            $idProducto = intval($_POST['id']);

            if (isset($_SESSION['arrCarrito'][$idProducto])) {
                unset($_SESSION['arrCarrito'][$idProducto]);
            }

            // Conteo total
            $cantCarrito = 0;
            if (!empty($_SESSION['arrCarrito'])) {
                foreach ($_SESSION['arrCarrito'] as $pro) {
                    $cantCarrito += $pro['cantidad'];
                }
            }

            ob_start();
            $data = $_SESSION['arrCarrito'] ?? [];
            getModal('modalCarrito', $data);
            $htmlCarrito = ob_get_clean();

            echo json_encode([
                "status" => true,
                "cantCarrito" => $cantCarrito,
                "htmlCarrito" => $htmlCarrito
            ], JSON_UNESCAPED_UNICODE);

            die();
        }
    }

    // 2. ACTUALIZAR CANTIDADES (+ / -) EN EL JOYERO
    public function updateCarrito()
    {
        if ($_POST) {

            if (ob_get_length()) ob_clean();

            $idProducto = intval($_POST['id']);
            $action = strClean($_POST['action']);

            if (!isset($_SESSION['arrCarrito'][$idProducto])) {
                echo json_encode(["status" => false, "msg" => "No se encontró el producto."]);
                die();
            }

            if ($action == "add") {
                $_SESSION['arrCarrito'][$idProducto]['cantidad']++;
            }

            if ($action == "sub") {

                if ($_SESSION['arrCarrito'][$idProducto]['cantidad'] > 1) {
                    $_SESSION['arrCarrito'][$idProducto]['cantidad']--;
                } else {
                    unset($_SESSION['arrCarrito'][$idProducto]);
                }
            }

            // Conteo total
            $cantCarrito = 0;
            foreach ($_SESSION['arrCarrito'] as $pro) {
                $cantCarrito += $pro['cantidad'];
            }

            ob_start();
            $data = $_SESSION['arrCarrito'];
            getModal('modalCarrito', $data);
            $htmlCarrito = ob_get_clean();

            echo json_encode([
                "status" => true,
                "cantCarrito" => $cantCarrito,
                "htmlCarrito" => $htmlCarrito
            ], JSON_UNESCAPED_UNICODE);

            die();
        }
    }
    // 3. VACIAR TODO EL JOYERO
    public function clearCarrito()
    {
        unset($_SESSION['arrCarrito']);

        ob_start();
        $data = [];
        require_once("Views/Template/Modals/modalCarrito.php");
        $htmlCarrito = ob_get_clean();

        echo json_encode([
            'status' => true,
            'cantCarrito' => 0,
            'htmlCarrito' => $htmlCarrito
        ]);
        die();
    }
    public function getNotificacionesCount()
    {
        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => false, 'count' => 0]);
            die();
        }

        // Usamos el ID de la sesión (ajusta 'idPersona' según tu sistema, suele ser userData['idpersona'])
        $idPersona = $_SESSION['userData']['idpersona'];

        // Llamamos directamente a la función del Trait
        $count = $this->getCantNotificacionesT($idPersona);

        echo json_encode([
            'status' => true,
            'count' => $count
        ]);
        die();
    }
    public function getNotificacionesList()
    {
        if (ob_get_length()) ob_clean(); // 🔥 CLAVE

        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => false, 'data' => []]);
            die();
        }

        $idPersona = $_SESSION['userData']['idpersona'];

        $notificaciones = $this->getNotificacionesT($idPersona);

        header('Content-Type: application/json');

        echo json_encode([
            'status' => true,
            'data' => $notificaciones
        ], JSON_UNESCAPED_UNICODE);

        die();
    }


    public function marcarNotificacionesLeidas()
    {
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            // Verifica si tu sistema usa 'idUser' o el array 'userData'
            $idPersona = $_SESSION['idUser'];

            // Esto ejecuta el UPDATE que pusiste en tu Trait
            $request = $this->updateNotificacionesStatus($idPersona);

            echo json_encode(['status' => true], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Sesión no iniciada']);
        }
        die();
    }

    public function search()
    {
        if (empty($_GET['s'])) {
            header("Location: " . base_url() . "/tienda");
        } else {
            $busqueda = strClean($_GET['s']);
            $data['tag_page'] = NOMBRE_EMPRESA . " | Buscar";
            $data['page_title'] = "Resultados de: " . $busqueda;
            $data['page_name'] = "tienda";
            $data['productos'] = $this->getBusquedaT($busqueda); // Función en el Trait
            $data['categorias'] = $this->getCategoriasT(null);

            $this->views->getView($this, "tienda", $data);
        }
        die();
    }
    public function setCalificacion()
    {
        if ($_POST) {
            if (ob_get_length()) ob_clean();

            if (empty($_SESSION['login'])) {
                $arrResponse = array("status" => false, "msg" => "Debes iniciar sesión para calificar.");
            } else {
                $intIdProducto = intval($_POST['idproducto']);
                $intPuntuacion = intval($_POST['puntuacion']);
                // En tu sistema suele ser $_SESSION['userData']['idpersona'] o $_SESSION['idUser']
                $intIdPersona = $_SESSION['idUser'];

                // Llamamos a la función que acabamos de crear en el Trait TProducto
                $request_calificacion = $this->setCalificacionT($intIdProducto, $intIdPersona, $intPuntuacion);

                if ($request_calificacion > 0) {
                    $arrResponse = array("status" => true, "msg" => "¡Calificación guardada exitosamente!");
                } else {
                    $arrResponse = array("status" => false, "msg" => "Error al guardar la calificación.");
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
    public function addWishlist()
    {
        if ($_POST) {
            if (empty($_SESSION['login'])) {
                $arrResponse = array("status" => false, "msg" => "Inicia sesión para agregar a favoritos.");
            } else {
                $intIdProducto = intval($_POST['idproducto']);
                $intIdPersona = $_SESSION['idUser'];

                $request_wishlist = $this->setWishlistT($intIdProducto, $intIdPersona);

                if ($request_wishlist == "add") {
                    $arrResponse = array("status" => true, "action" => "add", "msg" => "Agregado a tus favoritos.");
                } else if ($request_wishlist == "del") {
                    $arrResponse = array("status" => true, "action" => "del", "msg" => "Eliminado de tus favoritos.");
                } else {
                    $arrResponse = array("status" => false, "msg" => "Error en el servidor.");
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function wishlist()
    {
        if (empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/login');
            die();
        }
        $data['tag_page'] = "Wishlist - Sayana";
        $data['page_title'] = "Mi Lista de Deseos";
        $data['page_name'] = "wishlist";
        // Obtenemos los productos guardados para este usuario
        $data['productos'] = $this->getWishlistT($_SESSION['idUser']);

        $this->views->getView($this, "wishlist", $data);
    }
}
