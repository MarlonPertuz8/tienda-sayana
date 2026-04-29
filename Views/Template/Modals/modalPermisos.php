<div class="modal fade modalPermisos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered"> <div class="modal-content shadow-lg" style="border: none; border-radius: 12px; overflow: hidden;">
        <div class="modal-header" style="background-color: #0b2a3d; color: white; border: none;">
            <h5 class="modal-title" style="font-weight: 600; font-size: 1.1rem;">Configuración de Accesos</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
              <span aria-hidden="true" style="color: white; opacity: 0.8;">&times;</span>
            </button>
        </div>
        <div class="modal-body" style="padding: 0; background-color: #ffffff;">
            <form id="formPermisos" name="formPermisos">
                <input type="hidden" id="idrol" name="idrol" value="<?= $data['idrol']; ?>">
                
                <div class="permisos-wrapper">
                    <div class="permisos-header-row">
                        <div class="p-col-mod">Módulo</div>
                        <div class="p-col-action">Ver</div>
                        <div class="p-col-action">Crear</div>
                        <div class="p-col-action">Act.</div>
                        <div class="p-col-action">Del.</div>
                    </div>

                    <div class="permisos-body">
                        <?php 
                            $modulos = $data['modulos'];
                            for ($i=0; $i < count($modulos); $i++) { 
                                $permisos = $modulos[$i]['permisos'];
                                $idmod = $modulos[$i]['idmodulo'];
                        ?>
                        <div class="permisos-row">
                            <div class="p-col-mod">
                                <span class="mod-name"><?= $modulos[$i]['titulo']; ?></span>
                                <input type="hidden" name="modulos[<?= $i; ?>][idmodulo]" value="<?= $idmod ?>">
                            </div>
                            <div class="p-col-action">
                                <label class="p-switch">
                                    <input type="checkbox" name="modulos[<?= $i; ?>][r]" <?= $permisos['r'] == 1 ? "checked" : "" ?>>
                                    <span class="p-slider"></span>
                                </label>
                            </div>
                            <div class="p-col-action">
                                <label class="p-switch">
                                    <input type="checkbox" name="modulos[<?= $i; ?>][w]" <?= $permisos['w'] == 1 ? "checked" : "" ?>>
                                    <span class="p-slider"></span>
                                </label>
                            </div>
                            <div class="p-col-action">
                                <label class="p-switch">
                                    <input type="checkbox" name="modulos[<?= $i; ?>][u]" <?= $permisos['u'] == 1 ? "checked" : "" ?>>
                                    <span class="p-slider"></span>
                                </label>
                            </div>
                            <div class="p-col-action">
                                <label class="p-switch">
                                    <input type="checkbox" name="modulos[<?= $i; ?>][d]" <?= $permisos['d'] == 1 ? "checked" : "" ?>>
                                    <span class="p-slider"></span>
                                </label>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="modal-footer-custom">
                    <button class="btn-save-permisos" type="submit">
                        <i class="fas fa-save"></i> Actualizar Permisos
                    </button>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>