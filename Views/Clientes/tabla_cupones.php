<div class="bor10 p-lr-40 p-t-40 p-b-40 p-lr-15-sm card-perfil-sayana">
    <h4 class="mtext-109 cl2 p-b-30 title-perfil-sayana">
        <i class="fa fa-ticket-alt m-r-10"></i> Mis Cupones de Descuento
    </h4>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="stext-110 cl2">
                    <th>Código</th>
                    <th>Descuento</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($data['cupones'])) {
                    $fecha_actual = date("Y-m-d");

                    foreach ($data['cupones'] as $cupon) {
                        // Lógica de validación CORREGIDA
                        $fecha_vencimiento = $cupon['fecha_vencimiento'];
                        $esta_vencido = ($fecha_vencimiento < $fecha_actual);

                        // Si "fecha_uso" NO está vacía, significa que ESTE usuario ya lo usó
                        $esta_usado = !empty($cupon['fecha_uso']);

                        // Definición de estilos y etiquetas según el estado
                        if ($esta_usado) {
                            $statusLabel = 'Usado';
                            $badgeClass = 'badge-secondary';
                            $badgeStyle = 'background-color: #6c757d;';
                            $rowStyle = 'opacity: 0.6; background-color: #f8f9fa;';
                        } elseif ($esta_vencido) {
                            $statusLabel = 'Expirado';
                            $badgeClass = 'badge-danger';
                            $badgeStyle = 'background-color: #dc3545;';
                            $rowStyle = 'opacity: 0.6;';
                        } else {
                            $statusLabel = 'Disponible';
                            $badgeClass = 'badge-success';
                            $badgeStyle = 'background-color: #28a745;';
                            $rowStyle = '';
                        }
                ?>
                        <tr class="stext-111 cl2" style="<?= $rowStyle ?>">
                            <td>
                                <b style="color: #f77870; font-family: monospace; font-size: 1.2em; letter-spacing: 1px;">
                                    <?= $cupon['codigo'] ?>
                                </b>
                            </td>
                            <td><span class="mtext-101" style="color: #333;"><?= $cupon['descuento'] ?>%</span></td>
                            <td><?= $fecha_vencimiento ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?>" style="<?= $badgeStyle ?> color:white; padding: 6px 12px; border-radius: 20px; font-weight: 500;">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4" class="text-center p-t-40 p-b-40">
                            <div class="flex-col-c-m">
                                <i class="fa fa-gift fa-3x m-b-15" style="color: #e6e6e6;"></i>
                                <p class="stext-115 cl4">Aún no tienes cupones disponibles.</p>
                                <p class="stext-111 cl8">¡Participa en nuestras dinámicas para ganar descuentos!</p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>