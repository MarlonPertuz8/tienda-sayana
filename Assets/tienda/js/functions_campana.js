
const productosBase = window.productosBase || [];
let packActual = { comboId: null, slotsMax: 0, seleccionados: [] };

function initBuilder(combo) {
    packActual.comboId = combo.id_combo;
    packActual.slotsMax = parseInt(combo.slots);
    packActual.seleccionados = [];

    // 1. Ocultar Paso 1, Mostrar Paso 2
    document.getElementById('step-1').classList.add('d-none');
    document.getElementById('step-2').classList.remove('d-none');
    document.getElementById('resumen-caja').innerText = combo.nombre;

    // 2. Generar Slots Visuales
    let htmlSlots = '';
    for(let i=0; i < packActual.slotsMax; i++) {
        htmlSlots += `<div id="slot-ui-${i}" class="slot-item d-flex align-items-center justify-content-center text-white"> <i class="fas fa-plus opacity-50"></i> </div>`;
    }
    document.getElementById('slots-visualizer').innerHTML = htmlSlots;

    // 3. Filtrar Catálogo: Solo joyas que asociaste a ESTE combo en el admin
    const joyasParaEsteCombo = productosBase.filter(p => p.id_combo == combo.id_combo);
    renderCatalogo(joyasParaEsteCombo);
}

function renderCatalogo(lista) {
    const grid = document.getElementById('catalog-grid');
    if (!grid) return;
    
    grid.innerHTML = lista.map(joya => {
        // Validación de imagen dinámica: si trae imagen la usa, de lo contrario pone la default
        let imgJoya = (joya.images && joya.images.length > 0) ? joya.images[0].url_image : 'default.png';
        let rutaImg = base_url + '/Assets/images/uploads/' + imgJoya;

        return `
            <div class="col-6 col-md-3 animate__animated animate__fadeInUp">
                <div class="card combo-card-sayana p-2 text-center border-0 h-100 shadow-sm" style="cursor: pointer;" onclick="seleccionarJoya(${joya.idproducto}, '${joya.nombre}')">
                    <img src="${rutaImg}" class="img-fluid rounded-circle p-2" style="width: 100px; height: 100px; object-fit: cover; margin: 0 auto;" onerror="this.onerror=null; this.src='${base_url}/Assets/images/uploads/default.png';">
                    <h6 class="fw-bold mt-2 small text-uppercase">${joya.nombre}</h6>
                </div>
            </div>
        `;
    }).join('');
}

function seleccionarJoya(id, nombre) {
    if (packActual.seleccionados.length < packActual.slotsMax) {
        packActual.seleccionados.push(id);
        
        // Actualizar visual del slot
        const slotDiv = document.getElementById(`slot-ui-${packActual.seleccionados.length - 1}`);
        if (slotDiv) {
            slotDiv.style.animation = "none";
            slotDiv.style.background = "var(--coral)";
            slotDiv.innerHTML = `<i class="fas fa-check"></i>`;
        }
        
        const resumenSlots = document.getElementById('resumen-slots');
        if (resumenSlots) {
            resumenSlots.innerText = `${packActual.seleccionados.length} de ${packActual.slotsMax} accesorios elegidos`;
        }

        // Si se llenó, mostramos el checkout
        if (packActual.seleccionados.length == packActual.slotsMax) {
            const stickyCheckout = document.getElementById('sticky-checkout');
            if (stickyCheckout) stickyCheckout.classList.remove('d-none');
        }
    } else {
        // FEEDBACK DE ERROR: Vibración si intenta elegir de más
        const visualizer = document.getElementById('slots-visualizer');
        if (visualizer) {
            visualizer.classList.add('shake-error');
            setTimeout(() => visualizer.classList.remove('shake-error'), 400);
        }
    }
}

function fntFinalizarPack() {
    let data = new FormData();
    data.append('idCombo', packActual.comboId);
    data.append('joyas', JSON.stringify(packActual.seleccionados));

    fetch(base_url + '/Carrito/addPack', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(obj => {
        if(obj.status) {
            window.location.href = base_url + '/carrito';
        }
    })
    .catch(err => console.error("Error al finalizar el pack:", err));
}