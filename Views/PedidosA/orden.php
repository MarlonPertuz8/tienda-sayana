<?php headerAdmin($data); ?>
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text-o"></i> <?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/pedidosA">Pedidos</a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <section class="invoice">
                    <div class="row mb-4">
                        <div class="col-6">
                            <h2 class="page-header"><img src="<?= media(); ?>/images/logoSayana.png" alt="Sayana Luxury" style="width: 150px;"></h2>
                        </div>
                        <div class="col-6 text-right">
                            <h5 class="text-muted">Fecha: <?= $data['pedido']['orden']['fecha']; ?></h5>
                        </div>
                    </div>

                    <div class="row invoice-info mb-4">
                        <div class="col-4">
                            <strong>CLIENTE</strong>
                            <address>
                                <strong><?= $data['pedido']['orden']['nombre'] . ' ' . $data['pedido']['orden']['apellido']; ?></strong><br>
                                Teléfono: <?= $data['pedido']['orden']['telefono']; ?><br>
                                Email: <?= $data['pedido']['orden']['email_user']; ?>
                            </address>
                        </div>
                        <div class="col-4">
                            <strong>ENVÍO</strong>
                            <address>
                                Dirección: <?= $data['pedido']['orden']['direccion_envio']; ?><br>
                                Ciudad: <?= $data['pedido']['orden']['ciudad_envio']; ?><br>
                                Costo Envío: <?= formatMoneda($data['pedido']['orden']['costo_envio']); ?>
                            </address>
                        </div>
                        <div class="col-4">
                            <b>Orden #<?= $data['pedido']['orden']['idpedido']; ?></b><br>
                            <b>Estado:</b> <?= $data['pedido']['orden']['status'] == 4 ? 'Entregado' : 'Pendiente'; ?><br>
                            <b>Pago ID:</b> <?= $data['pedido']['orden']['tipopagoid']; ?>
                        </div>
                    </div>

                <?php 
    // 1. Limpiamos y convertimos la ciudad a minúsculas para una comparación segura
    $ciudadEnvio = !empty($data['pedido']['orden']['ciudad_envio']) ? trim(mb_strtolower($data['pedido']['orden']['ciudad_envio'])) : "";

    // 2. Solo mostramos el panel si:
    //    - El barrioid está vacío (es nacional) 
    //    - Y la ciudad NO es cartagena (doble seguridad para tu caso actual)
    if (empty($data['pedido']['orden']['barrioid']) && $ciudadEnvio != "cartagena") { 
?>
    <div class="row mt-4 d-print-none">
        <div class="col-md-6">
            <div class="tile shadow-sm border-left-primary">
                <div class="tile-body">
                    <h5 class="mb-3" style="color: #c9a050;">
                        <i class="fas fa-shipping-fast"></i> Despacho Nacional
                    </h5>
                    <div class="form-group">
                        <label class="font-weight-bold">Número de Guía (Coordinadora)</label>
                        <div class="input-group">
                            <input type="text" id="txtGuia" class="form-control"
                                placeholder="Ej: 123456789"
                                value="<?= !empty($data['pedido']['orden']['nro_guia']) ? $data['pedido']['orden']['nro_guia'] : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button"
                                    onclick="fntGuardarGuia(<?= $data['pedido']['orden']['idpedido']; ?>)">
                                    <i class="fas fa-save"></i> Guardar
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">El cliente recibirá una notificación con este número.</small>
                    </div>

                    <?php if (!empty($data['pedido']['orden']['nro_guia'])) { ?>
                        <div class="mt-3">
                            <a href="https://www.coordinadora.com/rastreo/rastreo-de-guia/rastreo-de-guia/?guia=<?= $data['pedido']['orden']['nro_guia']; ?>"
                                target="_blank" class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-external-link-alt"></i> Rastrear en Coordinadora
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Color</th>
                                        <th class="text-right">Precio</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subtotal_pedido = 0;
                                    // ✅ RECORRIDO DEL DETALLE
                                    foreach ($data['pedido']['detalle'] as $producto) {
                                        $importe = $producto['precio'] * $producto['cantidad'];
                                        $subtotal_pedido += $importe;
                                    ?>
                                        <tr>
                                            <td><?= $producto['producto']; ?></td>
                                            <td><?= $producto['color']; ?></td>
                                            <td class="text-right"><?= formatMoneda($producto['precio']); ?></td>
                                            <td class="text-center"><?= $producto['cantidad']; ?></td>
                                            <td class="text-right"><?= formatMoneda($importe); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Subtotal:</th>
                                        <td class="text-right"><?= formatMoneda($subtotal_pedido); ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Envío:</th>
                                        <td class="text-right"><?= formatMoneda($data['pedido']['orden']['costo_envio']); ?></td>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">Total:</th>
                                        <td class="text-right"><strong><?= formatMoneda($data['pedido']['orden']['monto']); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="row d-print-none mt-2">
                        <div class="col-12 text-right">
                            <a class="btn btn-primary" href="javascript:window.print();"><i class="fa fa-print"></i> Imprimir</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>
<?php footerAdmin($data); ?>