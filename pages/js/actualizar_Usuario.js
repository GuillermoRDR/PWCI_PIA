const $ = (selector) => document.querySelector(selector);

const updateForm = $('#actualizarUsuarioForm');

if (updateForm) {
    // Preview de imagen en tiempo real
    // ################################### Vista previa de foto ###################################
    const fotoExiste = document.getElementById('foto');
    if(fotoExiste){
    fotoExiste.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');

        if (!file) {
            preview.style.display = 'none';
            return;
        }

        // Validación básica
        if (!file.type.startsWith('image/')) {
            alert('Solo se permiten imágenes');
            e.target.value = '';
            preview.style.display = 'none';
            return;
        }

        // Crear URL temporal
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
        
        // 1. Captura y Limpieza (Trim)
        const nombre = $('#nombre').value.trim();
        const apPaterno = $('#apellidoPaterno').value.trim();
        const apMaterno = $('#apellidoMaterno').value.trim();
        const dob = $('#fechaNacimiento').value;
        const correo = $('#correo').value.trim();
        const alias = $('#alias').value.trim();
        const pwd = $('#contrasenia').value.trim();
        const pwd2 = $('#contrasenia2').value.trim();
        console.log("Datos capturados:", { nombre, apPaterno, apMaterno, dob, correo, alias, pwd, pwd2 });

        // 2. Validaciones de Texto (Solo letras y longitud)
        const regexLetras = /^[a-zA-ZÁÉÍÓÚñÑ ]+$/;
        if (!regexLetras.test(nombre)) errors.push('El nombre solo debe contener letras.');
        if (alias.length < 3) errors.push('El alias debe tener al menos 3 caracteres.');

        // 3. Validación de Edad (Lógica de negocio: +18)
        if (dob) {
            const birthDate = new Date(dob);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
            
            if (age < 18) errors.push('Debes ser mayor de 18 años.');
            if (age > 100) errors.push('Fecha de nacimiento no válida.');
        }

        // 4. Validación de Correo (Regex estándar)
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo)) errors.push('Ingresa un correo electrónico válido.');

        // 5. Validación de Contraseña (Opcional en Update)
        if (pwd !== '') { 
            // Primero validamos la coincidencia
            if (pwd !== pwd2) {
                errors.push('Las contraseñas no coinciden.');
            } 
            
            // Luego validamos la longitud mínima de seguridad básica (8 caracteres)
            if (pwd.length < 8) {
                errors.push('La contraseña debe tener al menos 8 caracteres.');
            }
            
            // Finalmente validamos la política de contraseña fuerte (10+ y símbolos)
            const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}$/;
            if (!strong.test(pwd)) {
                errors.push('Contraseña débil: usa 10+ caracteres, incluye Mayúscula, Minúscula, Número y Símbolo.');
            }
        }

        // 6. Manejo de Errores
        if (errors.length > 0) {
            e.preventDefault();
            // Podrías usar un modal o un div de alertas en lugar de alert()
            alert("Por favor corrige lo siguiente:\n\n• " + errors.join('\n• '));
        }
    });
}

// ################################### SweetAlert para feedback de registro ################################
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
