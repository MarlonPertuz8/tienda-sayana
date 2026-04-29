<div class="modal fade modal-pro" id="modalFormSlider" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
      <div class="modal-header headerRegister" style="background: #009688; color: white; border: 0;">
        <h5 class="modal-title" id="titleModal">Nuevo Slider</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4 bg-light">
        <form id="formSlider" name="formSlider" class="form-horizontal">
          <input type="hidden" id="idSlider" name="idSlider" value="">
          <input type="hidden" id="foto_actual" name="foto_actual" value="">
          <input type="hidden" id="foto_remove" name="foto_remove" value="0">
          
          <p class="text-muted small">Los campos con asterisco (<span class="text-danger">*</span>) son obligatorios.</p>

          <div class="row">
            <div class="col-md-6">
              <div class="bg-white p-3 shadow-sm mb-3" style="border-radius: 20px;">
                <div class="form-group mb-3">
                  <label class="font-weight-bold">Título del Banner <span class="text-danger">*</span></label>
                  <input class="form-control" id="txtNombre" name="txtNombre" type="text" placeholder="Ej: Especial Día de las Madres" required style="border-radius: 12px;">
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold">Descripción Corta</label>
                  <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="2" placeholder="Subtítulo promocional" style="border-radius: 12px;"></textarea>
                </div>
                <div class="form-group mb-3">
                  <label class="font-weight-bold">Enlace / URL <span class="text-danger">*</span></label>
                  <input class="form-control" id="txtLink" name="txtLink" type="text" placeholder="https://tuweb.com/oferta" required style="border-radius: 12px;">
                </div>
                <div class="form-group">
                  <label class="font-weight-bold text-dark">Estado <span class="text-danger">*</span></label>
                  <select class="form-control selectpicker" id="listStatus" name="listStatus" required>
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                  </select>
                </div> 
              </div>
            </div>
            <div class="col-md-6">
              <div class="bg-white p-3 shadow-sm text-center h-100" style="border-radius: 20px;">
                <label class="font-weight-bold d-block">Imagen del Banner</label>
                
                <div class="prevPhoto mb-3 position-relative">
                  <span class="delPhoto notBlock" style="background: #e74a3b; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; position: absolute; top: 5px; right: 5px; cursor: pointer; z-index: 10;">×</span>
                  <div id="imgSlider" class="img-preview-container bg-light shadow-sm" style="border-radius: 15px; min-height: 150px; overflow: hidden;"></div>
                </div>
                
                <div class="upimg">
                  <input type="file" name="foto" id="foto" accept="image/*" class="d-none">
                  <label for="foto" class="btn btn-primary btn-block shadow-sm" style="border-radius: 25px;">
                    <i class="fas fa-image"></i> Seleccionar Imagen
                  </label>
                  <span id="fileName" class="file-name-display text-muted small mt-2 d-block"></span>
                </div>
                <div id="form_alert" class="mt-2"></div>
              </div>
            </div>
          </div>
          
          <div class="modal-footer border-0 p-0 mt-4">
            <button id="btnActionForm" class="btn btn-primary shadow-sm" type="submit" style="border-radius: 25px; padding: 10px 30px;">
              <i class="fa fa-check-circle"></i> <span id="btnText">Guardar</span>
            </button>
            <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 10px 30px;">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalViewSlider" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
      <div class="modal-header" style="background: #007bff; color: white; border: 0;">
        <h5 class="modal-title"><i class="fas fa-eye mr-2"></i> Datos del Slider</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="table-responsive">
          <table class="table table-hover border-0">
            <tbody>
              <tr>
                <td class="border-0"><strong>ID:</strong></td>
                <td class="border-0" id="celId"></td>
              </tr>
              <tr>
                <td><strong>Título:</strong></td>
                <td id="celNombre"></td>
              </tr>
              <tr>
                <td><strong>Descripción:</strong></td>
                <td id="celDescripcion"></td>
              </tr>
              <tr>
                <td><strong>Link:</strong></td>
                <td id="celLink" class="text-primary text-break"></td>
              </tr>
              <tr>
                <td><strong>Estado:</strong></td>
                <td id="celEstado"></td>
              </tr>
              <tr>
                <td colspan="2" class="text-center pt-4">
                  <strong>Banner:</strong>
                  <div id="imgSlider" class="mt-3 shadow-sm mx-auto" style="border-radius: 15px; overflow: hidden; background: #f8f9fa;">
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-secondary shadow-sm btn-block" data-dismiss="modal" style="border-radius: 25px;">Cerrar</button>
      </div>
    </div>
  </div>
</div>