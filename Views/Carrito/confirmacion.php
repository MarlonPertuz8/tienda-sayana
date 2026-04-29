<?php headerTienda($data); ?>

<style>
    /* 1. Empujamos todo hacia abajo para que el header no lo tape */
    .main-confirm-wrapper {
        padding-top: 150px; /* Ajusta este valor según el alto de tu header */
        padding-bottom: 80px;
        background-color: #fff;
    }

    .confirm-container {
        max-width: 750px;
        margin: 0 auto;
        padding: 50px;
        background-color: #fff;
        /* Sombra muy sutil para estilo Luxury */
        box-shadow: 0 15px 40px rgba(0,0,0,0.05);
        border: 1px solid #f1f1f1;
    }

    /* 2. Caja de orden más elegante */
    .order-box {
        background-color: #fcfcfc;
        border: 1px solid #eeeeee;
        padding: 40px;
        margin: 40px 0;
    }

    .order-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #999;
        letter-spacing: 3px;
        display: block;
        margin-bottom: 15px;
    }

    .order-number {
        font-size: 38px;
        font-weight: 700;
        letter-spacing: 5px;
        color: #1d1d1d;
        margin: 0;
    }

    /* 3. Botón de WhatsApp redondeado como pediste */
    .btn-wpp-luxury {
        background-color: #25D366; 
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 18px 45px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 50px !important; /* Totalmente redondeado */
        width: 100%;
        max-width: 400px;
        border: none;
    }

    .btn-wpp-luxury:hover {
        background-color: #1eb956;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(37, 211, 102, 0.2);
    }

    /* 4. Enlaces inferiores minimalistas */
    .nav-links-footer {
        margin-top: 50px;
    }

    .link-nav-luxury {
        color: #1d1d1d;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0 20px;
        transition: opacity 0.3s;
    }

    .link-nav-luxury:hover {
        color:#f3635a;
        opacity: 0.6;
    }

    .check-icon-luxury {
        color: #25D366;
        font-size: 60px;
        margin-bottom: 25px;
    }
</style>

<div class="main-confirm-wrapper">
    <div class="container">
        <div class="confirm-container text-center">
            
            <div class="check-icon-luxury">
                <i class="fa fa-check-circle"></i>
            </div>

            <h1 style="font-weight: 900; letter-spacing: -1px; font-size: 40px; margin-bottom: 10px;">¡PEDIDO RECIBIDO!</h1>
            <p style="color: #777; font-size: 16px;">Gracias por elegir la exclusividad de <strong>Sayana Luxury</strong>.</p>
            
            <div class="order-box">
                <span class="order-label">Número de seguimiento</span>
                <h2 class="order-number">ORDEN #<?= $data['order']; ?></h2>
            </div>

            <p style="font-size: 15px; color: #555; line-height: 1.8; max-width: 500px; margin: 0 auto 40px;">
                Para procesar el envío de tu pedido lo antes posible, por favor envíanos el 
                <strong>comprobante de tu transferencia</strong> a través de nuestro canal de atención.
            </p>

            <?php 
                $telefonoTienda = "573000000000"; 
                $nombre = !empty($_SESSION['userData']['nombre']) ? $_SESSION['userData']['nombre'] : "Cliente";
                $apellido = !empty($_SESSION['userData']['apellido']) ? $_SESSION['userData']['apellido'] : "";
                $cliente = trim($nombre . " " . $apellido);
                $mensaje = "Hola, mi nombre es " . $cliente . ". Acabo de realizar el pedido #" . $data['order'] . " en Sayana Luxury y aquí adjunto mi comprobante.";
                $urlWpp = "https://wa.me/" . $telefonoTienda . "?text=" . urlencode($mensaje);
            ?>
            
            <a href="<?= $urlWpp; ?>" target="_blank" class="btn-wpp-luxury">
                <i class="fa fa-whatsapp" style="margin-right: 15px; font-size: 24px;"></i> Enviar Comprobante
            </a>

            <div class="nav-links-footer">
                <a href="<?= base_url(); ?>/tienda" class="link-nav-luxury">Tienda</a>
                <span style="color: #eee;">|</span>
                <a href="<?= base_url(); ?>/pedidos" class="link-nav-luxury">Mis Pedidos</a>
            </div>
        </div>
    </div>
</div>

<?php footerTienda($data); ?>