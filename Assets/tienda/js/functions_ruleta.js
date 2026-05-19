let anguloActualRuleta = 0;
let girandoRuleta = false;

// COLORES VIBRANTES (ESTILO NEÓN-PASTEL)
const COLORES_SAYANA = ['#f77870', '#c3fc8b', '#82eaff', '#FFD166', '#9B5DE5', '#F15BB5', '#00F5D4', '#FEE440'];

function iniciarDibujoRuleta(segmentos) {
    const canvas = document.getElementById('canvasRuleta');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const centro = canvas.width / 2;
    const radio = centro - 10;
    const arco = (Math.PI * 2) / segmentos.length;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    segmentos.forEach((s, i) => {
        const angulo = anguloActualRuleta + (i * arco);
        ctx.beginPath();
        ctx.fillStyle = COLORES_SAYANA[i % COLORES_SAYANA.length];
        ctx.moveTo(centro, centro);
        ctx.arc(centro, centro, radio, angulo, angulo + arco);
        ctx.fill();
        ctx.strokeStyle = "rgba(255,255,255,0.3)";
        ctx.lineWidth = 2;
        ctx.stroke();

        // TEXTO
        ctx.save();
        ctx.translate(centro, centro);
        ctx.rotate(angulo + arco / 2);
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        ctx.font = "900 18px Arial";
        ctx.shadowBlur = 5;
        ctx.shadowColor = "rgba(0,0,0,0.5)";
        let txt = s.texto.toUpperCase();
        ctx.fillText(txt.length > 15 ? txt.substring(0, 12) + "..." : txt, radio - 40, 10);
        ctx.restore();
    });

    // CÍRCULO CENTRAL (DECORATIVO)
    ctx.beginPath();
    ctx.arc(centro, centro, 50, 0, Math.PI * 2);
    ctx.fillStyle = "#111";
    ctx.fill();
}

function girarRuleta(segmentos) {
    // 1. VALIDACIÓN INICIAL: Si ya giró en esta sesión o en otra pestaña (LocalStorage)
    if (girandoRuleta || localStorage.getItem('ruleta_sayana_jugada')) {
        Swal.fire({
            title: '¡Acceso limitado!',
            text: 'Ya has participado en la ruleta. ¡Revisa tu código de descuento!',
            icon: 'warning',
            confirmButtonColor: '#f77870'
        });
        return;
    }

    girandoRuleta = true;
    
    const btn = document.getElementById('btnGirarTienda');
    if (btn) {
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.5';
    }

    const vueltas = 8 + Math.floor(Math.random() * 5);
    const arco = (Math.PI * 2) / segmentos.length;
    
    // Elegir premio al azar
    const premioIndex = Math.floor(Math.random() * segmentos.length);
    
    // Calculamos el ángulo para que el puntero coincida con el segmento
    const anguloPremio = (Math.PI * 1.5) - (premioIndex * arco) - (arco / 2);
    const anguloFinal = (vueltas * Math.PI * 2) + anguloPremio;

    const duracion = 6000;
    let inicio = null;

    function animar(timestamp) {
        if (!inicio) inicio = timestamp;
        const progreso = timestamp - inicio;
        const porcentaje = Math.min(progreso / duracion, 1);
        
        // Ease out suave (va frenando al final)
        const ease = 1 - Math.pow(1 - porcentaje, 4);
        anguloActualRuleta = ease * anguloFinal;

        iniciarDibujoRuleta(segmentos);

        if (porcentaje < 1) {
            requestAnimationFrame(animar);
        } else {
            girandoRuleta = false;
            
            // Reestablecer botón visualmente
            if (btn) {
                btn.style.pointerEvents = 'auto';
                btn.style.opacity = '1';
            }
            
            const premio = segmentos[premioIndex];

            // LÓGICA DE REINTENTO (Gira de nuevo)
            if (premio.texto.toLowerCase().includes("gira") || premio.texto.toLowerCase().includes("nuevo")) {
                Swal.fire({
                    title: '¡CASI!',
                    text: 'Te ha tocado volver a girar. ¡Inténtalo de nuevo!',
                    icon: 'info',
                    confirmButtonColor: '#f77870'
                });
            } else {
                // 2. BLOQUEO PERSISTENTE: Guardamos en LocalStorage para que afecte a todas las pestañas
                localStorage.setItem('ruleta_sayana_jugada_' + userId, 'true');
                
                // También lo guardamos en SessionStorage por compatibilidad con tu lógica actual
                sessionStorage.setItem('ruleta_jugada', 'true');
                
                mostrarPremio(premio);
            }
        }
    }
    requestAnimationFrame(animar);
}

