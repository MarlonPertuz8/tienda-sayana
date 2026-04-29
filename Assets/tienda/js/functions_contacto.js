document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector("#frmContacto")) {
        let frmContacto = document.querySelector("#frmContacto");
        
        frmContacto.addEventListener('submit', function(e) {
            e.preventDefault(); // Detiene el envío por la URL

            let btnEnviar = frmContacto.querySelector('button');
            let originalText = btnEnviar.innerHTML;

            // Captura de valores
            let nombre = document.querySelector('input[name="nombreContacto"]').value.trim();
            let email = document.querySelector('input[name="emailContacto"]').value.trim();
            let mensaje = document.querySelector('textarea[name="mensaje"]').value.trim();

            if (nombre === "" || email === "" || mensaje === "") {
                swal("Atención", "Todos los campos son obligatorios.", "warning");
                return false;
            }

            // Efecto de carga profesional
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ENVIANDO...';

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Contacto/enviarMensaje'; 
            let formData = new FormData(frmContacto);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function() {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        try {
                            let objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                swal("¡Enviado!", objData.msg, "success");
                                frmContacto.reset();
                            } else {
                                swal("Error", objData.msg, "error");
                            }
                        } catch (error) {
                            swal("Error", "El servidor no respondió correctamente.", "error");
                        }
                    } else {
                        swal("Error", "No se pudo conectar con el servidor.", "error");
                    }
                    btnEnviar.disabled = false;
                    btnEnviar.innerHTML = originalText;
                }
            }
        });
    }
}, false);