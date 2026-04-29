<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - <?= NOMBRE_EMPRESA ?></title>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { width: 100%; padding: 20px 15px; background-color: #f4f7f9; }
        .container { max-width: 600px; margin: auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); }
        .header { background: #0f172a; padding: 30px 20px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 20px; margin: 0; text-transform: uppercase; letter-spacing: 2px; }
        .body { padding: 30px; color: #334155; }
        .order-info { text-align: center; margin-bottom: 30px; }
        .order-id { font-size: 24px; font-weight: bold; color: #2563eb; display: block; }
        .table-container { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table-container th { border-bottom: 2px solid #f1f5f9; padding: 12px; text-align: left; font-size: 13px; color: #64748b; text-transform: uppercase; }
        .table-container td { padding: 15px 12px; border-bottom: 1px solid #f1f5f9; font-size: 15px; }
        .product-name { font-weight: 600; color: #0f172a; display: block; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-section { margin-top: 20px; float: right; width: 100%; max-width: 250px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .total-main { font-size: 18px; font-weight: bold; color: #0f172a; border-top: 2px solid #f1f5f9; margin-top: 10px; padding-top: 10px; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="<?= media(); ?>/images/logoSayana.png" alt="Sayana Luxury" style="max-width:120px; margin-bottom:10px;">
                <h1>¡Gracias por tu compra!</h1>
            </div>

            <div class="body">
                <div class="order-info">
                    <p>Hola <strong><?= (!empty($data['nombreUsuario'])) ? $data['nombreUsuario'] : 'Cliente'; ?></strong>, hemos recibido tu pedido con éxito.</p>
                    <span class="order-id">Orden #<?= $data['pedido']['numpedido']; ?></span>
                </div>

                <table class="table-container">
                    <thead>
                        <tr>
                            <th colspan="2">Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(!empty($data['pedido']['productos'])){
                            foreach ($data['pedido']['productos'] as $producto) { 
                                // Aseguramos que la ruta de la imagen sea completa
                                $rutaImagen = !empty($producto['portada']) ? $producto['portada'] : media().'/images/uploads/product.png';
                                $nombreProd = !empty($producto['nombre']) ? $producto['nombre'] : 'Producto de Sayana';
                        ?>
                            <tr>
                                <td style="width: 60px; vertical-align: middle;">
                                    <img src="<?= $rutaImagen ?>" 
                                         alt="<?= $nombreProd ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #f1f5f9;">
                                </td>
                                <td style="vertical-align: middle;">
                                    <span class="product-name"><?= $nombreProd ?></span>
                                </td>
                                <td class="text-center" style="vertical-align: middle;"><?= $producto['cantidad'] ?></td>
                                <td class="text-right" style="vertical-align: middle;">
                                    <?= SMONEY . formatMoneda($producto['precio'] * $producto['cantidad']) ?>
                                </td>
                            </tr>
                        <?php 
                            } 
                        } 
                        ?>
                    </tbody>
                </table>

                <div class="totals-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span><?= SMONEY . formatMoneda($data['pedido']['monto']) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Envío:</span>
                        <span>Gratis</span>
                    </div>
                    <div class="total-row total-main">
                        <span>TOTAL:</span>
                        <span><?= SMONEY . formatMoneda($data['pedido']['monto']) ?></span>
                    </div>
                </div>

                <div style="clear: both; padding-top: 30px;">
                    <p style="font-size: 14px; color: #64748b;">
                        Referencia de seguimiento: <strong><?= $data['pedido']['referencia'] ?></strong><br>
                        Pronto recibirás un correo con la guía de seguimiento de tu paquete. 
                        Si tienes dudas, contáctanos a soporte@sayanaluxury.com
                    </p>
                </div>
            </div>

            <div class="footer">
                <strong>© <?= date("Y"); ?> <?= NOMBRE_EMPRESA ?></strong><br>
                Este es un recibo digital de tu compra.
            </div>
        </div>
    </div>
</body>
</html>