<?php
require_once("Config/config.php");
require_once("Helpers/Helpers.php");
require_once("Libraries/Core/Autoload.php");

$dataEmail = array(
    'email' => 'mdpertuz08@gmail.com',
    'nombreUsuario' => 'Developer Sayana',
    'asunto' => 'Confirmación de Orden - ' . NOMBRE_EMPRESA,
    'pedido' => array(
        'numpedido' => 105,              // Cambiado de idpedido a numpedido
        'referencia' => 'ABC-123-XYZ',
        'fecha' => date("d/m/Y"),
        'monto' => 155000,
        'productos' => array(            // Cambiado de detalle a productos
            array(
                'producto' => 'Reloj Sayana Luxury Gold',
                'precio' => 120000,
                'cantidad' => 1
            ),
            array(
                'producto' => 'Pulsera Minimalista Silver',
                'precio' => 35000,
                'cantidad' => 1
            )
        )
    )
);

$envio = sendEmail($dataEmail, 'email_notificacion_orden');

if($envio){
    echo "<h1>¡Correo de Orden enviado con éxito!</h1>";
} else {
    echo "<h1>Error en el envío</h1>";
}