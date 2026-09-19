const inputBusqueda = document.getElementById("buscarAsegurado");

inputBusqueda.addEventListener("keyup", buscarAsegurado);

async function buscarAsegurado(){

    const texto = inputBusqueda.value.trim();

    if(texto.length < 2){
        document.getElementById("resultadosBusqueda").innerHTML = "";
        return;
    }

    try{
        const response = await 
        fetch(`/index.php?action=buscarAsegurado&texto=${encodeURIComponent(texto)}`);
        const data = await response.json();
        mostrarResultados(data);
    }catch(error){
        console.error(error);
    }
}

function mostrarResultados(data){

    let html = "";

    if(data.length === 0){

        html = `
            <div class="resultado-item">
                No se encontraron resultados
            </div>
        `;
    }

    data.forEach(item => {
        html += `<div 
                    class="resultado-item"
                    onclick='seleccionarAsegurado(${JSON.stringify(item)})'>
                    <strong>${item.asegurado}</strong>
                    <br>
                    Póliza:
                    ${item.numero_poliza}
                    <br>
                    Placas:
                    ${item.placas}
                </div>`;
    });

    document.getElementById("resultadosBusqueda").innerHTML = html;
}

function seleccionarAsegurado(item){
    // IDs ocultos
    document.getElementById("idUnidad").value =
        item.id_unidad;

    document.getElementById("idUsuario").value =
        item.id_usuario;

    document.getElementById("idPoliza").value =
        item.id_poliza;

    // Cliente
    document.getElementById("txtAsegurado").innerText =
        item.asegurado;

    document.getElementById("txtCorreo").innerText =
        item.correo;

    // Compañía
    document.getElementById("txtCompania").innerText =
        item.compania;

    // Póliza
    document.getElementById("txtPoliza").innerText =
        item.numero_poliza;

    document.getElementById("txtEstadoPoliza").innerText =
        item.estado_poliza;

    document.getElementById("txtFechaInicio").innerText =
        item.fecha_inicio;

    document.getElementById("txtFechaFinal").innerText =
        item.fecha_final;

    // Unidad
    document.getElementById("txtPlacas").innerText =
        item.placas;

    document.getElementById("txtNumeroSerie").innerText =
        item.numero_serie;

    document.getElementById("txtMarca").innerText =
        item.marca;

    document.getElementById("txtModelo").innerText =
        item.modelo;

    document.getElementById("txtAnio").innerText =
        item.anio;

    // Limpiar resultados
    document.getElementById("resultadosBusqueda").innerHTML = "";

    // Mostrar seleccionado en el input
    document.getElementById("buscarAsegurado").value =
        item.asegurado;

    document.getElementById("imgAsegurado").src 
        =`/index.php?action=obtenerFotoUsuario&id=${item.id_usuario}`;
}

// Detectar si hay un mensaje de error en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const errorMsg = urlParams.get('error');

    if (errorMsg) {
        Swal.fire({
            icon: 'error',
            title: '¡Oops!',
            text: errorMsg,
            confirmButtonText: 'Entendido'
        });
    }

    // También puedes aprovechar para mostrar el éxito
    if (urlParams.get('registroSiniestroSuccess')) {
        Swal.fire({
            icon: 'success',
            title: '¡Logrado!',
            text: 'El siniestro se registró correctamente.',
        });
    }

const inputFotos =
    document.querySelector(
        'input[name="fotos[]"]'
    );

const inputVideos =
    document.querySelector(
        'input[name="videos[]"]'
    );

const previewFotos =
    document.getElementById(
        'previewFotos'
    );

const previewVideos =
    document.getElementById(
        'previewVideos'
    );


// ##################################### Fotos #####################################

inputFotos.addEventListener(
    'change',
    function(){

        previewFotos.innerHTML = '';

        const archivos =
            this.files;

        for(const archivo of archivos){

            const reader =
                new FileReader();

            reader.onload = function(e){

                const img =
                    document.createElement(
                        'img'
                    );

                img.src =
                    e.target.result;

                img.classList.add(
                    'preview-img'
                );

                previewFotos.appendChild(
                    img
                );
            };

            reader.readAsDataURL(
                archivo
            );
        }
    }
);


// ##################################### Videos #####################################

inputVideos.addEventListener(
    'change',
    function(){

        previewVideos.innerHTML = '';

        const archivos =
            this.files;

        for(const archivo of archivos){

            const card =
                document.createElement(
                    'div'
                );

            card.classList.add(
                'video-card'
            );

            card.innerHTML = `

                <div class="video-icon">
                    🎥
                </div>

                <div class="video-info">

                    <div class="video-name">
                        ${archivo.name}
                    </div>

                    <div class="video-size">
                        ${(
                            archivo.size
                            / 1024 / 1024
                        ).toFixed(2)} MB
                    </div>

                </div>
            `;

            previewVideos.appendChild(
                card
            );
        }
    }
);
