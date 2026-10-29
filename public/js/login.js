
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const identificador = document.getElementById('email');
  const password = document.getElementById('password');
  const alerta = document.getElementById('jsAlert');
  const mensaje = document.getElementById('jsAlertMessage');
  const boton = form.querySelector('button[type="submit"]');

  const base = new URL('.', window.location.href);
  const loginUrl = new URL('login', base);

  function mostrarError(texto) {
    mensaje.textContent = texto;
    alerta.style.display = 'flex';
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    alerta.style.display = 'none';

    const usuario = identificador.value.trim();
    const clave = password.value;

    if (!usuario || !clave) {
      mostrarError('Completa todos los campos.');
      return;
    }

    boton.disabled = true;
    boton.textContent = 'Ingresando...';

    try {
      const respuesta = await fetch(loginUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          identificador: usuario,
          password: clave
        })
      });

      const datos = await respuesta.json();

      if (!respuesta.ok || !datos.ok) {
        mostrarError(datos.mensaje || 'No se pudo iniciar sesión.');
        password.value = '';
        return;
      }

      window.location.assign(datos.redirect);

    } catch (error) {
      mostrarError('No se pudo conectar con el servidor.');
    } finally {
      boton.disabled = false;
      boton.textContent = 'Ingresar';
    }
  });
});
