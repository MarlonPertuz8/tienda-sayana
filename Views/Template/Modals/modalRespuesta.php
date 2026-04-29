<div class="modal fade" id="modalRespuesta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header header-primary">
        <h5 class="modal-title" id="titleModal"><i class="fas fa-reply-all"></i> Responder Mensaje</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formRespuesta" name="formRespuesta" class="form-horizontal">
          <input type="hidden" id="idContacto" name="idContacto" value="">
          <div class="form-group">
            <label class="control-label">Para:</label>
            <input type="text" class="form-control" id="txtEmail" name="txtEmail" readonly>
          </div>
          <div class="form-group">
            <label class="control-label">Escribir Respuesta:</label>
            <textarea class="form-control" id="txtRespuesta" name="txtRespuesta" rows="5" placeholder="¿En qué podemos ayudarle?"></textarea>
          </div>
          <div class="tile-footer">
            <button id="btnActionForm" class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i> Enviar</button>&nbsp;&nbsp;&nbsp;
            <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="fa fa-fw fa-lg fa-times-circle"></i> Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>