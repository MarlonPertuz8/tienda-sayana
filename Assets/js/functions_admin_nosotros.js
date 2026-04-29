// Función global para cargar datos en el modal
function fntEditNosotros() {
    let ajaxUrl = base_url + '/AdminNosotros/getNosotros';
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            try {
                let objData = JSON.parse(request.responseText);
                if (objData.status) {
                    // IDs CORREGIDOS para que coincidan con tu HTML
                    document.querySelector("#idPost").value = objData.data.id;
                    document.querySelector("#txtTitulo").value = objData.data.titulo;
                    document.querySelector("#foto_actual").value = objData.data.portada;
                    
                    // Actualizar imagen de portada
                    document.querySelector("#imgNav").src = base_url + '/Assets/images/uploads/' + objData.data.portada;

                    // Sincronizar TinyMCE
                    if(tinymce.get("txtContenido")){
                        tinymce.get("txtContenido").setContent(objData.data.contenido);
                    }

                    // Cambiar estilos del modal para edición
                    document.querySelector('#titleModal').innerHTML = "Actualizar Artículo";
                    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
                    document.querySelector('#btnText').innerHTML = "Actualizar";
                    
                    $('#modalFormPost').modal('show');
                }
            } catch (error) {
                console.error("Error en el mapeo:", error);
            }
        }
    }
}

// Previsualización de imágenes
function uploadImg(input, target) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) { document.querySelector(target).src = e.target.result; }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function(){
    
    // Inicializar TinyMCE para ambos campos
    if(document.querySelector(".rich-text-area")){ // Asegúrate de usar una clase común en el HTML
        tinymce.init({
            selector: '#txtContenidoHistoria, #txtContenidoMision',
            height: 300,
            plugins: 'lists link image charmap preview code table paste help wordcount',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat'
        });
    }

    // Eventos para cambio de foto
    if(document.querySelector("#foto_historia")){
        document.querySelector("#foto_historia").onchange = function() { uploadImg(this, "#imgHistoria"); };
    }
    if(document.querySelector("#foto_mision")){
        document.querySelector("#foto_mision").onchange = function() { uploadImg(this, "#imgMision"); };
    }

    // Guardar cambios
    let formNosotros = document.querySelector("#formNosotros");
    if(formNosotros){
        formNosotros.onsubmit = function(e) {
            e.preventDefault();
            
            // Sincronizar AMBOS editores antes de enviar
            if (tinymce.get("txtContenidoHistoria")) tinymce.get("txtContenidoHistoria").save();
            if (tinymce.get("txtContenidoMision")) tinymce.get("txtContenidoMision").save();

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/AdminNosotros/setNosotros'; 
            let formData = new FormData(formNosotros);
            
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    try {
                        let objData = JSON.parse(request.responseText);
                        if(objData.status){
                            $('#modalFormNosotros').modal('hide');
                            swal("Sayana", objData.msg, "success");
                            setTimeout(() => { location.reload(); }, 1500);
                        } else {
                            swal("Error", objData.msg, "error");
                        }
                    } catch (e) {
                        console.error("Error en respuesta:", request.responseText);
                    }
                }
            }
        }
    }
}, false);