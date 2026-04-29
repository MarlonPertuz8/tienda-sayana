<div class="modal fade" id="modalFormRoles" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header headerRegister" style="background: #274e66; color: white; border: none; padding: 1.2rem;">
        <h5 class="modal-title" id="titleModal">Nuevo Rol</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formRol" name="formRol">
        <input type="hidden" id="idRol" name="idRol" value="">
        
        <div class="modal-body p-4">
          <div class="form-group">
            <label class="control-label font-weight-bold">Nombre <span class="text-danger">*</span></label>
            <input class="form-control" id="txtNombre" name="txtNombre" placeholder="Nombre del rol" type="text" required="" style="border-radius: 10px;">
          </div>

          <div class="form-group">
            <label class="control-label font-weight-bold">Descripción <span class="text-danger">*</span></label>
            <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="2" placeholder="Descripción del rol" required="" style="border-radius: 10px;"></textarea>
          </div>

          <div class="form-group">
            <label class="font-weight-bold" for="listStatus">Estado <span class="text-danger">*</span></label>
            <select class="form-control" id="listStatus" name="listStatus" required="" style="border-radius: 10px;">
              <option value="1">Activo</option>
              <option value="2">Inactivo</option>
            </select>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button id="btnActionForm" type="submit" class="btn btn-primary shadow-sm" style="border-radius: 25px; padding: 10px 25px;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i> <span id="btnText">Guardar</span>
          </button>
          
          <button type="button" class="btn btn-light shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 10px 25px;">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>