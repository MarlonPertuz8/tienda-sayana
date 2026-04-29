<?php headerTienda($data); ?>

<div class="pedidos-hero">
    <div class="container">
        <div class="text-center mb-5">
            <h1 style="font-weight: 900; letter-spacing: 3px; text-transform: uppercase;">Mis Pedidos</h1>
            <p style="color: #888;">Historial de adquisiciones en Sayana Luxury</p>
        </div>

        <div class="table-responsive">
            <table class="table-luxury">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data['pedidos'])) {
                        // 1. Contamos cuántos pedidos tiene el cliente en total
                        $n = count($data['pedidos']);

                        foreach ($data['pedidos'] as $pedido) { ?>
                            <tr>
                                <td><strong>#<?= $n; ?></strong></td>
                                <td><?= $pedido['fecha']; ?></td>
                                <td><?= formatMoneda($pedido['monto']); ?></td>
                                <td>
                                    <?php
                                    if ($pedido['tipopagoid'] == 1) echo "Wompi (Tarjeta/PSE)";
                                    elseif ($pedido['tipopagoid'] == 2) echo "Transferencia";
                                    elseif ($pedido['tipopagoid'] == 3) echo "Contra entrega";
                                    else echo "Por definir";
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Definimos el color y el texto según el status
                                    if ($pedido['status'] == 1) {
                                        $bgStatus = "#f3635a"; // Rojo - Pendiente
                                        $txtStatus = "Pendiente";
                                    } elseif ($pedido['status'] == 2) {
                                        $bgStatus = "#ffc107"; // Amarillo - Procesado
                                        $txtStatus = "Procesado";
                                    } elseif ($pedido['status'] == 3) {
                                        $bgStatus = "#274e66"; // Azul - Enviado
                                        $txtStatus = "Enviado";
                                    } else {
                                        $bgStatus = "#28a745"; // Verde - Entregado
                                        $txtStatus = "Entregado";
                                    }
                                    ?>
                                    <span class="status-badge" style="background: <?= $bgStatus; ?>; color: #fff; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                        <?= $txtStatus; ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url(); ?>/pedidos/orden/<?= $pedido['idpedido']; ?>" class="btn-detalle">Ver Detalle</a>
                                </td>
                            </tr>
                        <?php
                            $n--; // 4. Restamos 1 para que el siguiente pedido tenga el número anterior
                        }
                    } else { ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php footerTienda($data); ?>