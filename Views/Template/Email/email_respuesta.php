<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Respuesta a tu consulta - <?= NOMBRE_EMPRESA ?></title>

<style>
    body{ margin:0; padding:0; background-color:#f4f7f9; font-family: 'Segoe UI', Arial, sans-serif; }
    .wrapper{ width:100%; padding:40px 15px; background-color:#f4f7f9; }
    .container{ max-width:540px; margin:auto; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.05); }
    .header{ background:#0f172a; padding:40px 20px; text-align:center; }
    .header img{ max-width:150px; margin-bottom:15px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
    .header h1{ color:#ffffff; font-size:22px; margin:0; font-weight:700; text-transform: uppercase; letter-spacing: 1.5px; }
    
    .body{ padding:40px 35px; color:#334155; font-size:16px; line-height:1.6; }
    .body h2{ color:#0f172a; font-size:24px; margin-top:0; text-align: center; }
    .welcome-text { text-align: center; color: #64748b; margin-bottom: 30px; }
    
    .response-box {
        background: #f8fafc;
        border: 1px dashed #2563eb;
        border-radius: 12px;
        padding: 25px;
        margin: 25px 0;
        font-style: italic;
        color: #1e293b;
    }

    .button {
        display: block;
        width: 100%;
        max-width: 260px;
        margin: 30px auto;
        background: #2563eb;
        color: #ffffff !important;
        text-decoration: none;
        text-align: center;
        padding: 16px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
    }
    
    .footer { background:#f1f5f9; padding:25px; text-align:center; font-size:13px; color:#64748b; }
    .footer a { color:#2563eb; text-decoration:none; font-weight:600; }
    .social-notice { font-size: 12px; margin-top: 10px; color: #94a3b8; }
</style>
</head>

<body>
<div class="wrapper">
    <div class="container">

        <div class="header">
            <img src="<?= media(); ?>/images/logoSayana.png" alt="Logo Sayana">
            <h1><?= NOMBRE_EMPRESA ?></h1>
        </div>

        <div class="body">
            <h2>Hola, tenemos noticias</h2>
            <p class="welcome-text">Hemos revisado tu mensaje en <strong><?= NOMBRE_EMPRESA ?></strong> y queremos darte una respuesta.</p>

            <p>Respecto a tu consulta, nuestro equipo te informa lo siguiente:</p>

            <div class="response-box">
                "<?= $data['respuesta']; ?>"
            </div>

            <p style="text-align: center;">Si necesitas algo más, puedes seguir explorando nuestras colecciones:</p>

            <a href="<?= base_url(); ?>" target="_blank" class="button">
                Ver Colección Sayana
            </a>

            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">

            <p style="font-size: 14px;">
                Gracias por contactarnos. Siempre es un gusto atenderte. <br>
                <strong>Equipo <?= NOMBRE_EMPRESA ?></strong>
            </p>
        </div>

        <div class="footer">
            <strong>© <?= date("Y"); ?> <?= NOMBRE_EMPRESA ?></strong><br>
            Visítanos en: <a href="<?= WEB_EMPRESA; ?>" target="_blank"><?= WEB_EMPRESA; ?></a>
            <p class="social-notice">Este correo ha sido enviado para dar seguimiento a tu solicitud de contacto.</p>
        </div>

    </div>
</div>
</body>
</html>