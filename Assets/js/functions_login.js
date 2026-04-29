document.addEventListener('DOMContentLoaded', function () {

    const divLoading = document.querySelector("#divLoading");

    // ===============================
    // FLIP LOGIN
    // ===============================
    $('.login-content [data-toggle="flip"]').click(function () {
        $('.login-box').toggleClass('flipped');
        return false;
    });

    // ===============================
    // LOGIN
    // ===============================
if (document.querySelector('#formLogin')) {
    let formLogin = document.querySelector('#formLogin');

    formLogin.onsubmit = function (e) {
        e.preventDefault();

        // 1. Detectar inputs (admin o tienda)
        let emailInput = document.querySelector('#txtEmail') || document.querySelector('#txtEmailLogin');
        let passwordInput = document.querySelector('#txtPassword') || document.querySelector('#txtPasswordLogin');

        let strEmail = emailInput ? emailInput.value.trim() : "";
        let strPassword = passwordInput ? passwordInput.value.trim() : "";

        // 2. Validación
        if (strEmail === '' || strPassword === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Debes completar email y contraseña',
                confirmButtonColor: '#274e66'
            });
            return;
        }

        if (typeof divLoading !== 'undefined') divLoading.style.display = "flex";

        let request = new XMLHttpRequest();
        let ajaxUrl = base_url + '/Login/loginUser';

        // 🔥 CREAR FORMDATA
        let formData = new FormData(formLogin);

        // 🔥 SOLUCIÓN DEFINITIVA (DETECTA ORIGEN AUTOMÁTICO)
        if (!formData.has("origen")) {

            if (window.location.href.includes('/login/tienda')) {
                formData.append("origen", "tienda");
            } else {
                formData.append("origen", "admin");
            }
        }

        // 🧪 DEBUG (puedes quitar luego)
        console.log([...formData]);

        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState !== 4) return;

            if (typeof divLoading !== 'undefined') divLoading.style.display = "none";

            if (request.status === 200) {
                let objData = JSON.parse(request.responseText);

                if (objData.status) {
                    window.location.href = objData.url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: objData.msg,
                        confirmButtonColor: '#274e66'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'No se pudo procesar la solicitud'
                });
            }
        };
    };
}

    // ===============================
    // RESET PASSWORD
    // ===============================
    if (document.querySelector('#formRecetPass')) {

		divLoading.style.display = "none";
        let formRecetPass = document.querySelector('#formRecetPass');

        formRecetPass.onsubmit = function (e) {
            e.preventDefault();

            let strEmail = document.querySelector('#txtEmailReset').value.trim();

            if (strEmail === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Debes completar email',
                    confirmButtonColor: '#274e66'
                });
                return;
            }

            if (divLoading) divLoading.style.display = "flex";

            let request = new XMLHttpRequest();
            let ajaxUrl = base_url + '/Login/resetPass';
            let formData = new FormData(formRecetPass);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {

                if (request.readyState !== 4) return;

                if (divLoading) divLoading.style.display = "none";

                if (request.status === 200) {

                    let objData = JSON.parse(request.responseText);

                    if (objData.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Correo enviado',
                            text: objData.msg,
                            confirmButtonColor: '#274e66'
                        }).then(() => {
                            window.location = base_url;
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: objData.msg,
                            confirmButtonColor: '#274e66'
                        });
                    }

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: 'No se pudo procesar la solicitud'
                    });
                }
				divLoading.style.display = "none";
            };
        };
    }

    // ===============================
    // CAMBIAR PASSWORD
    // ===============================
    if (document.querySelector("#formCambiarPass")) {

		divLoading.style.display = "none";
        let formCambiarPass = document.querySelector("#formCambiarPass");

        formCambiarPass.onsubmit = function (e) {
            e.preventDefault();

            const strPassword = document.querySelector('#txtPassword').value.trim();
            const strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value.trim();

            if (strPassword === "" || strPasswordConfirm === "") {
                Swal.fire({
                    icon: "warning",
                    title: "Campos requeridos",
                    text: "Debes escribir la nueva contraseña"
                });
                return;
            }

            if (strPassword.length < 5) {
                Swal.fire({
                    icon: "info",
                    title: "Contraseña insegura",
                    text: "La contraseña debe tener mínimo 5 caracteres"
                });
                return;
            }

            if (strPassword !== strPasswordConfirm) {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Las contraseñas no coinciden"
                });
                return;
            }

            if (divLoading) divLoading.style.display = "flex";

            let request = new XMLHttpRequest();
            let ajaxUrl = base_url + '/Login/setPassword';
            let formData = new FormData(formCambiarPass);

            request.open("POST", ajaxUrl, true);
            request.send(formData);

            request.onreadystatechange = function () {

                if (request.readyState !== 4) return;

                if (divLoading) divLoading.style.display = "none";

                if (request.status === 200) {

                    try {

                        let objData = JSON.parse(request.responseText);

                        if (objData.status) {

                            Swal.fire({
                                icon: "success",
                                title: "Contraseña actualizada",
                                text: objData.msg,
                                confirmButtonText: "Iniciar sesión",
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = base_url + '/login';
                            });

                        } else {

                            Swal.fire({
                                icon: "error",
                                title: "Atención",
                                text: objData.msg
                            });

                        }

                    } catch (error) {

                        Swal.fire({
                            icon: "error",
                            title: "Error del servidor",
                            text: "La respuesta no es JSON válido"
                        });
                    }

                } else {

                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "No se pudo procesar la solicitud"
                    });

                }
				divLoading.style.display = "none";
            };
        };
    }

});
