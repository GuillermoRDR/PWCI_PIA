// --- UTILIDADES NECESARIAS (NO BORRAR) ---
const $ = (sel, root = document) => root.querySelector(sel);

function calcAge(isoDate) {
    const birth = new Date(isoDate + 'T00:00:00');
    const now = new Date();
    let age = now.getFullYear() - birth.getFullYear();
    const m = now.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && now.getDate() < birth.getDate())) age--;
    return age;
}

// ---- REGISTRO: 18+ ----
const regForm = $('#registerForm');
if (regForm) {
    regForm.addEventListener('submit', (e) => {
        const dob = $('#fechaNacimiento')?.value;
        const pwd = $('#contrasenia')?.value || '';
        const pwd2 = $('#contrasenia2')?.value || '';
        const alias = $('#alias')?.value || '';
        const errors = [];

        if (dob) {
            if (calcAge(dob) < 18) errors.push('Debes ser mayor de 18 años.');
        } else {
            errors.push('Ingresa tu fecha de nacimiento.');
        }

        const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{10,}$/;
        if (!strong.test(pwd)) errors.push('Contraseña débil: usa 10+ caracteres, mayúscula, minúscula, número y símbolo.');
        if (pwd !== pwd2) errors.push('Las contraseñas no coinciden.');
        if (alias.trim().length < 3) errors.push('Alias mínimo 3 caracteres.');

        if (errors.length) {
            e.preventDefault();
            alert('Revisa esto:\n\n- ' + errors.join('\n- '));
        } else {
            const tipo = $('#tipoUsuario')?.value || 'asegurado';
            localStorage.setItem('mockRole', tipo);
            localStorage.setItem('mockAlias', alias.trim());
        }
    });
}

// ---- VISTA PREVIA DE FOTO ----
const fotoExiste = document.getElementById('foto');
if (fotoExiste) {
    fotoExiste.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');
        if (!file || !file.type.startsWith('image/')) {
            if (file) alert('Solo se permiten imágenes');
            preview.style.display = 'none';
            return;
        }
        const reader = new FileReader();
        reader.onload = (event) => {
            preview.src = event.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
}

// ---- FEEDBACK CON SWEETALERT ----
const params = new URLSearchParams(window.location.search);
const alertas = {
    success: { icon: "success", title: "Registro exitoso", text: "Registrado correctamente" },
    loginSuccess: { icon: "success", title: "Login exitoso", text: "Sesión iniciada" },
    error: { icon: "error", title: "Error", text: "No se pudo registrar" },
    loginError: { icon: "error", title: "Error", text: "Credenciales incorrectas" }
};

for (let key in alertas) {
    if (params.get(key)) {
        Swal.fire(alertas[key]);
        break;
    }
}
