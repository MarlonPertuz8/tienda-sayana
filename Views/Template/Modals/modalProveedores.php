<div class="modal fade" id="modalFormProveedor" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header headerRegister" style="background: #009688; color: white;">
        <h5 class="modal-title" id="titleModal">Nuevo Proveedor</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formProveedor" name="formProveedor" class="form-horizontal">
        <input type="hidden" id="idProveedor" name="idProveedor" value="">
        <div class="modal-body p-4">
          <p class="text-muted">Todos los campos con asterisco (<span class="text-danger">*</span>) son obligatorios.</p>
          
          <div class="form-group">
            <label class="control-label font-weight-bold">Nombre de la Empresa <span class="text-danger">*</span></label>
            <input class="form-control" id="txtNombre" name="txtNombre" type="text" placeholder="Nombre del proveedor" required="" style="border-radius: 10px;">
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">NIT</label>
                <input class="form-control" id="txtNit" name="txtNit" type="text" placeholder="Identificación fiscal" style="border-radius: 10px;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Teléfono</label>
                <input class="form-control" id="txtTelefono" name="txtTelefono" type="text" placeholder="Contacto" style="border-radius: 10px;">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label font-weight-bold">Dirección</label>
            <textarea class="form-control" id="txtDireccion" name="txtDireccion" rows="2" placeholder="Ubicación de la empresa" style="border-radius: 10px;"></textarea>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button id="btnActionForm" class="btn btn-primary shadow-sm" type="submit" style="border-radius: 25px; padding: 10px 25px;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span>
          </button>
          <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 10px 25px;">
            <i class="fa fa-fw fa-lg fa-times-circle"></i>Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>