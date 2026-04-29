<?php 
  headerAdmin($data); 
  getModal('modalNosotros', $data);
?>
<main class="app-content">
  <div class="app-title">
    <div>
      <h1>
        <i class="fa fa-info-circle" style="color: #c9a050;"></i> <?= $data['page_title']; ?>
        <?php if($_SESSION['permisos'][10]['u']){ ?>
          <button class="btn btn-primary" type="button" onclick="fntEditNosotros();">
            <i class="fas fa-pencil-alt"></i> Editar Sección
          </button>
        <?php } ?>
      </h1>
      <p>Gestión de la identidad y marca de Sayana Luxury</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>/AdminNosotros">Nosotros</a></li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
          <div class="row">
            <div class="col-md-8 border-right">
              <h3 class="mb-3 text-primary" id="lblTitulo"><?= $data['info']['titulo']; ?></h3>
              <div id="lblContenido" class="text-justify" style="font-size: 16px; line-height: 1.8; color: #333;">
                <?= $data['info']['contenido']; ?>
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="text-center mb-4">
                <h5 class="text-muted">Portada Banner</h5>
                <?php $portada = (!empty($data['info']['portada'])) ? media().'/images/uploads/'.$data['info']['portada'] : media().'/images/uploads/default.jpg'; ?>
                <img id="viewPortada" src="<?= $portada; ?>" class="img-fluid rounded shadow" style="width: 100%; max-height: 180px; object-fit: cover; border: 2px solid #eee;">
              </div>
              
              <div class="text-center">
                <h5 class="text-muted">Imagen Secundaria</h5>
                <?php $secundaria = (!empty($data['info']['imagen_secundaria'])) ? media().'/images/uploads/'.$data['info']['imagen_secundaria'] : media().'/images/uploads/default.jpg'; ?>
                <img id="viewSecundaria" src="<?= $secundaria; ?>" class="img-fluid rounded shadow" style="width: 100%; max-height: 180px; object-fit: cover; border: 2px solid #eee;">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php footerAdmin($data); ?>