function mostrarPremio(premio) {
    const esSorpresa = premio.texto.toLowerCase().includes("sorpresa");
    let valorDescuento = parseInt(premio.texto);
    // Si no es un número (caso sorpresa), enviamos 0
    let descuentoEnviar = isNaN(valorDescuento) ? 0 : valorDescuento;

    // 1. Registro en BD (Enviando el ID del usuario detectado en el Front)
    let formData = new FormData();
    formData.append('codigo', premio.codigo);
    formData.append('descuento', descuentoEnviar);
    formData.append('idUsuario', userId); // Envía el ID exacto al servidor

    fetch(base_url + '/Carrito/registrarCuponRuleta', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(!data.status) {
            // Si el servidor reporta un error (ej: cupón ya registrado), lo notificamos con discreción
            Swal.fire({
                title: "Atención",
                text: data.msg,
                icon: "warning",
                background: '#111',
                color: '#fff',
                confirmButtonColor: '#f77870'
            });
        }
    })
    .catch(err => {
        // En caso de un fallo crítico de red, se puede manejar una alerta silenciosa o un aviso
        console.error("Fallo de red:", err);
    });

    // 2. Configuración Visual (Bloquea el auto-cierre del parche)
    const configBase = {
        background: '#111',
        color: '#fff',
        timer: false,
        showConfirmButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
    };

    if (esSorpresa) {
        const linkWA = `https://wa.me/573023075957?text=${encodeURIComponent('¡Gané un PREMIO SORPRESA! Código: ' + premio.codigo)}`;

        Swal.fire({
            ...configBase,
            title: '🎁 ¡PREMIO SORPRESA!',
            html: `
                <div style="padding:10px;">
                    <p>Has ganado un regalo especial de la casa.</p>
                    <div style="background:linear-gradient(45deg, #f77870, #ff9472); padding:25px; border-radius:20px; margin:20px 0;">
                        <h2 style="color:#fff; font-weight:900; margin:0;">REGALO VIP</h2>
                    </div>
                    <p>Código: <b style="color:#f77870;">${premio.codigo}</b></p>
                </div>
            `,
            confirmButtonText: 'RECLAMAR POR WHATSAPP',
            confirmButtonColor: '#25D366'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(linkWA, '_blank');
                $('#modalRuletaSayana').modal('hide');
            }
        });
    } else {
        Swal.fire({
            ...configBase,
            title: '🎉 ¡ERES GANADOR!',
            html: `
                <div style="padding:10px;">
                    <p>Ganaste un <b style="color:#f77870;">${premio.texto}</b></p>
                    <div style="background:#222; border:2px dashed #f77870; padding:20px; border-radius:15px; margin:15px 0;">
                        <h2 style="color:#f77870; font-weight:900; margin:0;">${premio.codigo}</h2>
                    </div>
                    <p style="font-size:13px; color:#888;">Copia el código antes de cerrar.</p>
                </div>
            `,
            confirmButtonText: 'COPIAR Y CERRAR',
            confirmButtonColor: '#f77870'
        }).then((result) => {
            if (result.isConfirmed) {
                navigator.clipboard.writeText(premio.codigo);
                $('#modalRuletaSayana').modal('hide');
            }
        });
    }
}

function abrirRuleta(segmentos) {
    // 1. Verificamos si el usuario ha iniciado sesión PRIMERO
    // Esto es vital para saber QUÉ usuario es antes de chequear el LocalStorage
    if (typeof userLoged === 'undefined' || userLoged === false) {
        Swal.fire({
            title: '¡Inicia Sesión!',
            text: 'Debes estar registrado para poder girar la ruleta y ganar premios.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ir al Login',
            cancelButtonText: 'Cerrar',
            confirmButtonColor: '#f77870'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = base_url + '/login';
            }
        });
        return; 
    }

    // 2. Verificamos si ESTE usuario específico ya jugó en este navegador
    // Guardamos la marca como: ruleta_jugada_IDUSUARIO
    
    if (localStorage.getItem('ruleta_sayana_jugada_' + userId)) {
        Swal.fire({
            title: '¡Ya participaste!',
            text: 'Tu cuenta ya reclamó un premio de la ruleta.',
            icon: 'info',
            confirmButtonColor: '#f77870'
        });
        return;
    }

    // 3. Si todo está correcto, abrimos el modal
    $('#modalRuletaSayana').modal('show');
    
    // Usamos 'one' en lugar de 'on' para evitar que se dupliquen los eventos si abre/cierra el modal
    $('#modalRuletaSayana').one('shown.bs.modal', function () {
        iniciarDibujoRuleta(segmentos);
        document.getElementById('btnGirarTienda').onclick = function () {
            girarRuleta(segmentos);
        };
    });
}