<div class="modal fade" id="modalImportar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      
      <div class="modal-header" style="background: #c9a050; color: white; border: none;">
        <h5 class="modal-title font-weight-bold">
            <i class="fas fa-file-excel mr-2"></i> Importar Productos
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="formImportarProductos" name="formImportarProductos">
        <div class="modal-body p-4">
          
          <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px; background-color: #e3f2fd;">
            <div class="d-flex">
                <i class="fas fa-info-circle mr-3 mt-1" style="font-size: 1.2rem;"></i>
                <span>Asegúrese de que el archivo <b>.xlsx</b> siga la estructura de columnas del sistema.</span>
            </div>
          </div>

          <div class="form-group mt-4">
            <label class="font-weight-bold text-muted">Seleccione el archivo de Excel</label>
            <div class="custom-file shadow-sm">
              <input type="file" class="custom-file-input" name="fileProductos" id="fileProductos" accept=".xlsx, .xls" required>
              <label class="custom-file-label label-plus" for="fileProductos" style="border-radius: 10px;">Elegir archivo...</label>
            </div>
          </div>

        </div>
    

        <div class="modal-footer bg-light border-0">
          <button id="btnActionForm" class="btn btn-primary shadow-sm px-4" type="submit" style="border-radius: 25px; padding: 10px 25px; background-color: #c9a050; border: none;">
            <i class="fa fa-fw fa-lg fa-check-circle"></i> <span id="btnText">Subir productos</span>
          </button>
          <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 10px 25px;">
              Cancelar
          </button>
        </div>
      </form>
      
    </div>
  </div>
</div>