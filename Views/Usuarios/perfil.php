<?php 
  headerAdmin($data);
  getModal('modalPerfil', $data);
?>
<main class="app-content">

  <div class="row user">

    <!-- ===== PERFIL ===== -->
    <div class="col-md-12">
      <div class="profile">
        <div class="info">
          <div class="avatar-wrapper" onclick="document.getElementById('fileAvatar').click();">
            <img class="user-img" id="avatarPreview" src="<?= media(); ?>/images/avatar.png">
            <div class="avatar-overlay">
                <i class="fas fa-camera"></i>
            </div>
            <input type="file" id="fileAvatar" hidden accept="image/*">
        </div>

          <div class="user-text">
            <h4><?= $_SESSION['userData']['nombre'].' '.$_SESSION['userData']['apellido']; ?></h4>
            <p><?= $_SESSION['userData']['nombrerol']; ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== TABS ===== -->
    <div class="col-md-3">
      <div class="tile p-0">
        <ul class="nav flex-column nav-tabs user-tabs">
          <li class="nav-item">
            <a class="nav-link active" href="#user-timeline" data-toggle="tab">
              <i class="fas fa-user"></i> Datos Personales
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#user-settings" data-toggle="tab">
              <i class="fas fa-file-invoice"></i> Informacion Tributaria
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- ===== CONTENIDO ===== -->
    <div class="col-md-9">
      <div class="tab-content">

        <!-- ===== DATOS PERSONALES ===== -->
        <div class="tab-pane fade show active" id="user-timeline">

          <div class="content-header">
            <h5>
              Datos Personales
              <button class="btn btn-sm btn-primary" onclick="openModalPerfil();">
                <i class="fas fa-pencil-alt"></i>
              </button>
            </h5>
          </div>

          <table class="table table-bordered mt-3">
            <tbody>
              <tr>
                <td>Identificación</td>
                <td><?= $_SESSION['userData']['identificacion']; ?></td>
              </tr>
              <tr>
                <td>Nombres</td>
                <td><?= $_SESSION['userData']['nombre']; ?></td>
              </tr>
              <tr>
                <td>Apellidos</td>
                <td><?= $_SESSION['userData']['apellido']; ?></td>
              </tr>
              <tr>
                <td>Teléfono</td>
                <td><?= $_SESSION['userData']['telefono']; ?></td>
              </tr>
              <tr>
                <td>Email</td>
                <td><?= $_SESSION['userData']['email_user']; ?></td>
              </tr>
            </tbody>
          </table>

        </div>

        <!-- ===== DATOS FISCALES ===== -->
        <div class="tab-pane fade" id="user-settings">

          <div class="tile user-settings">
            <h4 class="line-head">Informacion</h4>

            <form id="formDataFiscal">

              <div class="row mb-4">
                <div class="col-md-6">
                  <label>NIT / Cédula</label>
                  <input class="form-control" type="text" id="txtNit" name="txtNit"
                    value="<?= $_SESSION['userData']['nit']; ?>">
                </div>

                <div class="col-md-6">
                  <label>Razón Social / Nombre</label>
                  <input class="form-control" type="text" id="txtNombreFiscal" name="txtNombreFiscal"
                    value="<?= $_SESSION['userData']['nombrefiscal']; ?>">
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 mb-4">
                  <label>Dirección (DIAN)</label>
                  <input class="form-control" type="text" id="txtDirFiscal" name="txtDirFiscal"
                    value="<?= $_SESSION['userData']['direccionfiscal']; ?>">
                </div>
              </div>

              <button class="btn btn-primary" type="submit">
                <i class="fa fa-check-circle"></i> Guardar cambios
              </button>

            </form>
          </div>

        </div>

      </div>
    </div>

  </div>

</main>
<?php footerAdmin($data); ?>