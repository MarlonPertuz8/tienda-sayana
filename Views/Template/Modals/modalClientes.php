<div class="modal fade" id="modalFormCliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header" style="background: #009688; color: white; border: none;">
        <h5 class="modal-title" id="titleModal">Nuevo Cliente</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

<ul class="nav nav-tabs nav-justified border-0 mt-3 px-3" id="clienteTab" role="tablist">
  <li class="nav-item">
    <a class="nav-link active font-weight-bold" id="personal-tab" data-toggle="tab" href="#personal" role="tab">
      <i class="fas fa-user-circle mr-1"></i> 1. Información Personal
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link font-weight-bold" id="tributario-tab" data-toggle="tab" href="#tributario" role="tab">
      <i class="fas fa-file-invoice-dollar mr-1"></i> 2. Datos de Facturación
    </a>
  </li>
</ul>
      
      <form id="formCliente" name="formCliente" class="form-horizontal">
        <div class="modal-body p-4">
          <div class="tab-content" id="clienteTabContent">
            
            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
              <input type="hidden" id="idUsuario" name="idUsuario" value="">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="font-weight-bold">Nombres <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="txtNombre" name="txtNombre" required="" placeholder="Nombres del cliente" style="border-radius: 10px;">
                </div>
                <div class="form-group col-md-6">
                  <label class="font-weight-bold">Apellidos <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="txtApellido" name="txtApellido" required="" placeholder="Apellidos del cliente" style="border-radius: 10px;">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label class="font-weight-bold">Teléfono <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="txtTelefono" name="txtTelefono" required="" placeholder="Ej: 3001234567" style="border-radius: 10px;">
                </div>
                <div class="form-group col-md-6">
                  <label class="font-weight-bold">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="txtEmail" name="txtEmail" required="" placeholder="correo@ejemplo.com" style="border-radius: 10px;">
                </div>
              </div>
              <div class="form-group mb-0">
                <label class="font-weight-bold">Contraseña <span class="text-muted">(Llenar solo si desea cambiarla)</span></label>
                <input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="********" style="border-radius: 10px;">
              </div>
            </div>

            <div class="tab-pane fade" id="tributario" role="tabpanel" aria-labelledby="tributario-tab">
              <div class="form-row">
                <div class="form-group col-md-5">
                  <label class="font-weight-bold">Identificación / NIT <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="txtNit" name="txtNit" required="" placeholder="Cédula o NIT" style="border-radius: 10px;">
                </div>
                <div class="form-group col-md-7">
                  <label class="font-weight-bold">Razón Social <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="txtNombreFiscal" name="txtNombreFiscal" required="" placeholder="Nombre según RUT" style="border-radius: 10px;">
                </div>
              </div>
              <div class="form-group">
                <label class="font-weight-bold">Dirección Fiscal (DIAN) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="txtDirFiscal" name="txtDirFiscal" required="" placeholder="Dirección completa" style="border-radius: 10px;">
              </div>
              <div class="form-group mb-0">
                <label class="font-weight-bold">Estado <span class="text-danger">*</span></label>
                <select class="form-control selectpicker" id="listStatus" name="listStatus" required="">
                  <option value="1">Activo</option>
                  <option value="2">Inactivo</option>
                </select>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer bg-light border-0">
          <button id="btnActionForm" class="btn btn-primary shadow-sm px-4" type="submit" style="border-radius: 25px; padding: 10px 25px;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i> <span id="btnText">Guardar Cliente</span>
          </button>
          <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 10px 25px;">
             Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalViewCliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header" style="background: #007bff; color: white; border: none;">
        <h5 class="modal-title"><i class="fas fa-address-card mr-2"></i> Detalles del Cliente</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <tbody>
              <tr>
                <td class="font-weight-bold text-muted border-0" style="width:160px; padding-left: 20px;">Identificación:</td>
                <td class="border-0" id="celIdentificacion"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Nombres:</td>
                <td id="celNombre"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Apellidos:</td>
                <td id="celApellido"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Teléfono:</td>
                <td id="celTelefono"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Email:</td>
                <td id="celEmail"></td>
              </tr>
              <tr style="background-color: #f8f9fa;">
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">NIT / RUT:</td>
                <td id="celNit" class="font-weight-bold"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Razón Social:</td>
                <td id="celNomFiscal"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Dirección Fiscal:</td>
                <td id="celDirFiscal"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Estado:</td>
                <td id="celEstado"></td>
              </tr>
              <tr>
                <td class="font-weight-bold text-muted" style="padding-left: 20px;">Fecha Registro:</td>
                <td id="celFechaRegistro"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 8px 20px;">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>