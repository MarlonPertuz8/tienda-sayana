<div class="modal fade modal-pro" id="modalFormCampana" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-campana-content">

            <div class="modal-header headerRegister modal-campana-header">
                <h5 class="modal-title">
                    <i class="fas fa-bullhorn"></i>
                    <span id="titleText">Nueva Campaña</span>
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <form id="formCampana" name="formCampana" class="form-horizontal">

                    <input type="hidden" id="idCampana" name="idCampana" value="">
                    <input type="hidden" id="foto_actual" name="foto_actual" value="">
                    <input type="hidden" id="txtJsonContenido" name="txtJsonContenido" value="">

                    <div class="row">

                        <!-- IZQUIERDA -->
                        <div class="col-lg-8">

                            <!-- INFO GENERAL -->
                            <div class="card border-0 shadow-sm mb-4 card-campana">

                                <div class="card-body p-4">

                                    <div class="campana-section-title">
                                        <div class="campana-icon-box">
                                            <i class="fas fa-rocket"></i>
                                        </div>

                                        <div>
                                            <h5 class="mb-0 font-weight-bold">
                                                Información General
                                            </h5>

                                            <small class="text-muted">
                                                Configura los datos principales de tu campaña
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4">
                                        <label class="form-label font-weight-bold">
                                            Nombre de la Campaña
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control input-campana"
                                            id="txtNombre"
                                            name="txtNombre"
                                            placeholder="Ej: Especial Día de las Madres"
                                            required>
                                    </div>

                                    <div class="form-group mt-4 mb-0">
                                        <label class="form-label font-weight-bold">
                                            Descripción Corta (SEO)
                                        </label>

                                        <textarea
                                            class="form-control textarea-campana"
                                            id="txtDescripcionCorta"
                                            name="txtDescripcionCorta"
                                            rows="3"
                                            placeholder="Describe brevemente esta campaña..."></textarea>
                                    </div>

                                </div>

                            </div>

                            <!-- LANDING -->
                            <div class="card border-0 shadow-sm card-campana">
                                <div class="card-body p-4">
                                    <div class="campana-section-title mb-4">
                                        <div class="campana-icon-box"><i class="fas fa-layer-group"></i></div>
                                        <div>
                                            <h5 class="mb-0 font-weight-bold">Constructor de Contenido</h5>
                                            <small class="text-muted">Añade y personaliza las secciones de tu página</small>
                                        </div>
                                    </div>

                                    <div class="landing-builder-grid mb-4" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px;">
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('hero')">
                                            <i class="fas fa-window-maximize"></i> <span>+ Hero</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('banner')">
                                            <i class="fas fa-image"></i> <span>+ Banner</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('media')">
                                            <i class="fas fa-play-circle"></i> <span>+ Multimedia</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('cards')">
                                            <i class="fas fa-th-large"></i> <span>+ Cards</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('cta')">
                                            <i class="fas fa-bullhorn"></i> <span>+ CTA</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('popup')">
                                            <i class="fas fa-window-restore"></i> <span>+ Popup</span>
                                        </button>
                                        <button type="button" class="landing-block-btn" onclick="agregarNuevoBloque('ruleta')">
                                            <i class="fas fa-dharmachakra"></i> <span>+ Ruleta</span>
                                        </button>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="font-weight-bold small text-muted">ESTRUCTURA Y EDICIÓN</label>
                                            <div id="blocksEditorList" class="bg-white border rounded p-2" style="max-height: 600px; overflow-y: auto; min-height: 400px;">
                                                <p class="text-center text-muted p-4">No hay bloques aún.</p>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <label class="font-weight-bold small text-muted">VISTA PREVIA EN VIVO</label>
                                            <div class="landing-editor-box" style="zoom: 0.7;">
                                                <div id="livePreview" class="bg-light border" style="min-height: 600px; border-radius: 8px; overflow: hidden; padding: 10px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <textarea id="txtContenidoHtml" name="txtContenidoHtml" class="d-none"></textarea>
                                </div>
                            </div>

                        </div>

                        <!-- DERECHA -->
                        <div class="col-lg-4">

                            <!-- FECHAS -->
                            <div class="card border-0 shadow-sm mb-4 card-campana">

                                <div class="card-body p-4">

                                    <div class="campana-section-title mb-4">

                                        <div class="campana-icon-box">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>

                                        <div>
                                            <h6 class="mb-0 font-weight-bold">
                                                Vigencia
                                            </h6>

                                            <small class="text-muted">
                                                Fechas de publicación
                                            </small>
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label class="small font-weight-bold">
                                            Fecha Inicio *
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control input-campana"
                                            id="txtFechaInicio"
                                            name="txtFechaInicio"
                                            required>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label class="small font-weight-bold">
                                            Fecha Fin *
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control input-campana"
                                            id="txtFechaFin"
                                            name="txtFechaFin"
                                            required>
                                    </div>

                                </div>

                            </div>

                            <!-- BANNER -->
                            <div class="card border-0 shadow-sm mb-4 card-campana">
                                <div class="card-body p-4">
                                    <div class="campana-section-title d-flex align-items-center mb-4">
                                        <div class="campana-icon-box mr-3">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">Banner Principal</h6>
                                            <small class="text-muted">Imagen destacada</small>
                                        </div>
                                    </div>

                                    <div class="photo mt-2">
                                        <div class="prevPhoto modern-upload-box position-relative">
                                            <span class="delPhoto notBlock" style="cursor: pointer;">
                                                <i class="fas fa-times"></i>
                                            </span>

                                            <label for="foto" class="d-block w-100 mb-0" style="cursor: pointer;">
                                                <div id="iconUpload" class="text-center text-muted p-5">
                                                    <i class="fas fa-cloud-upload-alt upload-icon fa-3x"></i>
                                                    <h6 class="mt-3 mb-1">Subir Imagen</h6>
                                                    <span class="small text-uppercase">JPG, PNG o WEBP</span>
                                                </div>

                                                <img id="imgNav" src="" class="img-fluid d-none" />
                                            </label>
                                        </div>

                                        <input
                                            type="file"
                                            name="foto"
                                            id="foto"
                                            accept="image/*"
                                            style="display:none;">
                                    </div>
                                </div>
                            </div>

                            <!-- BOTON -->
                            <div class="card border-0 shadow-sm mb-4 card-campana">

                                <div class="card-body p-4">

                                    <div class="form-group mb-0">

                                        <label class="font-weight-bold small text-uppercase">
                                            URL del Botón
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control input-campana"
                                            id="txtEnlaceBoton"
                                            name="txtEnlaceBoton"
                                            placeholder="https://...">

                                    </div>

                                </div>

                            </div>

                            <!-- ESTADO -->
                            <div class="card border-0 shadow-sm mb-4 card-campana">

                                <div class="card-body p-4">

                                    <div class="form-group mb-0">

                                        <label class="font-weight-bold small text-uppercase">
                                            Estado
                                        </label>

                                        <select
                                            class="form-control input-campana"
                                            id="listStatus"
                                            name="listStatus">
                                            <option value="1">Activo</option>
                                            <option value="2">Inactivo</option>
                                        </select>

                                    </div>

                                </div>

                            </div>

                            <!-- BOTON -->
                            <button
                                type="submit"
                                id="btnActionForm"
                                class="btn btn-primary btn-block btn-save-campana">
                                <i class="fas fa-check-circle"></i>
                                <span id="btnText">Guardar Campaña</span>
                            </button>

                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-pro" id="modalViewCampana" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-campana-content">

            <div class="modal-header modal-campana-header" style="background: #4e73df;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-eye"></i>
                    <span>Detalles de la Campaña</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4 card-campana">
                            <div class="card-body p-4">
                                <div class="campana-section-title mb-3">
                                    <div class="campana-icon-box"><i class="fas fa-desktop"></i></div>
                                    <div>
                                        <h5 class="mb-0 font-weight-bold">Vista Previa de la Página</h5>
                                        <small class="text-muted">Así es como se ve el contenido construido</small>
                                    </div>
                                </div>
                                <div id="celContenido" class="bg-white border rounded shadow-inner" style="min-height: 500px; overflow-y: auto; zoom: 0.8; padding: 15px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4 card-campana">
                            <div class="card-body p-4">
                                <div class="campana-section-title mb-4">
                                    <div class="campana-icon-box"><i class="fas fa-info-circle"></i></div>
                                    <h6 class="mb-0 font-weight-bold">Estado Actual</h6>
                                </div>
                                <div class="mb-3">
                                    <label class="small font-weight-bold text-uppercase text-muted d-block">Nombre</label>
                                    <span id="celNombre" class="h6 font-weight-bold text-dark"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="small font-weight-bold text-uppercase text-muted d-block">Estado</label>
                                    <div id="celEstado"></div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-muted">Inicio</label>
                                        <p id="celFechaInicio" class="small"></p>
                                    </div>
                                    <div class="col-6">
                                        <label class="small font-weight-bold text-muted">Fin</label>
                                        <p id="celFechaFin" class="small"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4 card-campana">
                            <div class="card-body p-4">
                                <label class="small font-weight-bold text-uppercase text-muted">Banner Principal</label>
                                <div class="text-center mt-2 border rounded p-2 bg-white">
                                    <img id="imgBanner" src="" class="img-fluid rounded" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4 card-campana">
                            <div class="card-body p-4">
                                <label class="small font-weight-bold text-uppercase text-muted">Enlace del Botón</label>
                                <div id="celEnlace" class="alert alert-secondary py-2 small mt-1 text-truncate"></div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary btn-block shadow-sm" data-dismiss="modal">
                            <i class="fas fa-times-circle"></i> Cerrar Vista
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>