<?php
require_once("Models/TCategoria.php");
require_once("Models/TProducto.php");
require_once("Models/TCliente.php");
require_once("Models/TCarrito.php");

class Carrito extends Controllers
{
    use TCategoria, TProducto, TCliente, TCarrito;

    public function __construct()
    {
        parent::__construct();
        session_start();
    }

    public function addCarrito()
    {
        if ($_POST) {
            if (ob_get_length()) ob_clean();

            $idProducto = openssl_decrypt($_POST['id'], METHODENCRIPT, KEY);
            $cantidad   = intval($_POST['cant']);
            // IMPORTANTE: Verifica si tu JS envía 'color' o 'col'
            $color      = !empty($_POST['color']) ? strClean($_POST['color']) : "";

            if (!is_numeric($idProducto)) {
                echo json_encode(["status" => false, "msg" => "Producto inválido"]);
                die();
            }

            $arrProducto = $this->getProductoIDT($idProducto);

            if (empty($arrProducto)) {
                echo json_encode(["status" => false, "msg" => "Producto no encontrado"]);
                die();
            }

            // --- LÓGICA DE PRECIOS ---
            $precioBase = $arrProducto['precio'];
            $precioOferta = $arrProducto['precio_oferta'] ?? 0;
            $precioFinal = ($precioOferta > 0 && $precioOferta < $precioBase) ? $precioOferta : $precioBase;

            // --- CLAVE ÚNICA PARA SEPARAR POR COLOR ---
            // Si el color es "Celeste", la llave será ej: "12Celeste"
            $idCarrito = $idProducto . $color;

            $arrCarrito = array(
                'idproducto' => $idProducto,
                'producto'   => $arrProducto['nombre'],
                'cantidad'   => $cantidad,
                'precio'     => $precioFinal,
                'precio_original' => $precioBase,
                'imagen'     => (!empty($arrProducto['images'])) ? $arrProducto['images'][0]['url_image'] : "",
                'color'      => $color
            );

            if (isset($_SESSION['arrCarrito'][$idCarrito])) {
                $_SESSION['arrCarrito'][$idCarrito]['cantidad'] += $cantidad;
            } else {
                $_SESSION['arrCarrito'][$idCarrito] = $arrCarrito;
            }

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

   public function delCarrito()
{
    if ($_POST) {
        if (ob_get_length()) ob_clean();
        // Recibimos la llave única (ej: "14Plata")
        $idProducto = strClean($_POST['id']); 

        if (isset($_SESSION['arrCarrito'][$idProducto])) {
            unset($_SESSION['arrCarrito'][$idProducto]);
        }

        // Calculamos la nueva cantidad total
        $cantCarrito = 0;
        if (!empty($_SESSION['arrCarrito'])) {
            foreach ($_SESSION['arrCarrito'] as $pro) {
                $cantCarrito += $pro['cantidad'];
            }
        }

        // Generamos el nuevo HTML del modal
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

public function updateCarrito()
{
    if ($_POST) {
        if (ob_get_length()) ob_clean();
        // Recibimos la llave única directamente desde la vista
        $idKey = strClean($_POST['id']); 
        $action = strClean($_POST['action']);

        // Verificamos si la llave existe en el carrito de la sesión
        if (!isset($_SESSION['arrCarrito'][$idKey])) {
            echo json_encode(["status" => false, "msg" => "No se encontró el producto en el joyero."]);
            die();
        }

        // Actualizamos la cantidad según la acción
        if ($action == "add") {
            $_SESSION['arrCarrito'][$idKey]['cantidad']++;
        } else if ($action == "sub") {
            if ($_SESSION['arrCarrito'][$idKey]['cantidad'] > 1) {
                $_SESSION['arrCarrito'][$idKey]['cantidad']--;
            } else {
                // Si la cantidad llega a 0, lo eliminamos
                unset($_SESSION['arrCarrito'][$idKey]);
            }
        }

        // Recalculamos el contador total de la burbuja
        $cantCarrito = 0;
        if (!empty($_SESSION['arrCarrito'])) {
            foreach ($_SESSION['arrCarrito'] as $pro) {
                $cantCarrito += $pro['cantidad'];
            }
        }

        // Refrescamos el HTML del carrito lateral
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

    public function clearCarrito()
    {
        unset($_SESSION['arrCarrito']);
        unset($_SESSION['descuento_detalle']);

        ob_start();
        $data = [];
        getModal('modalCarrito', $data);
        $htmlCarrito = ob_get_clean();

        echo json_encode([
            'status' => true,
            'cantCarrito' => 0,
            'htmlCarrito' => $htmlCarrito
        ]);
        die();
    }

    public function procesarpago()
    {
        if (empty($_SESSION['arrCarrito'])) {
            header("Location: " . base_url() . "/tienda");
            die();
        }

        $total = 0;
        foreach ($_SESSION['arrCarrito'] as $producto) {
            $total += $producto['precio'] * $producto['cantidad'];
        }

        $data['tag_page'] = "Procesar Pago | Sayana Luxury";
        $data['page_title'] = "Procesar Pago";
        $data['page_name'] = "procesarpago";
        $data['total'] = $total;
        $data['barrios'] = $this->getBarrios();

        $this->views->getView($this, "procesarpago", $data);
    }

    public function aplicarCupon()
    {
        if ($_POST) {
            if (ob_get_length()) ob_clean();
            $strCupon = strClean(strtoupper($_POST['cupon']));

            require_once("Models/CuponModel.php");
            $objCupon = new CuponModel();
            $check = $objCupon->consultarCupon($strCupon);

            if (!empty($check)) {
                $porcentaje = $check['descuento'];
                $totalSujetoADescuento = 0; // Solo para productos sin oferta
                $subtotalGeneral = 0; // El total actual del carrito

                foreach ($_SESSION['arrCarrito'] as $producto) {
                    $precioActual = $producto['precio'];
                    $precioOriginal = $producto['precio_original'];
                    $subtotalGeneral += ($precioActual * $producto['cantidad']);

                    // LÓGICA: Si el precio actual es igual al original, no tiene descuento previo
                    if ($precioActual == $precioOriginal) {
                        $totalSujetoADescuento += ($precioActual * $producto['cantidad']);
                    }
                }

                if ($totalSujetoADescuento > 0) {
                    $montoDescuentoCupon = $totalSujetoADescuento * ($porcentaje / 100);

                    $_SESSION['descuento_detalle'] = [
                        'id' => $check['idcupon'],
                        'codigo' => $strCupon,
                        'monto' => $montoDescuentoCupon
                    ];

                    echo json_encode([
                        "status" => true,
                        "msg" => "Cupón aplicado: -$" . formatMoneda($montoDescuentoCupon),
                        "total_descuento_format" => SMONEY . formatMoneda($subtotalGeneral - $montoDescuentoCupon),
                        "total_descuento" => ($subtotalGeneral - $montoDescuentoCupon)
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    unset($_SESSION['descuento_detalle']);
                    echo json_encode([
                        "status" => false,
                        "msg" => "El cupón no aplica a productos que ya tienen descuento."
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                unset($_SESSION['descuento_detalle']);
                echo json_encode(["status" => false, "msg" => "Cupón no válido."], JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }

public function procesarPedido()
{
    if ($_POST) {
        if (empty($_SESSION['arrCarrito'])) {
            echo json_encode(["status" => false, "msg" => "Carrito vacío."], JSON_UNESCAPED_UNICODE);
            die();
        }

        // 1. Limpieza y recepción de datos
        $strDireccion = strClean($_POST['txtDireccion']);
        $strCiudad    = strClean($_POST['txtCiudad']);
        $intIdBarrio  = intval($_POST['listBarrio']);
        $floatEnvio   = floatval($_POST['txtCostoEnvio']);
        if ($intIdBarrio == 0) $intIdBarrio = null;

        $metodoRaw    = $_POST['intTipopago'];
        $intTipopago  = ($metodoRaw == 'transferencia' || $metodoRaw == 1) ? 1 : 2;
        $intIdUsuario = $_SESSION['idUser'];
        $intStatus    = 1; // Pendiente

        // 2. Cálculo del total del carrito
        $subtotal = 0;
        foreach ($_SESSION['arrCarrito'] as $producto) {
            $subtotal += $producto['precio'] * $producto['cantidad'];
        }

        $montoDescuento = !empty($_SESSION['descuento_detalle']) ? $_SESSION['descuento_detalle']['monto'] : 0;
        $montoFinal = ($subtotal - $montoDescuento) + $floatEnvio;

        // 3. Insertar Pedido
        $request_pedido = $this->insertPedido(
            $intIdUsuario, $strDireccion, $strCiudad, $intIdBarrio, $floatEnvio, $montoFinal, $intTipopago, $intStatus
        );

        if ($request_pedido > 0) {
            // 4. Procesar productos
            foreach ($_SESSION['arrCarrito'] as $pro) {
                $idProducto = $pro['idproducto'];
                $cantComprada = $pro['cantidad'];
                $colorDetalle = !empty($pro['color']) ? $pro['color'] : "";

                // A. Insertar detalle
                $this->insertDetallePedido($request_pedido, $idProducto, $pro['precio'], $cantComprada, $colorDetalle);

                // B. Actualizar STOCK (Tu función actual)
                $this->updateStockProducto($idProducto, $cantComprada);

                // C. NUEVO: Registrar en historial de inventario
                $observacion = "Venta Online - Pedido #".$request_pedido;
                $this->registrarSalidaInventario($idProducto, $intIdUsuario, $cantComprada, $colorDetalle, $observacion);

                $productosEmail[] = array(
                    'nombre'   => $pro['producto'],
                    'precio'   => $pro['precio'],
                    'cantidad' => $cantComprada,
                    'portada'  => $pro['imagen'],
                    'color'    => $colorDetalle
                );
            }

            // 5. Obtener contador de orden
            $con = new Mysql();
            $sqlCount = "SELECT COUNT(*) as total FROM pedido WHERE personaid = $intIdUsuario";
            $resCount = $con->select($sqlCount);
            $nroOrdenUsuario = $resCount['total'];

            // ... (Resto de tu código de Email y Notificaciones se mantiene igual) ...
            
            $nombreCliente = "Cliente";
            $emailCliente = "";
            if (!empty($_SESSION['userData']['nombre'])) {
                $nombreCliente = $_SESSION['userData']['nombre'] . ' ' . ($_SESSION['userData']['apellido'] ?? '');
                $emailCliente = $_SESSION['userData']['email_user'];
            } else {
                $sqlUser = "SELECT nombre, apellido, email_user FROM persona WHERE idpersona = $intIdUsuario";
                $userData = $con->select($sqlUser);
                if ($userData) {
                    $nombreCliente = $userData['nombre'] . ' ' . $userData['apellido'];
                    $emailCliente = $userData['email_user'];
                }
            }

            if (!empty($emailCliente)) {
                $dataEmail = array(
                    'email'         => $emailCliente,
                    'nombreUsuario' => $nombreCliente,
                    'asunto'        => 'Confirmación de Orden #' . $nroOrdenUsuario . ' - ' . NOMBRE_EMPRESA,
                    'pedido'        => array(
                        'numpedido'  => $nroOrdenUsuario,
                        'referencia' => 'REF-' . strtoupper(bin2hex(random_bytes(3))),
                        'fecha'      => date("d/m/Y"),
                        'monto'      => $montoFinal,
                        'productos'  => $productosEmail
                    )
                );
                sendEmail($dataEmail, 'email_notificacion_orden');
            }

            $tituloNotif = "Pedido #$nroOrdenUsuario Recibido";
            $mensajeNotif = "Hola $nombreCliente, hemos recibido tu pedido con éxito.";
            $queryNotif = "INSERT INTO notificacion(usuarioid, titulo, mensaje, leido, pedido_id) VALUES(?,?,?,?,?)";
            $con->insert($queryNotif, [$intIdUsuario, $tituloNotif, $mensajeNotif, 0, $request_pedido]);

            if (!empty($_SESSION['descuento_detalle'])) {
                $idCupon = $_SESSION['descuento_detalle']['id'];
                require_once("Models/CuponModel.php");
                $objCupon = new CuponModel();
                $objCupon->registrarUsoCupon($intIdUsuario, $idCupon);
            }

            unset($_SESSION['arrCarrito']);
            unset($_SESSION['descuento_detalle']);

            echo json_encode(["status" => true, "orden" => $nroOrdenUsuario, "msg" => "¡Pedido realizado con éxito!"], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "msg" => "Error al procesar la orden."], JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}

    public function confirmacion()
    {
        if (empty($_GET['p'])) {
            header("Location: " . base_url());
            die();
        }

        $idPedidoGlobal = intval($_GET['p']);
        $intIdUsuario = $_SESSION['idUser'];

        $data['tag_page'] = "Confirmación | Sayana Luxury";
        $data['page_title'] = "Confirmación de compra";
        $data['page_name'] = "confirmacion";

        // CORRECCIÓN: Usamos el ID real del pedido para que coincida con el correo y la DB
        // En lugar de contar (COUNT), simplemente asignamos el ID que viene por la URL
        $data['order'] = $idPedidoGlobal;

        $this->views->getView($this, "confirmacion", $data);
    }
    public function procesarRegistro()
    {
        if ($_POST) {
            // Limpiamos cualquier salida previa para evitar errores en el JSON
            if (ob_get_length()) ob_clean();

            if (empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtEmail']) || empty($_POST['txtTelefonoRegistro'])) {
                $arrResponse = array("status" => false, "msg" => "Datos incompletos.");
            } else {
                $strNombre   = strClean($_POST['txtNombre']);
                $strApellido = strClean($_POST['txtApellido']);
                $strEmail    = strtolower(strClean($_POST['txtEmail']));
                $strTelefono = strClean($_POST['txtTelefonoRegistro']);

                // CAMBIO CLAVE: Usamos la constante RCLIENTES que configuramos en 5
                $intTipoId   = RCLIENTES;

                $strPassword     = passGenerator();
                $strPasswordHash = hash("SHA256", $strPassword);

                // 1. Insertamos al cliente
                $request_user = $this->insertCliente(
                    $strNombre,
                    $strApellido,
                    $strTelefono,
                    $strEmail,
                    $strPasswordHash,
                    $intTipoId
                );

                if ($request_user > 0) {
                    // 2. Iniciamos sesión básica
                    $_SESSION['idUser'] = $request_user;
                    $_SESSION['login']  = true;

                    // 3. CARGA CRÍTICA: Llenamos userData inmediatamente
                    require_once("Models/LoginModel.php");
                    $objLoginModel = new LoginModel();
                    $arrData = $objLoginModel->sessionLogin($request_user);

                    if (!empty($arrData)) {
                        $_SESSION['userData'] = $arrData;
                    } else {
                        $_SESSION['userData'] = array(
                            'idpersona'  => $request_user,
                            'nombres'    => $strNombre,
                            'apellidos'  => $strApellido,
                            'email_user' => $strEmail,
                            'idrol'      => $intTipoId
                        );
                    }

                    // 4. Enviar Email de Bienvenida
                    $dataUsuario = array(
                        'nombreUsuario' => $strNombre . ' ' . $strApellido,
                        'email'         => $strEmail,
                        'password'      => $strPassword,
                        'asunto'        => 'Bienvenido a ' . NOMBRE_EMPRESA
                    );

                    sendEmail($dataUsuario, 'email_bienvenida');

                    $arrResponse = array("status" => true, "msg" => "Usuario registrado correctamente.");
                } else if ($request_user == 'exist') {
                    $arrResponse = array("status" => false, "msg" => "¡Atención! El email ya está registrado.");
                } else {
                    $arrResponse = array("status" => false, "msg" => "No es posible almacenar los datos.");
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
