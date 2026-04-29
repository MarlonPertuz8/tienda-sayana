<div class="modal fade modal-pro" id="modalFormProductos" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header headerRegister" style="background: #009688; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-box-open"></i>
                    <span id="titleText">Nuevo Producto</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <form id="formProductos" name="formProductos" class="form-horizontal">
                    <input type="hidden" id="idProducto" name="idProducto" value="">

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card-pro mb-3 p-3 shadow-sm bg-white" style="border-radius: 15px; border: 1px solid #e3e6f0;">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold">Nombre del Producto <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="txtNombre" name="txtNombre" placeholder="Ej: Camiseta Deportiva Pro" required style="border-radius: 10px;">
                                </div>
                                <div class="form-group mt-3">
                                    <label class="form-label font-weight-bold">Descripción</label>
                                    <textarea class="form-control" id="txtDescripcion" name="txtDescripcion" rows="4" style="border-radius: 10px;"></textarea>
                                </div>
                            </div>

                            <div class="card-pro p-3 shadow-sm bg-white" id="containerGallery" style="border-radius: 15px; border: 1px solid #e3e6f0;">
                                <div class="card-header-flex d-flex justify-content-between align-items-center mb-3">
                                    <span class="h6 mb-0 font-weight-bold"><i class="fas fa-images"></i> Galería de Imágenes</span>
                                    <button type="button" class="btn btn-primary btn-sm btnAddImage shadow-sm" onclick="fntAddImage()" style="border-radius: 20px; padding: 5px 15px;">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                                <div id="containerImages" class="image-grid-pro d-flex flex-wrap gap-2 p-2" style="min-height: 100px; border: 2px dashed #009688; border-radius: 10px; background: #f8f9fa;">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div id="accordionConfig">

                                <div class="card-pro mb-3 p-3 shadow-sm bg-white" style="border-radius: 15px; border: 1px solid #e3e6f0;">
                                    <div class="card-header-flex font-weight-bold" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseOne">
                                        <span><i class="fas fa-barcode"></i> Identificación</span>
                                        <i class="fas fa-chevron-down small float-right mt-1"></i>
                                    </div>
                                    <div id="collapseOne" class="collapse show mt-2">
                                        <label class="form-label d-block text-left small font-weight-bold">Código de Barras (SKU)</label>
                                        <input type="text" class="form-control mb-2" id="txtCodigo" name="txtCodigo" placeholder="Código / SKU" style="border-radius: 10px;">
                                        <div id="divBarCode" class="barcode-box border p-2 bg-light mb-2 text-center" style="border-radius: 10px;">
                                            <svg id="barcode"></svg>
                                        </div>
                                        <button type="button" onclick="fntPrintBarcode('#barcode')" class="btn btn-dark btn-sm w-100 shadow-sm" style="border-radius: 10px;">
                                            <i class="fas fa-print"></i> Imprimir Código
                                        </button>
                                    </div>
                                </div>

                                <div class="card-pro mb-3 p-3 shadow-sm bg-white" style="border-radius: 15px; border: 1px solid #e3e6f0;">
                                    <div class="card-header-flex font-weight-bold" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseTwo">
                                        <span><i class="fas fa-tag"></i> Inventario</span>
                                        <i class="fas fa-chevron-down small float-right mt-1"></i>
                                    </div>
                                    <div id="collapseTwo" class="collapse mt-2">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label small font-weight-bold">Precio de Venta <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="txtPrecio" name="txtPrecio" placeholder="0.00" required style="border-radius: 10px;">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label small font-weight-bold">Precio Oferta (Opcional)</label>
                                                <input type="text" class="form-control" id="txtPrecioOferta" name="txtPrecioOferta" placeholder="0.00" style="border: 1px solid #ffc107; border-radius: 10px;">
                                                <small class="text-muted">Dejar en 0 o vacío si no hay descuento.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-pro mb-3 p-3 shadow-sm bg-white" style="border-radius: 15px; border: 1px solid #e3e6f0; overflow: visible;">
                                    <div class="card-header-flex font-weight-bold" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseThree">
                                        <span><i class="fas fa-layer-group"></i> Clasificación</span>
                                        <i class="fas fa-chevron-down small float-right mt-1"></i>
                                    </div>
                                    <div id="collapseThree" class="collapse mt-2" style="overflow: visible;">
                                        <div class="form-group mb-3">
                                            <label class="form-label small font-weight-bold">Categoría</label>
                                            <select class="form-control selectpicker" id="listCategoria" name="listCategoria" data-live-search="true"></select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label small font-weight-bold">Material <span class="text-danger">*</span></label>
                                            <select class="form-control selectpicker" id="listMaterial" name="listMaterial" data-live-search="true">
                                                <option value="1">Acero</option>
                                                <option value="2">Rodio</option>
                                                <option value="3">Acrílicos</option>
                                                <option value="4">Perlas</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-pro mb-3 p-3 shadow-sm bg-white" style="border-radius: 15px; border: 1px solid #e3e6f0;">
                                    <div class="card-header-flex font-weight-bold" style="cursor: pointer;" data-toggle="collapse" data-target="#collapseFour">
                                        <span><i class="fas fa-palette"></i> Atributos</span>
                                        <i class="fas fa-chevron-down small float-right mt-1"></i>
                                    </div>
                                    <div id="collapseFour" class="collapse mt-2">
                                        <div class="form-group mb-3">
                                            <label class="form-label small font-weight-bold">Colores Disponibles</label>
                                            <div class="color-input-container d-flex gap-2">
                                                <input type="text" class="form-control m-0" id="txtColorInput" placeholder="Ej: Dorado" style="border-radius: 10px;">
                                                <button class="btn btn-primary-soft ml-2" type="button" onclick="fntAddColor()" style="border-radius: 10px;">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <div id="containerColores" class="colors-wrapper mt-2"></div>
                                            <input type="hidden" name="txtColores" id="txtColores">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label small font-weight-bold">Estado</label>
                                            <select class="form-control selectpicker" id="listStatus" name="listStatus">
                                                <option value="1">Activo</option>
                                                <option value="2">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="btnActionForm" class="btn btn-primary btn-block py-2 mb-3 shadow-sm"
                                    style="border-radius: 25px; background: #274e66; border: none; font-weight: bold;">
                                    <i class="fas fa-check-circle"></i> <span id="btnText">Guardar</span>
                                </button>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalViewProducto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-pro shadow-lg">
            <div class="modal-header" style="background: #274e66; color: white; border: none;">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i> Detalles del Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-hover border-0">
                    <tbody style="border: none;">
                        <tr>
                            <td class="border-0" width="30%"><strong>Código/SKU:</strong></td>
                            <td class="border-0 text-muted" id="celCodigo"></td>
                        </tr>
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td id="celNombre" class="font-weight-bold text-dark"></td>
                        </tr>
                        <tr>
                            <td><strong>Precio:</strong></td>
                            <td id="celPrecio" class="text-dark"></td>
                        </tr>
                        <tr class="table-warning" style="background-color: rgba(255, 193, 7, 0.1);">
                            <td><strong>Precio Oferta:</strong></td>
                            <td id="celPrecioOferta" class="font-weight-bold text-warning"></td>
                        </tr>
                        <tr>
                            <td><strong>Stock:</strong></td>
                            <td><span id="celStock" class="badge badge-primary px-3 py-2" style="border-radius: 10px;"></span></td>
                        </tr>
                        <tr>
                            <td><strong>Categoría:</strong></td>
                            <td id="celCategoria" class="text-muted"></td>
                        </tr>
                        <tr>
                            <td><strong>Colores:</strong></td>
                            <td id="celColor"></td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td id="celStatus"></td>
                        </tr>
                        <tr>
                            <td><strong>Descripción:</strong></td>
                            <td id="celDescripcion" class="text-muted small"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border-0 pt-4">
                                <label class="font-weight-bold text-uppercase small text-muted">Galería de Fotos</label>
                                <div id="celImagenes" class="d-flex flex-wrap gap-2 p-3 bg-light" style="border-radius: 15px; border: 1px solid #eee;"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>