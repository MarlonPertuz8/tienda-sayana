<?php headerTienda($data);

if (empty($data['orden']['pedido'])) {
    echo '<div class="container text-center" style="padding: 100px 20px;">
            <h2 style="color: #274e66;">¡Ups! Pedido no encontrado</h2>
            <p>Parece que el número de pedido no es válido o no tienes permiso para verlo.</p>
            <br>
            <a href="' . base_url() . '/pedidos" class="btn-regresar">Volver a mis pedidos</a>
          </div>';
    footerTienda($data);
    exit; // Detiene el resto del archivo
}

// Extraemos los datos con seguridad
$pedido = $data['orden']['pedido'];
$detalle = $data['orden']['detalle'];
?>

<div class="orden-hero">
    <div class="container">

        <div class="mb-4">
            <a href="<?= base_url(); ?>/pedidos" class="btn-regresar">← Volver a mis pedidos</a>
        </div>

        <div class="card-luxury" style="padding: 20px 40px; border-radius: 0;">
            <div class="stepper-luxury">
                <div class="step <?= ($pedido['status'] >= 1) ? 'active' : ''; ?>">Recibido</div>
                <div class="step <?= ($pedido['status'] >= 2) ? 'active' : ''; ?>">Procesando</div>
                <div class="step <?= ($pedido['status'] >= 3) ? 'active' : ''; ?>">Enviado</div>
                <div class="step <?= ($pedido['status'] >= 4) ? 'active' : ''; ?>">Entregado</div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-8">
                <div class="card-luxury">
                    <div class="order-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                        <h2 class="order-title" style="margin: 0;">
                            Pedido #<?= $data['orden']['numero_secuencial'] ?? $pedido['idpedido']; ?>
                        </h2>

                        <span class="badge" style="background: <?php
                                                                if ($pedido['status'] == 1) echo '#f3635a';     // Rojo (Pendiente)
                                                                elseif ($pedido['status'] == 2) echo '#f39c12'; // Naranja (Procesando)
                                                                elseif ($pedido['status'] == 3) echo '#274e66'; // Azul Sayana (Enviado)
                                                                elseif ($pedido['status'] == 4) echo '#28a745'; // Verde (Entregado)
                                                                else echo '#6c757d';                            // Gris (Otro/Cancelado)
                                                                ?>; color: #fff; padding: 8px 15px; border-radius: 50px; font-size: 10px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">

                            <?php
                            if ($pedido['status'] == 1) echo 'Pendiente';
                            elseif ($pedido['status'] == 2) echo 'Procesando';
                            elseif ($pedido['status'] == 3) echo 'Enviado';
                            elseif ($pedido['status'] == 4) echo 'Entregado';
                            else echo 'Desconocido';
                            ?>
                        </span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <span class="info-label">Fecha de Compra</span>
                            <span class="info-value"><?= $pedido['fecha']; ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Método de Pago</span>
                            <span class="info-value">
                                <?php
                                if ($pedido['tipopagoid'] == 1) echo "Wompi Pay";
                                elseif ($pedido['tipopagoid'] == 2) echo "Transferencia";
                                else echo "Contra entrega";
                                ?>
                            </span>
                        </div>
                    </div>

                    <table class="table-detalle">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Precio</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalle as $item) { ?>
                                <tr>
                                    <td><?= $item['producto'] ?? $item['nombre'] ?? 'Producto de Lujo'; ?></td>
                                    <td class="text-center"><?= formatMoneda($item['precio']); ?></td>
                                    <td class="text-center"><?= $item['cantidad']; ?></td>
                                    <td class="text-right" style="font-weight: 700;"><?= formatMoneda($item['precio'] * $item['cantidad']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="total-row mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color: #888;">Subtotal</span>
                            <span><?= formatMoneda($pedido['monto']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span style="color: #888;">Envío</span>
                            <span>Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong style="font-size: 18px;">TOTAL</strong>
                            <strong style="font-size: 18px; color: #f3635a;"><?= formatMoneda($pedido['monto']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-luxury">
                    <h3 class="order-title" style="font-size: 18px; margin-bottom: 25px;">Envío</h3>

                    <div class="mb-4">
                        <span class="info-label">Destinatario</span>
                        <span class="info-value">
                            <?php
                            $nom = $_SESSION['userData']['nombres'] ?? 'Cliente';
                            $ape = $_SESSION['userData']['apellidos'] ?? '';
                            echo $nom . ' ' . $ape;
                            ?>
                        </span>
                    </div>

                    <div class="mb-4">
                        <span class="info-label">Dirección</span>
                        <span class="info-value"><?= $pedido['direccion_envio'] ?? 'Dirección no registrada'; ?></span>
                    </div>

                    <div class="mb-4">
                        <span class="info-label">Ciudad / Estado</span>
                        <span class="info-value"><?= $pedido['ciudad_envio'] ?? 'Ciudad no registrada'; ?></span>
                    </div>

                    <?php if (!empty($pedido['nro_guia'])): ?>
                        <div class="mb-4" style="background: #f0f9ff; padding: 18px; border-radius: 15px; border: 1px solid #d0e7ff; position: relative; overflow: hidden;">
                            <i class="fas fa-shipping-fast" style="position: absolute; right: 10px; bottom: 10px; font-size: 40px; color: rgba(39, 78, 102, 0.05);"></i>

                            <span class="info-label" style="color: #274e66; font-weight: 700; display: block; margin-bottom: 5px;">
                                <i class="fa fa-barcode"></i> Número de Guía
                            </span>
                            <span class="info-value" style="font-size: 18px; font-family: monospace; letter-spacing: 1px; color: #274e66;">
                                <?= $pedido['nro_guia']; ?>
                            </span>

                            <div class="mt-3">
                                <a href="https://www.coordinadora.com/rastreo/rastreo-de-guia/detalle-de-rastreo/?guia=<?= $pedido['nro_guia']; ?>"
                                    target="_blank"
                                    class="btn btn-sm"
                                    style="background: #274e66; color: #fff; border-radius: 8px; font-size: 11px; width: 100%; border: none; padding: 8px;">
                                    <i class="fa fa-search"></i> Rastrear en Coordinadora
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div style="background: <?php echo ($pedido['status'] >= 3) ? '#f0fff4' : '#fff8f7'; ?>; 
                    padding: 15px; 
                    border-radius: 15px; 
                    border: 1px dashed <?php echo ($pedido['status'] >= 3) ? '#28a745' : '#f3635a'; ?>;">

                        <p style="font-size: 12px; color: <?php echo ($pedido['status'] >= 3) ? '#28a745' : '#f3635a'; ?>; margin: 0; font-weight: 600;">
                            <?php if ($pedido['status'] == 1): ?>
                                <i class="fa fa-clock"></i> Tu pedido ha sido recibido.
                            <?php elseif ($pedido['status'] == 2): ?>
                                <i class="fa fa-info-circle"></i> Tu pedido está siendo procesado.
                            <?php elseif ($pedido['status'] == 3): ?>
                                <i class="fa fa-truck"></i> ¡Tu pedido ha sido enviado! Revisa tu guía arriba.
                            <?php else: ?>
                                <i class="fa fa-check-circle"></i> Tu pedido ha sido entregado con éxito.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php footerTienda($data); ?>