<?php 
headerTienda($data); 
$campana = $data['campana'];
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>

:root{
    --dark:#0f172a;
    --primary:#274e66;
    --light:#ffffff;
    --gray:#f8fafc;
    --text:#475569;
}

body{
    overflow-x:hidden;
}

.main-landing{
    font-family:'Inter',sans-serif;
    background:var(--gray);
}

/* HERO */

.hero{
    min-height:100vh;
    position:relative;
    display:flex;
    align-items:center;
    background:
    linear-gradient(to right, rgba(15,23,42,.92), rgba(15,23,42,.55)),
    url('<?= $campana["url_banner"] ?>');
    background-size:cover;
    background-position:center;
    overflow:hidden;
}

.hero::after{
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(to top, #f8fafc 0%, transparent 20%);
}

.hero-content{
    position:relative;
    z-index:2;
    color:white;
    max-width:700px;
}

.hero-subtitle{
    font-size:14px;
    letter-spacing:4px;
    text-transform:uppercase;
    opacity:.7;
    margin-bottom:25px;
}

.hero-title{
    font-size:clamp(3rem,8vw,6.5rem);
    font-weight:900;
    line-height:.95;
    margin-bottom:30px;
}

.hero-text{
    font-size:1.15rem;
    line-height:1.9;
    opacity:.9;
    margin-bottom:40px;
}

.hero-actions{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
}

.btn-primary-custom{
    background:white;
    color:#111;
    padding:18px 38px;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
    transition:.3s;
}

.btn-primary-custom:hover{
    transform:translateY(-4px);
    text-decoration:none;
    color:#111;
}

.btn-outline-custom{
    border:1px solid rgba(255,255,255,.25);
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(10px);
    color:white;
    padding:18px 38px;
    border-radius:14px;
    text-decoration:none;
    transition:.3s;
}

.btn-outline-custom:hover{
    background:white;
    color:#111;
    text-decoration:none;
}

/* PRODUCT SHOWCASE */

.showcase{
    margin-top:-40px;
    position:relative;
    z-index:5;
}

.showcase-card{
    background:white;
    border-radius:30px;
    overflow:hidden;
    box-shadow:0 25px 60px rgba(0,0,0,.08);
    transition:.4s;
    height:100%;
}

.showcase-card:hover{
    transform:translateY(-10px);
}

.showcase-image{
    height:300px;
    overflow:hidden;
}

.showcase-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.6s;
}

.showcase-card:hover img{
    transform:scale(1.05);
}

.showcase-body{
    padding:35px;
}

.showcase-body h4{
    font-weight:800;
    margin-bottom:15px;
    color:var(--dark);
}

.showcase-body p{
    color:var(--text);
    line-height:1.8;
}

/* CONTENT */

.content-section{
    padding:120px 0;
}

.content-wrapper{
    background:white;
    border-radius:40px;
    padding:80px;
    box-shadow:0 20px 50px rgba(0,0,0,.05);
}

.content-label{
    font-size:14px;
    text-transform:uppercase;
    letter-spacing:4px;
    color:var(--primary);
    margin-bottom:20px;
    font-weight:700;
}

.content-title{
    font-size:3.5rem;
    font-weight:900;
    color:var(--dark);
    margin-bottom:40px;
    line-height:1.1;
}

.landing-content{
    font-size:1.1rem;
    color:var(--text);
    line-height:2;
}

.landing-content img{
    width:100%;
    border-radius:25px;
    margin:45px 0;
}

/* CTA */

.bottom-cta{
    padding:100px 20px;
}

.cta-box{
    background:linear-gradient(135deg, #274e66, #0f172a);
    border-radius:40px;
    padding:80px;
    text-align:center;
    color:white;
    position:relative;
    overflow:hidden;
}

.cta-box::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    background:rgba(255,255,255,.05);
    border-radius:50%;
    top:-250px;
    right:-100px;
}

.cta-box h2{
    font-size:4rem;
    font-weight:900;
    margin-bottom:25px;
    position:relative;
    z-index:2;
}

.cta-box p{
    max-width:700px;
    margin:auto;
    line-height:1.9;
    opacity:.9;
    margin-bottom:40px;
    position:relative;
    z-index:2;
}

.cta-box a{
    position:relative;
    z-index:2;
}
/* PREMIUM CARDS */

.premium-card{
    position:relative;
    padding:45px 35px;
    border-radius:35px;
    overflow:hidden;
    background:white;
    border:1px solid rgba(242,127,119,.15);
    transition:.45s ease;
    box-shadow:0 20px 50px rgba(0,0,0,.06);
}

.premium-card::before{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    background:rgba(242,127,119,.08);
    border-radius:50%;
    top:-80px;
    right:-60px;
    transition:.5s;
}

