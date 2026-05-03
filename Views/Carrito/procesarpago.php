<?php headerTienda($data); ?>

<br><br><br>
<hr>

<div class="container">
    <div class="bread-crumb flex-w p-l-25 p-r-15 p-t-30 p-lr-0-lg">
        <a href="<?= base_url(); ?>" class="stext-109 cl8 hov-cl1 trans-04">
            Inicio
            <i class="fa fa-angle-right m-l-9 m-r-10" aria-hidden="true"></i>
        </a>
        <span class="stext-109 cl4">
            <?= $data['page_title']; ?>
        </span>
    </div>
</div>

<section class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-xl-7 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl">

                    <?php if (!isset($_SESSION['login'])) { ?>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active stext-101 cl2" id="login-tab" data-toggle="tab" href="#login" role="tab" aria-controls="login" aria-selected="true">Iniciar Sesión</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link stext-101 cl2" id="registro-tab" data-toggle="tab" href="#registro" role="tab" aria-controls="registro" aria-selected="false">Crear Cuenta</a>
                            </li>
                        </ul>

                        <div class="tab-content p-t-30" id="myTabContent">
                            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                                <form id="formLogin">
                                    <div class="m-b-20">
                                        <label class="stext-102 cl3">Usuario (Email)</label>
                                        <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="email" id="txtEmailLogin" name="txtEmailLogin">
                                    </div>
                                    <div class="m-b-20">
                                        <label class="stext-102 cl3">Contraseña</label>
                                        <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="password" id="txtPasswordLogin" name="txtPasswordLogin">
                                    </div>
                                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer" style="background: #333;">
                                        Ingresar
                                    </button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="registro" role="tabpanel" aria-labelledby="registro-tab">
                                <form id="formRegistro">
                                    <div class="row">
                                        <div class="col-md-6 m-b-20">
                                            <label class="stext-102 cl3">Nombres</label>
                                            <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="text" id="txtNombre" name="txtNombre" required>
                                        </div>
                                        <div class="col-md-6 m-b-20">
                                            <label class="stext-102 cl3">Apellidos</label>
                                            <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="text" id="txtApellido" name="txtApellido" required>
                                        </div>
                                        <div class="col-md-6 m-b-20">
                                            <label class="stext-102 cl3">Email</label>
                                            <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="email" id="txtEmail" name="txtEmail" required>
                                        </div>
                                        <div class="col-md-6 m-b-20">
                                            <label class="stext-102 cl3">Teléfono</label>
                                            <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="number" id="txtTelefonoRegistro" name="txtTelefonoRegistro" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer" style="background: #f3635a; border:none;">
                                        Registrarme y Continuar
                                    </button>
                                </form>
                            </div>
                        </div>

                    <?php } else {
                        $dirEntrega = isset($_SESSION['userData']['direccion']) ? $_SESSION['userData']['direccion'] : "";
                        $telefono   = isset($_SESSION['userData']['telefono'])  ? $_SESSION['userData']['telefono']  : "";
                    ?>
                        <h4 class="mtext-109 cl2 p-b-30" style="color: #f3635a;">
                            Detalles de Envío
                        </h4>
                        <div class="row">
                            <div class="col-md-12 m-b-20">
                                <label class="stext-102 cl3">¿Dónde te encuentras?</label>
                                <select class="stext-111 cl2 size-116 bor13 p-lr-20" id="tipoEnvio" name="tipoEnvio">
                                    <option value="1">Cartagena (Envío Local)</option>
                                    <option value="2">Otras Ciudades (Envío Nacional)</option>
                                </select>
                            </div>

                            <div id="sectionCartagena" class="col-md-12">
                                <div class="m-b-20">
                                    <label class="stext-102 cl3">Barrio en Cartagena</label>
                                    <select class="stext-111 cl2 size-116 bor13 p-lr-20" id="listBarrio" name="listBarrio" style="width: 100%;">
                                        <option value="">Seleccione un barrio...</option>
                                        <?php if (!empty($data['barrios'])) {
                                            foreach ($data['barrios'] as $barrio) { ?>
                                                <option value="<?= $barrio['idbarrio'] ?>" data-costo="<?= $barrio['costo'] ?>">
                                                    <?= $barrio['nombre'] ?>
                                                </option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <div id="sectionNacional" class="col-md-12" style="display: none;">
                                <div class="m-b-20">
                                    <label class="stext-102 cl3">Ciudad de Destino</label>
                                    <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="text" id="txtCiudadNacional" name="txtCiudadNacional" placeholder="Ej: Medellín, Bogotá, Montería...">
                                    <p class="stext-111 cl6 p-t-10" style="font-style: italic; color: #f3635a;">
                                        * El envío se realiza por <b>Coordinadora</b> y el valor del flete lo pagas al recibir el paquete.
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-12 m-b-20">
                                <label class="stext-102 cl3">Dirección de entrega</label>
                                <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="text" id="txtDireccion" name="txtDireccion" value="<?= $dirEntrega; ?>" placeholder="Ej: Calle 30 #50-10, Apto 201">
                            </div>

                            <div class="col-md-12 m-b-20">
                                <label class="stext-102 cl3">Teléfono de contacto</label>
                                <input class="stext-111 cl2 plh3 size-116 bor13 p-lr-20" type="text" id="txtTelefono" name="txtTelefono" value="<?= $telefono; ?>">
                            </div>

                            <input type="hidden" id="txtCostoEnvio" name="txtCostoEnvio" value="0">
                            <input type="hidden" id="txtCiudad" name="txtCiudad" value="Cartagena">
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="col-sm-10 col-lg-5 col-xl-5 m-lr-auto m-b-50">
                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-r-40 m-lr-0-xl bg11">
                    <h4 class="mtext-109 cl2 p-b-30">
                        Resumen de tu Compra
                    </h4>

                    <div class="p-b-30">
                        <?php
                        if (isset($_SESSION['arrCarrito']) && count($_SESSION['arrCarrito']) > 0) {
                            foreach ($_SESSION['arrCarrito'] as $producto) {
                                $idProducto = $producto['idproducto'].$producto['color'];
                        ?>
                                <div class="flex-w flex-t bor12 p-b-15 p-t-15">
                                    <div class="size-208 w-full-ssm" style="width: 70px;">
                                        <img src="<?= $producto['imagen']; ?>"
                                            alt="<?= $producto['producto']; ?>"
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;"
                                            onerror="this.src='<?= media(); ?>/images/uploads/default.png';">
                                    </div>

                                    <div class="size-209 p-l-15 w-full-ssm">
                                        <span class="stext-110 cl2" style="font-weight: 600; display: block; margin-bottom: 5px;">
                                            <?= $producto['producto']; ?>
                                        </span>

                                        <div class="flex-w flex-m">
                                            <div class="flex-w m-r-20" style="border: 1px solid #e6e6e6; border-radius: 25px; overflow: hidden; height: 32px; background: #fff;">
                                                <div class="btn-num-product-down cl8 hov-btn3 trans-04 flex-c-m"
                                                    style="cursor:pointer; width: 30px; height: 100%; border-right: 1px solid #e6e6e6;"
                                                    onclick="fntUpdateQty('<?= $idProducto; ?>','sub')">
                                                    <i class="fs-14 zmdi zmdi-minus"></i>
                                                </div>

                                                <input class="mtext-104 cl3 txt-center num-product" type="number"
                                                    name="num-product" value="<?= $producto['cantidad']; ?>"
                                                    style="width: 35px; height: 100%; border: none; background: transparent; font-size: 14px;" readonly>

                                                <div class="btn-num-product-up cl8 hov-btn3 trans-04 flex-c-m"
                                                    style="cursor:pointer; width: 30px; height: 100%; border-left: 1px solid #e6e6e6;"
                                                    onclick="fntUpdateQty('<?= $idProducto; ?>','add')">
                                                    <i class="fs-14 zmdi zmdi-plus"></i>
                                                </div>
                                            </div>

                                            <span class="stext-110 cl2 subtotal-<?= $idProducto; ?>" style="color: #666;">
                                                <?php
                                                // Si el precio original existe y es mayor al precio actual, hay oferta
                                                if (isset($producto['precio_original']) && $producto['precio_original'] > $producto['precio']) { ?>
                                                    <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                                                        $<?= formatMoneda($producto['precio_original'] * $producto['cantidad']); ?>
                                                    </span>
                                                    <span style="color: #f3635a; font-weight: bold;">
                                                        $<?= formatMoneda($producto['precio'] * $producto['cantidad']); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    $<?= formatMoneda($producto['precio'] * $producto['cantidad']); ?>
                                                <?php } ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>

                    <div class="flex-w flex-m m-b-20 p-t-10 bor12 p-b-20">
                        <div class="bor13 m-r-10 flex-grow-1" style="flex: 1; position: relative;">
                            <input class="stext-111 cl2 plh3 size-111 p-lr-20" type="text" id="txtCupon" name="txtCupon" placeholder="¿Tienes un cupón?">
                        </div>

                        <button id="btnAplicarCupon" class="flex-c-m stext-101 cl0 bg3 bor1 hov-btn3 trans-04"
                            style="width: 0; opacity: 0; overflow: hidden; padding: 0; white-space: nowrap; transition: all 0.3s ease;">
                            Aplicar
                        </button>
                    </div>
                    <div id="msgCupon" class="stext-111 p-b-10" style="display:none; font-size: 12px;"></div>

                    <div class="flex-w flex-t bor12 p-b-13">
                        <div class="size-208">
                            <span class="stext-110 cl2">Subtotal:</span>
                        </div>
                        <div class="size-209">
                            <!-- El ID subtotalCarrito es la clave aquí -->
                            <span id="subtotalCarrito" class="mtext-110 cl2">
                                $<?= formatMoneda($data['total']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-t-15 p-b-30">
                        <div class="size-208 w-full-ssm">
                            <span class="stext-110 cl2">Envío:</span>
                        </div>
                        <div class="size-209 p-r-18 p-r-0-sm w-full-ssm">
                            <p id="displayCostoEnvio" class="stext-111 cl6 p-t-2">$ 0</p>
                        </div>
                    </div>

                    <div class="flex-w flex-t p-t-27 p-b-33">
                        <div class="size-208">
                            <span class="mtext-101 cl2">Total:</span>
                        </div>
                        <div class="size-209 p-t-1">
                            <!-- Cambié el ID a totalFinalCompra para diferenciarlo de subtotales -->
                            <span id="totalFinalCompra" class="mtext-110 cl2" style="color: #f3635a; font-weight: bold; font-size: 22px;">
                                $<?= formatMoneda($data['total']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- AQUÍ EMPIEZA IGUAL -->

                    <hr>
                    <form id="formConfirmarPedido">
                        <input type="hidden" id="intTipopago" name="intTipopago" value="1">

                        <div class="payment-methods">

                            <!-- <div class="method-item">
                                <input type="radio" name="payment-method" id="method-wompi" value="1" checked
                                    onclick="document.querySelector('#intTipopago').value = 1;">
                                <label for="method-wompi" class="method-label">
                                    <div class="method-info">
                                        <span class="method-title">Tarjeta / PSE / Nequi</span>
                                        <span class="method-desc">Pago seguro con Wompi</span>
                                    </div>
                                    <div class="method-icons">
                                        <img src="<?= media(); ?>/images/nequi_logo.png">
                                        <img src="<?= media(); ?>/images/pse_logo.png">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg">
                                    </div>
                                </label>
                            </div> -->

                            <div class="method-item">
                                <input type="radio" name="payment-method" id="method-bank" value="2"
                                    onclick="document.querySelector('#intTipopago').value = 2;">
                                <label for="method-bank" class="method-label">
                                    <div class="method-info">
                                        <span class="method-title">Transferencia Directa</span>
                                        <span class="method-desc">Bancolombia, Nequi o Daviplata</span>
                                    </div>
                                </label>

                                <div id="bank-details" class="bank-info-box m-t-10" style="display: none; text-align: center;">
                                    <p class="stext-111">Escanea el código QR para pagar y envía el soporte por WhatsApp:</p>
                                    <div class="qr-container m-t-15 m-b-15">
                                        <img src="<?= media(); ?>/images/qr-pago-sayana.jpeg"
                                            alt="Código QR de Pago"
                                            style="width: 200px; height: 200px; border: 1px solid #e6e6e6; padding: 10px; border-radius: 8px;">
                                    </div>
                                    <ul class="m-t-5" style="list-style: none; padding: 0;">
                                        <li><strong class="cl2">Nequi / Bancolombia</strong></li>
                                        <li class="stext-111">Titular: Sayana Luxury</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="method-item">
                                <input type="radio" name="payment-method" id="method-delivery" value="3"
                                    onclick="document.querySelector('#intTipopago').value = 3;">
                                <label for="method-delivery" class="method-label">
                                    <div class="method-info">
                                        <span class="method-title">Pago Contra Entrega</span>
                                        <span class="method-desc">Paga al recibir</span>
                                    </div>
                                </label>
                            </div>

                        </div>

                        <button id="btnFinalizarPedido" type="submit"
                            class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04 pointer"
                            style="background: linear-gradient(to right, #f3635a, #f8a5a0); border:none;">
                            <span id="btnText">Confirmar y Pagar</span>
                            <span id="btnLoader" style="display:none; margin-left: 10px;">
                                <i class="fa fa-spinner fa-spin"></i> Procesando...
                            </span>
                        </button>
                    </form>



                    <div class="p-t-20 text-center">
                        <div class="p-t-20 text-center">
                            <div class="flex-c-m flex-w p-b-10" style="font-size: 25px; color: #888; gap: 15px;">
                                <i class="fa fa-cc-visa"></i>
                                <i class="fa fa-cc-mastercard"></i>
                                <i class="fa fa-cc-amex"></i>
                                <i class="fa fa-university"></i> <i class="fas fa-shield-alt"></i>
                            </div>
                            <span class="stext-111 cl6">Pago 100% Seguro y Encriptado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php footerTienda($data); ?>