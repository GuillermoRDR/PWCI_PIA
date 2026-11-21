const $ = (selector) => document.querySelector(selector);

const updateForm = $('#actualizarUsuarioForm');

if (updateForm) {
    const fotoExiste = document.getElementById('foto');
    if (fotoExiste) {
        fotoExiste.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview');

            if (!file) {
                preview.style.display = 'none';
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('Solo se permiten imágenes');
                e.target.value = '';
                preview.style.display = 'none';
                return;
            }
            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };

            reader.readAsDataURL(file);
        });
    }

    updateForm.addEventListener('submit', (e) => {
        const errors = [];
        const nombre = $('#nombre').value.trim();
        const apPaterno = $('#apellidoPaterno').value.trim();
        const apMaterno = $('#apellidoMaterno').value.trim();
        const dob = $('#fechaNacimiento').value;
        const correo = $('#correo').value.trim();
        const alias = $('#alias').value.trim();
        const pwd = $('#contrasenia').value.trim();
        const pwd2 = $('#contrasenia2').value.trim();
        console.log("Datos capturados:", { nombre, apPaterno, apMaterno, dob, correo, alias, pwd, pwd2 });

        const regexLetras = /^[a-zA-ZÁÉÍÓÚñÑ ]+$/;
        if (!regexLetras.test(nombre)) errors.push('El nombre solo debe contener letras.');
        if (alias.length < 3) errors.push('El alias debe tener al menos 3 caracteres.');

        if (dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;

            if (age < 18) errors.push('Debes ser mayor de 18 años.');
            if (age > 100) errors.push('Fecha de nacimiento no válida.');
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) errors.push('Ingresa un correo electrónico válido.');

        if (pwd !== '') {
            if (pwd !== pwd2) {
                errors.push('Las contraseñas no coinciden.');
            }

            if (pwd.length < 8) {
                errors.push('La contraseña debe tener al menos 8 caracteres.');
            }

            const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}$/;
            if (!strong.test(pwd)) {
                errors.push('Contraseña débil: usa 10+ caracteres, incluye Mayúscula, Minúscula, Número y Símbolo.');
            }
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert("Por favor corrige lo siguiente:\n\n• " + errors.join('\n• '));
        }
    });
}

const params = new URLSearchParams(window.location.search);

if (params.get("actualizarUsuarioSuccess")) {
    Swal.fire({
        icon: "success",
        title: "Actualización exitosa",
        text: "Los datos del usuario fueron actualizados correctamente"
    });
}

if (params.get("actualizarUsuarioError")) {
    Swal.fire({
        icon: "error",
        title: "Error",
        text: "No se pudieron actualizar los datos del usuario"
    });
}
