<div class="modal fade" id="modalFormUsuario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header headerRegister" style="background: #274e66; color: white; border: none; padding: 1.2rem;">
        <h5 class="modal-title" id="titleModal">Nuevo Usuario</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formUsuario" name="formUsuario">
        <input type="hidden" id="idUsuario" name="idUsuario" value="">
        
        <div class="modal-body p-4">
          <p class="text-muted mb-4">Todos los campos con <span class="text-danger">*</span> son obligatorios.</p>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Identificación <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="txtIdentificacion" name="txtIdentificacion" required="" style="border-radius: 10px;">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
              <input type="text" class="form-control valid validText" id="txtNombre" name="txtNombre" required="" style="border-radius: 10px;">
              <div class="invalid-feedback">Solo se permiten letras.</div>
            </div>
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
              <input type="text" class="form-control valid validText" id="txtApellido" name="txtApellido" required="" style="border-radius: 10px;">
              <div class="invalid-feedback">Solo se permiten letras.</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Teléfono <span class="text-danger">*</span></label>
              <input type="text" class="form-control valid validNumber" id="txtTelefono" name="txtTelefono" required="" style="border-radius: 10px;">
              <div class="invalid-feedback">Solo números.</div>
            </div>
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control valid validEmail" id="txtEmail" name="txtEmail" required="" style="border-radius: 10px;">
              <div class="invalid-feedback">Correo inválido.</div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Tipo Usuario <span class="text-danger">*</span></label>
              <select class="form-control" data-live-search="true" id="listRolid" name="listRolid" required="" style="border-radius: 10px;"></select>
            </div>
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
              <select class="form-control selectpicker" id="listStatus" name="listStatus" required="" style="border-radius: 10px;">
                <option value="1">Activo</option>
                <option value="2">Inactivo</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label class="font-weight-bold">Contraseña</label>
              <input type="password" class="form-control" id="txtPassword" name="txtPassword" style="border-radius: 10px;">
              <small class="text-muted">Dejar vacío si no desea cambiarla.</small>
            </div>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button id="btnActionForm" type="submit" class="btn btn-primary shadow-sm" style="border-radius: 25px; padding: 10px 30px;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i> <span id="btnText">Guardar</span>
          </button>
          <button type="button" class="btn btn-light shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 10px 30px;">Cancelar</button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade" id="modalViewUser" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header headerPrimary" style="background: #007bff; color: white; border: none; padding: 1.2rem;">
        <h5 class="modal-title">Datos del Usuario</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <div class="table-responsive">
          <table class="table table-hover border-0">
            <tbody>
              <tr>
                <td class="border-0"><strong>Identificación:</strong></td>
                <td class="border-0" id="celIdentificacion"></td>
              </tr>
              <tr>
                <td><strong>Nombres:</strong></td>
                <td id="celNombre"></td>
              </tr>
              <tr>
                <td><strong>Apellidos:</strong></td>
                <td id="celApellido"></td>
              </tr>
              <tr>
                <td><strong>Email:</strong></td>
                <td id="celEmail"></td>
              </tr>
              <tr>
                <td><strong>Teléfono:</strong></td>
                <td id="celTelefono"></td>
              </tr>
              <tr>
                <td><strong>Tipo Usuario:</strong></td>
                <td id="celTipoUsuario"></td>
              </tr>
              <tr>
                <td><strong>Estado:</strong></td>
                <td id="celEstado"></td>
              </tr>
              <tr>
                <td><strong>Fecha Registro:</strong></td>
                <td id="celFechaRegistro"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 8px 25px;">Cerrar</button>
      </div>
    </div>
  </div>
</div>