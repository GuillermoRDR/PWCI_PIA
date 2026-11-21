document.addEventListener('DOMContentLoaded', () => {
  const caja = document.getElementById('comentarios'); if (!caja) return;
  const error = mensaje => window.Swal ? Swal.fire({ icon: 'error', text: mensaje }) : alert(mensaje);
  async function cargar() {
    try {
      const r = await fetch(caja.dataset.url, { headers: { Accept: 'application/json' } });
      const data = await r.json(); if (!r.ok) throw new Error(data.error || 'No se pudo consultar el curso.');
      caja.replaceChildren();
      if (!data.comentarios.length) { const p = document.createElement('p'); p.textContent = 'Aún no hay comentarios.'; caja.append(p) }
      for (const item of data.comentarios) { const card = document.createElement('div'); card.className = 'comment-item'; const author = document.createElement('strong'); author.textContent = item.alumno; const date = document.createElement('small'); date.textContent = ' · ' + item.creado_en; const body = document.createElement('p'); body.textContent = item.comentario; card.append(author, date, document.createTextNode(item.calificacion == null ? '' : ' ★ ' + item.calificacion + '/5'), body); caja.append(card) }
    } catch (e) { error(e.message) }
  }
  cargar(); const form = document.getElementById('form-comentario'); if (!form) return;
  form.addEventListener('submit', async event => {
    event.preventDefault(); const fields = new FormData(form);
    try {
      const r = await fetch(caja.dataset.url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': fields.get('csrf_token') }, body: JSON.stringify({ calificacion: Number(fields.get('calificacion')), comentario: String(fields.get('comentario')).trim() }) });
      const data = await r.json(); if (!r.ok) throw new Error(data.error || 'No se pudo publicar.'); form.reset(); await cargar(); if (window.Swal) Swal.fire({ icon: 'success', text: 'Opinión guardada.' });
    } catch (e) { error(e.message) }
  });
});