.premium-card:hover{
    transform:translateY(-12px);
    box-shadow:0 35px 70px rgba(242,127,119,.18);
}

.premium-card:hover::before{
    transform:scale(1.2);
}

.premium-icon{
    width:85px;
    height:85px;
    border-radius:28px;
    background:linear-gradient(135deg,#ff9a92,#f27f77);
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:30px;
    position:relative;
    z-index:2;
    box-shadow:0 15px 35px rgba(242,127,119,.25);
}

.premium-icon i{
    font-size:2rem;
    color:white;
}

.premium-number{
    position:absolute;
    top:30px;
    right:30px;
    font-size:4rem;
    font-weight:900;
    color:rgba(242,127,119,.08);
    line-height:1;
}

.premium-card h4{
    font-size:1.6rem;
    font-weight:800;
    margin-bottom:18px;
    color:#2b2b2b;
    position:relative;
    z-index:2;
}

.premium-card p{
    font-size:1rem;
    line-height:1.9;
    color:#6b6b6b;
    position:relative;
    z-index:2;
}
/* RESPONSIVE */

@media(max-width:991px){

    .hero{
        min-height:auto;
        padding:120px 0;
    }

    .content-wrapper{
        padding:35px;
    }

    .content-title{
        font-size:2.2rem;
    }

    .cta-box{
        padding:50px 30px;
    }

    .cta-box h2{
        font-size:2.5rem;
    }

}

</style>

<main class="main-landing">

    <!-- HERO -->
    <section class="hero">

        <div class="container">

            <div class="hero-content">

                <div class="hero-subtitle">
                    NUEVA EXPERIENCIA
                </div>

                <h1 class="hero-title">
                    <?= $campana['nombre'] ?>
                </h1>

                <?php if(!empty($campana['descripcion_corta'])): ?>
                    <p class="hero-text">
                        <?= $campana['descripcion_corta'] ?>
                    </p>
                <?php endif; ?>

                <div class="hero-actions">

                    <?php if(!empty($campana['enlace_boton'])): ?>
                        <a href="<?= $campana['enlace_boton'] ?>" class="btn-primary-custom">
                            Comprar ahora
                        </a>
                    <?php endif; ?>

                    <a href="#informacion" class="btn-outline-custom">
                        Explorar
                    </a>

                </div>

            </div>

        </div>

    </section>

    <!-- SHOWCASE -->
<!-- SHOWCASE -->
<section class="showcase">

    <div class="container">

        <div class="row">

            <!-- CARD 1 -->
            <div class="col-lg-4 mb-4">

                <div class="showcase-card premium-card">

                    <div class="premium-icon">
                        <i class="fas fa-gem"></i>
                    </div>

                    <div class="premium-number">
                        01
                    </div>

                    <div class="showcase-body">

                        <h4>Diseño Exclusivo</h4>

                        <p>
                            Productos cuidadosamente seleccionados para destacar tu estilo con elegancia y personalidad.
                        </p>

                    </div>

                </div>

            </div>

            <!-- CARD 2 -->
            <div class="col-lg-4 mb-4">

                <div class="showcase-card premium-card">

                    <div class="premium-icon">
                        <i class="fas fa-spa"></i>
                    </div>

                    <div class="premium-number">
                        02
                    </div>

                    <div class="showcase-body">

                        <h4>Belleza y Estilo</h4>

                        <p>
                            Una experiencia visual moderna inspirada en tendencias premium y lujo femenino.
                        </p>

                    </div>

                </div>

            </div>

            <!-- CARD 3 -->
            <div class="col-lg-4 mb-4">

                <div class="showcase-card premium-card">

                    <div class="premium-icon">
                        <i class="fas fa-heart"></i>
                    </div>

                    <div class="premium-number">
                        03
                    </div>

                    <div class="showcase-body">

                        <h4>Colecciones Especiales</h4>

                        <p>
                            Descubre accesorios y productos únicos pensados para hacerte sentir especial.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

    <!-- CONTENT -->
    <section class="content-section" id="informacion">

        <div class="container">

            <div class="content-wrapper">

                <div class="content-label">
                    INFORMACIÓN
                </div>

                <h2 class="content-title">
                    Descubre todos los detalles
                </h2>

                <div class="landing-content">
                    <?= $campana['html_contenido'] ?>
                </div>

            </div>

        </div>

    </section>

    <!-- CTA -->
    <section class="bottom-cta">

        <div class="container">

            <div class="cta-box">

                <h2>
                    Lleva tu experiencia al siguiente nivel
                </h2>

                <p>
                    Explora esta campaña y descubre una forma más moderna, elegante y atractiva de comprar online.
                </p>

                <?php if(!empty($campana['enlace_boton'])): ?>
                    <a href="<?= $campana['enlace_boton'] ?>" class="btn-primary-custom">
                        Ir a comprar
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<?php footerTienda($data); ?>