<?php require base_path('views/portal/layout.php');
inicioPortal('Mensajes', $aviso); ?>
<div class="dos">
  <section class="panel">
    <h2>Enviar mensaje</h2>
    <form method="post" action="<?= e(url('/mensaje-enviar')) ?>"><?php csrfPortal(); ?><label>Destinatario<select
          name="destinatario_id" required>
          <option value="">Selecciona una persona</option>
          <?php foreach ($usuarios as $usuario):
            if ((int) $usuario['usuario_id'] === (int) $_SESSION['user_id'] || $usuario['estado'] !== 'activo')
              continue; ?>
            <option value="<?= (int) $usuario['usuario_id'] ?>"><?= e($usuario['nombre_completo']) ?>
              (<?= e($usuario['rol']) ?>)</option><?php endforeach; ?>
        </select></label><label>Mensaje<textarea name="contenido" required
          maxlength="2000"></textarea></label><button>Enviar</button></form>
  </section>
  <section class="panel">
    <h2>Conversaciones recientes</h2><?php if (!$mensajes): ?>
      <p class="vacio">Todavía no hay mensajes.</p><?php endif; ?><?php foreach ($mensajes as $mensaje): ?>
      <div class="mensaje"><img class="avatar" src="<?= e(foto($mensaje['foto_url'])) ?>"
          onerror="this.src='<?= e(url('/images/placeholder-curso.svg')) ?>'"
          alt=""><strong><?= e($mensaje['nombre_completo']) ?></strong> · <small><?= e($mensaje['enviado_en']) ?></small>
        <p><?= nl2br(e($mensaje['contenido'])) ?></p>
      </div><?php endforeach; ?>
  </section>
</div><?php finPortal(); ?>