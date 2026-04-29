<div class="modal fade modal-pro" id="modalFormPost" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            
            <div class="modal-header headerRegister" style="background: #009688; color: white; border-bottom: 0;">
                <h5 class="modal-title">
                    <i class="fas fa-feather-alt"></i>
                    <span id="titleModal">Nuevo Artículo</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <form id="formPost" name="formPost" class="form-horizontal">
                    <input type="hidden" id="idPost" name="idPost" value="">
                    <input type="hidden" id="foto_actual" name="foto_actual" value="">
                    <input type="hidden" id="foto_remove" name="foto_remove" value="0">
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="tile border-0 shadow-sm p-4 mb-3" style="border-radius: 20px;">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Título del Artículo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="txtTitulo" name="txtTitulo" placeholder="Ej: Tendencias de Lujo 2026" required style="border-radius: 12px; border: 1px solid #e3e6f0;">
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label font-weight-bold">Contenido del Post</label>
                                    <textarea class="form-control rich-text" id="txtContenido" name="txtContenido" rows="15" style="border-radius: 12px;"></textarea>
                                </div>
                            </div>

                            <div class="tile border-0 shadow-sm p-4" id="containerGallery" style="border-radius: 20px;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h6 mb-0 font-weight-bold"><i class="fas fa-images"></i> Imágenes del Artículo</span>
                                    <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="fntAddImage()" style="border-radius: 20px; padding: 5px 15px;">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                                <div id="containerImages" class="image-grid-pro d-flex flex-wrap gap-2">
                                    </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="tile border-0 shadow-sm p-4 mb-3" style="border-radius: 20px;">
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control selectpicker" id="listStatus" name="listStatus" required style="border-radius: 12px;">
                                        <option value="1">Activo (Publicado)</option>
                                        <option value="2">Inactivo (Borrador)</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Imagen de Portada</label>
                                    <div class="prevPhoto text-center border-0 p-2 bg-white mb-2 shadow-sm" style="border-radius: 15px; position: relative;">
                                        <span class="delPhoto notBlock" onclick="removePhoto()" style="background: #e74a3b; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; position: absolute; top: 10px; right: 10px; cursor: pointer; z-index: 10;">X</span>
                                        <label for="foto" class="mb-0">
                                            <div class="upimg" style="cursor: pointer;">
                                                <img id="imgNav" src="<?= media(); ?>/images/uploads/portada_categoria.png" style="max-width: 100%; height: auto; border-radius: 12px;">
                                            </div>
                                        </label>
                                        <input type="file" name="foto" id="foto" class="d-none">
                                    </div>
                                    <small class="text-muted d-block text-center mb-3">Sugerido: 800x500px</small>
                                </div>

                                <hr class="my-4">

                                <button type="submit" id="btnActionForm" class="btn btn-primary btn-block shadow-sm py-2" style="border-radius: 25px; font-size: 16px;">
                                    <i class="fas fa-save"></i> <span id="btnText">Guardar Artículo</span>
                                </button>
                                <button class="btn btn-light btn-block mt-2 shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; color: #666;">
                                    <i class="fas fa-times-circle"></i> Cancelar
                                </button>
                            </div>

                            <div id="form_alert"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>