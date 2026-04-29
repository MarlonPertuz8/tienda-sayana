<?php headerTienda($data); ?>

<section class="sayana-contacto-v6">
    
    <div class="contacto-header-v6">
        <h1 class="contacto-title-v6">CONTACTO</h1>
        <div class="header-line-sayana">
            <span class="diamond-sep"></span>
        </div>
        <p class="contacto-subtitle-v6">
            Estamos aquí para brindarte una experiencia excepcional
        </p>
    </div>

    <div class="contacto-cards-grid">
        <div class="card-luxury-item">
            <div class="card-icon-wrap"><i class="zmdi zmdi-pin"></i></div>
            <h4>Ubicación</h4>
            <p>Cartagena 123, Bolívar, Colombia</p>
        </div>

        <div class="card-luxury-item">
            <div class="card-icon-wrap"><i class="zmdi zmdi-whatsapp"></i></div>
            <h4>WhatsApp</h4>
            <p>+57 302 307 5957</p>
        </div>

        <div class="card-luxury-item">
            <div class="card-icon-wrap"><i class="zmdi zmdi-email"></i></div>
            <h4>Email</h4>
            <p>info@sayana.col</p>
        </div>
    </div>

    <div class="contacto-main-content">

        <!-- FORM -->
        <div class="form-container-v6">
            <form id="frmContacto" class="form-elite">

                <div class="input-luxury-v6">
                    <input type="text" name="nombreContacto" required>
                    <label>Nombre completo</label>
                    <span class="border-animate"></span>
                </div>

                <div class="input-luxury-v6">
                    <input type="email" name="emailContacto" required>
                    <label>Correo electrónico</label>
                    <span class="border-animate"></span>
                </div>

                <div class="input-luxury-v6">
                    <textarea name="mensaje" rows="4" required></textarea>
                    <label>¿En qué podemos ayudarte?</label>
                    <span class="border-animate"></span>
                </div>

                <button type="submit" class="btn-sayana-elite">
                    ENVIAR MENSAJE
                </button>

            </form>
        </div>

        <!-- MAPA -->
        <div class="map-container-v6">
            <div class="map-rounded">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18..."
                    loading="lazy">
                </iframe>
            </div>
        </div>

    </div>

</section>

<?php footerTienda($data); ?>