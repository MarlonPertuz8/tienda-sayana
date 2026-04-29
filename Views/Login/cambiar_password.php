<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="Abel OSH">
  <meta name="theme-color" content="#274e66">

  <link rel="shortcut icon" href="<?= media();?>/images/favicon.ico">

  <!-- Main CSS -->
  <link rel="stylesheet" type="text/css" href="<?= media();?>/css/main.css">
  <link rel="stylesheet" type="text/css" href="<?= media();?>/css/style.css">

  <title><?= $data['page_tag']; ?></title>
</head>

<body class="page-resetpass">

  <section class="material-half-bg">
    <div class="cover"></div>
  </section>

  <section class="login-content">

    <!-- ✅ LOGO ARRIBA - FLOTANTE -->
    <div class="login-logo">
      <img src="<?= media(); ?>/images/lo.png" alt="Logo">
    </div>

    <div class="logo">
      <h1><?= $data['page_title']; ?></h1>
    </div>

    <div class="login-box flipped">

      <!-- Loader (opcional) -->
      <div id="divLoading">
        <div>
          <img src="<?= media(); ?>/images/spinner-double.svg" alt="Loading">
        </div>
      </div>

      <!-- Formulario -->
      <form id="formCambiarPass" name="formCambiarPass" class="forget-form" action="">

        <input type="hidden" id="idUsuario" name="idUsuario" value="<?= $data['idpersona']; ?>" required>
        <input type="hidden" id="txtEmail" name="txtEmail" value="<?= $data['email'] ?? ''; ?>">
        <input type="hidden" id="txtToken" name="txtToken" value="<?= $data['token'] ?? ''; ?>">

        <h3 class="login-head">
          <i class="fas fa-key"></i> Cambiar contraseña
        </h3>

        <div class="form-group">
          <input id="txtPassword" name="txtPassword"
                 class="form-control"
                 type="password"
                 placeholder="Nueva contraseña"
                 required>
        </div>

        <div class="form-group">
          <input id="txtPasswordConfirm" name="txtPasswordConfirm"
                 class="form-control"
                 type="password"
                 placeholder="Confirmar contraseña"
                 required>
        </div>

        <div class="form-group btn-container">
          <button type="submit" class="btn btn-primary btn-block">
            <i class="fa fa-unlock fa-lg fa-fw"></i> REINICIAR
          </button>
        </div>

      </form>

    </div>
  </section>

  <!-- Base URL -->
  <script>
    const base_url = "<?= base_url(); ?>";
  </script>

  <!-- JS -->
  <script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
  <script src="<?= media(); ?>/js/popper.min.js"></script>
  <script src="<?= media(); ?>/js/bootstrap.min.js"></script>
  <script src="<?= media(); ?>/js/fontawesome.js"></script>
  <script src="<?= media(); ?>/js/main.js"></script>
  <script src="<?= media(); ?>/js/plugins/pace.min.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Funciones -->
  <script src="<?= media(); ?>/js/<?= $data['page_functions_js']; ?>"></script>

</body>
</html>
