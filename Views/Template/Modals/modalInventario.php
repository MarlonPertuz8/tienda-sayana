<div class="modal fade" id="modalFormInventario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <div class="modal-header headerRegister" style="background: #009688; color: white;">
        <h5 class="modal-title" id="titleModal">Registrar Movimiento</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formInventario" name="formInventario">
        <input type="hidden" id="idEntrada" name="idEntrada" value="">

        <div class="modal-body p-4">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="control-label font-weight-bold">Producto <span class="text-danger">*</span></label>
                <select class="form-control" data-live-search="true" id="listProducto" name="listProducto" required style="border-radius: 10px;"></select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Variante / Color</label>
                <select class="form-control" id="listColor" name="listColor" style="border-radius: 10px;">
                  <option value="">Seleccione color</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label font-weight-bold">Tipo de Movimiento</label>
                <select class="form-control" id="listTipo" name="listTipo" style="border-radius: 10px;">
                  <option value="1">Entrada (Compra/Producción)</option>
                  <option value="2">Ajuste Positivo</option>
                </select>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label font-weight-bold">Cantidad <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="txtCantidad" name="txtCantidad" required style="border-radius: 10px;">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label font-weight-bold">Costo Unitario <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="txtPrecioCosto" name="txtPrecioCosto" step="any" required style="border-radius: 10px;">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label font-weight-bold">Proveedor <span class="text-danger">*</span></label>
                <select class="form-control" data-live-search="true" id="listProveedor" name="listProveedor" required style="border-radius: 10px;">
                  <option value="">Seleccione proveedor</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button id="btnActionForm" type="submit" class="btn btn-primary shadow-sm" style="border-radius: 25px; padding: 10px 25px;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i> <span id="btnText">Guardar</span>
          </button>
          <button type="button" class="btn btn-light shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 10px 25px;">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modalViewInventario" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <div class="modal-header header-primary" style="background: #007bff; color: white;">
        <h5 class="modal-title">Detalle del Movimiento</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="table-responsive">
          <table class="table table-hover border-0">
            <tbody style="border-radius: 15px;">
              <tr>
                <td class="border-0"><strong>Producto:</strong></td>
                <td class="border-0" id="celProducto"></td>
              </tr>
              <tr>
                <td><strong>Cantidad:</strong></td>
                <td id="celCantidad"></td>
              </tr>
              <tr>
                <td><strong>Costo:</strong></td>
                <td id="celPrecio"></td>
              </tr>
              <tr>
                <td><strong>Proveedor:</strong></td>
                <td id="celProveedor"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal" style="border-radius: 25px; padding: 8px 20px;">Cerrar</button>
      </div>
    </div>
  </div>
</div>