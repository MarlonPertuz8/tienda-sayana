<div class="card-perfil-sayana animate__animated animate__fadeIn" style="border-radius: 20px; background: #fff; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div class="p-b-30">
        <h4 class="mtext-109 cl2" style="color: #c9a050; font-weight: 900; letter-spacing: 2px;">
            <i class="fa fa-shopping-bag m-r-10" style="color: #c9a050;"></i> MIS PEDIDOS
        </h4>
        <p class="stext-102 cl6">Historial de adquisiciones exclusivas</p>
    </div>

    <div class="table-responsive">
        <table class="table-luxury-perfil">
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
                <?php if(!empty($data['pedidos'])){ 
                    // 1. Contamos el total de pedidos para iniciar la cuenta regresiva
                    $n = count($data['pedidos']); 

                    foreach ($data['pedidos'] as $pedido) { ?>
                    <tr>
                        <td data-label="Orden"><strong>#<?= $n; ?></strong></td>
                        <td data-label="Fecha"><?= $pedido['fecha']; ?></td>
                        <td data-label="Monto" style="font-weight: bold; color: #333;"><?= formatMoneda($pedido['monto']); ?></td>
                        <td data-label="Pago" class="stext-111">
                            <?php 
                                if($pedido['tipopagoid'] == 1) echo "Wompi (PSE)";
                                elseif($pedido['tipopagoid'] == 2) echo "Transferencia";
                                else echo "Contra entrega";
                            ?>
                        </td>
                        <td data-label="Estado">
                            <?php if($pedido['status'] == 1){ ?>
                                <span class="status-badge-sayana" style="background: #f3635a;">Pendiente</span>
                            <?php }else{ ?>
                                <span class="status-badge-sayana" style="background: #28a745;">Procesado</span>
                            <?php } ?>
                        </td>
                        <td data-label="Acción" class="text-right">
                            <a href="<?= base_url(); ?>/pedidos/orden/<?= $pedido['idpedido']; ?>" class="btn-detalle-dorado">
                                Ver Detalle
                            </a>
                        </td>
                    </tr>
                <?php 
                    $n--; // 3. Restamos 1 para que el siguiente pedido sea el número anterior
                    } 
                } else { ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <p>Tu joyero de pedidos está vacío.</p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>