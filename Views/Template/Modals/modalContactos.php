<div class="modal fade" id="modalViewMessage" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
      
      <div class="modal-header" style="background: linear-gradient(45deg, #fdfdfd, #f8f9fa); border-bottom: 1px solid #eee; padding: 20px;">
        <h5 class="modal-title" style="color: #333; font-weight: 600;">
            <i class="fas fa-envelope-open-text" style="color: #d4af37; margin-right: 10px;"></i> 
            Detalles del Mensaje
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" style="padding: 25px;">
        <div class="table-responsive">
          <table class="table table-borderless">
            <tbody>
              <tr style="border-bottom: 1px solid #f4f4f4;">
                <td style="width:120px; color: #888; font-size: 0.9rem;">NOMBRE</td>
                <td id="celNombre" style="font-weight: 500; color: #333;"></td>
              </tr>
              <tr style="border-bottom: 1px solid #f4f4f4;">
                <td style="color: #888; font-size: 0.9rem;">EMAIL</td>
                <td id="celEmail" style="color: #333;"></td>
              </tr>
              <tr style="border-bottom: 1px solid #f4f4f4;">
                <td style="color: #888; font-size: 0.9rem;">FECHA</td>
                <td id="celFecha" style="color: #666;"></td>
              </tr>
              <tr>
                <td colspan="2" style="padding-top: 20px;">
                    <div style="background-color: #fafafa; border-radius: 10px; padding: 15px; border: 1px solid #f0f0f0;">
                        <strong style="display: block; margin-bottom: 10px; color: #888; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Mensaje</strong>
                        <div id="celMensaje" style="white-space: pre-wrap; line-height: 1.6; color: #444;"></div>
                    </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer" style="border-top: none; padding: 20px;">
        <button type="button" class="btn btn-dark" data-dismiss="modal" 
                style="border-radius: 25px; padding: 8px 25px; font-weight: 500; background-color: #333; border: none; transition: all 0.3s;">
            Cerrar
        </button>
      </div>
    </div>
  </div>
</div>