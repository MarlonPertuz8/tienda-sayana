<div class="modal fade modal-pro" id="modalFormCategorias" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            <div class="modal-header headerRegister" style="background: #009688; color: white; border: 0;">
                <h5 class="modal-title font-weight-bold" id="titleModal">Nueva Categoría</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="h3">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form id="formCategoria" name="formCategoria" class="form-horizontal" enctype="multipart/form-data">
                    <input type="hidden" id="idCategoria" name="idCategoria" value="">
                    <input type="hidden" id="foto_actual" name="foto_actual" value="">
                    <input type="hidden" id="foto_remove" name="foto_remove" value="0">
                    
                    <p class="text-muted mb-4 small">Los campos marcados con (<span class="text-danger">*</span>) son necesarios.</p>

                    <div class="row">
                        <div class="col-md-7">
                            <div class="tile border-0 shadow-sm p-4 h-100" style="border-radius: 20px; background: white;">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">Nombre <span class="text-danger">*</span></label>
                                    <input class="form-control py-4" id="txtNombre" name="txtNombre" type="text" placeholder="Ej: Joyería Reluciente" required style="border-radius: 12px; background: #f8f9fa; border: 1px solid #eee;">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">Descripción <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="4" placeholder="Breve descripción..." required style="border-radius: 12px; background: #f8f9fa; border: 1px solid #eee;"></textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark">Estado <span class="text-danger">*</span></label>
                                    <select class="form-control selectpicker shadow-sm" id="listStatus" name="listStatus" required>
                                        <option value="1">Activo</option>
                                        <option value="2">Inactivo</option>
                                    </select>
                                </div>  
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="tile border-0 shadow-sm p-4 text-center h-100" style="border-radius: 20px; background: white;">
                                <label class="font-weight-bold text-dark d-block text-left mb-3">Foto de Portada</label>
                                
                                <div class="prevPhoto mb-3 position-relative" style="height: 180px; border-radius: 15px; border: 2px dashed #d1d3e2; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                    <span class="delPhoto notBlock bg-danger text-white rounded-circle position-absolute" style="top:8px; right:8px; width:28px; height:28px; cursor:pointer; text-align:center; line-height:28px; z-index:10;">
                                        <i class="fas fa-times"></i>
                                    </span>
                                    <label for="foto" class="mb-0 w-100 h-100" style="cursor: pointer;">
                                        <div id="imgPlaceholder" class="w-100 h-100 d-flex align-items-center justify-content-center">
                                            <img id="img" src="<?= media(); ?>/images/uploads/default.png" style="object-fit: cover; max-height: 100%; width: 100%; height: 100%;">
                                        </div>
                                    </label>
                                </div>

                                <div class="upimg">
                                    <input type="file" name="foto" id="foto" class="d-none" accept="image/*">
                                    <label for="foto" class="btn btn-primary btn-block shadow-sm" style="border-radius: 25px; font-weight: 600;">
                                        <i class="fas fa-camera mr-2"></i> SELECCIONAR FOTO
                                    </label>
                                </div>
                                <div id="form_alert" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-transparent border-0 px-0 pb-0 mt-4">
                        <button id="btnActionForm" class="btn btn-primary shadow-sm px-4" type="submit" style="border-radius: 25px; padding: 12px 30px;">
                            <i class="fa fa-check-circle mr-1"></i> <span id="btnText">Guardar Categoría</span>
                        </button>
                        <button class="btn btn-light shadow-sm" type="button" data-dismiss="modal" style="border-radius: 25px; padding: 12px 30px; color: #666;">
                            <i class="fa fa-times-circle mr-1"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalViewCategoria" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
            <div class="modal-header bg-info text-white py-3 border-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Detalles de Categoría</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover border-0 mb-0">
                    <tbody style="background: white;">
                        <tr>
                            <td class="font-weight-bold pl-4 border-0" style="width: 35%;">ID:</td>
                            <td id="celId" class="border-0 text-muted"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold pl-4">Nombre:</td>
                            <td id="celNombre"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold pl-4">Descripción:</td>
                            <td id="celDescripcion"></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold pl-4">Estado:</td>
                            <td id="celEstado"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center py-4 bg-light">
                                <p class="font-weight-bold text-muted small text-uppercase mb-3">Imagen de Portada</p>
                                <div id="imgCategoria" class="mx-auto shadow-sm border-0 p-2 bg-white" style="max-width: 280px; min-height: 160px; border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-secondary btn-block shadow-sm" data-dismiss="modal" style="border-radius: 25px;">Cerrar Vista</button>
            </div>
        </div>
    </div>
</div>