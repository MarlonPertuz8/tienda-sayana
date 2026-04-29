<div class="modal fade modal-pro" id="modalFormCupon" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
      
      <div class="modal-header headerRegister" style="background: #009688; color: white; border: 0;">
        <h5 class="modal-title" id="titleModal">
          <i class="fas fa-ticket-alt mr-2"></i> Nuevo Cupón
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-4 bg-light">
        <form id="formCupon" name="formCupon" class="form-horizontal">
          <input type="hidden" id="idCupon" name="idCupon" value="">
          
          <div class="tile border-0 shadow-sm p-4" style="border-radius: 20px; background: white;">
            
            <div class="form-group mb-3">
              <label class="font-weight-bold">Código del Cupón <span class="text-danger">*</span></label>
              <input class="form-control" id="txtCodigo" name="txtCodigo" type="text" placeholder="Ej: VERANO2026" required style="border-radius: 12px; border: 1px solid #e3e6f0;">
            </div>

            <div class="form-row">
              <div class="form-group col-md-6 mb-3">
                <label class="font-weight-bold">Descuento (%) <span class="text-danger">*</span></label>
                <input class="form-control" id="txtDescuento" name="txtDescuento" type="number" min="1" max="100" required style="border-radius: 12px; border: 1px solid #e3e6f0;">
              </div>
              <div class="form-group col-md-6 mb-3">
                <label class="font-weight-bold">Límite de Uso <span class="text-danger">*</span></label>
                <input class="form-control" id="txtLimite" name="txtLimite" type="number" placeholder="Ej: 50" required style="border-radius: 12px; border: 1px solid #e3e6f0;">
              </div>
            </div>

            <div class="form-group mb-3">
              <label class="font-weight-bold">Fecha de Vencimiento <span class="text-danger">*</span></label>
              <input class="form-control" id="txtFechaVencimiento" name="txtFechaVencimiento" type="date" required style="border-radius: 12px; border: 1px solid #e3e6f0;">
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold" for="listStatus">Estado <span class="text-danger">*</span></label>
              <select class="form-control selectpicker" id="listStatus" name="listStatus" required>
                <option value="1">Activo</option>
                <option value="2">Inactivo</option>
              </select>
            </div>
          </div>

          <div class="modal-footer border-0 p-0 mt-4">
            <button id="btnActionForm" class="btn btn-primary shadow-sm" type="submit" style="border-radius: 25px; padding: 10px 30px; font-weight: 600;">
              <i class="fa fa-check-circle mr-1"></i> <span id="btnText"> Guardar</span>
            </button>
            <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 10px 30px; color: #666;">
              <i class="fa fa-times-circle mr-1"></i> Cancelar
            </button>
          </div>
          
        </form>
      </div>
    </div>
  </div>
</div>