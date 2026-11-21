<?php require base_path('views/partials/legacy.php');
legacyStart('Mi perfil', 'mi_perfil'); ?>
<div class="profile-wrapper">
  <div class="profile-card">
    <h1>Mi perfil</h1>
    <div class="avatar-section">
      <div class="avatar-wrapper"><img class="avatar-img" id="avatarPreview"
          src="<?= e(foto($perfil['foto_url'] ?? null)) ?>" alt="Avatar"></div>
      <div class="meta-info">
        <p>Rol: <?= e($perfil['rol']) ?></p>
        <p>Registro: <?= e(fechaPortal($perfil['registrado_en'])) ?></p>
        <p>Último cambio: <?= e(fechaPortal($perfil['actualizado_en'] ?? null)) ?></p>
      </div>
    </div>
    <form id="profileForm" action="<?= e(url('/perfil-guardar')) ?>" method="post" enctype="multipart/form-data">
      <?php csrfPortal(); ?>
      <div class="form-grid">
        <div class="form-group full-width"><label>Nombre completo<input class="form-control" name="nombre" required
              minlength="3" maxlength="150" value="<?= e($perfil['nombre_completo']) ?>"></label></div>
        <div class="form-group full-width"><label>Correo<input class="form-control" type="email" name="correo" required
              value="<?= e($perfil['correo']) ?>"></label></div>
        <div class="form-group"><label>Género<select class="form-control"
              name="genero"><?php foreach (['Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo'] as $op): ?>
                <option value="<?= e($op) ?>" <?= $perfil['genero'] === $op ? 'selected' : '' ?>><?= e($op) ?></option>
              <?php endforeach; ?>
            </select></label></div>
        <div class="form-group"><label>Fecha de nacimiento<input class="form-control" type="date"
              name="fecha_nacimiento" required value="<?= e($perfil['fecha_nacimiento']) ?>"></label></div>
        <div class="form-group full-width"><label>Nuevo avatar<input class="form-control" type="file" name="avatar"
              accept="image/png,image/jpeg,image/webp"></label></div>
      </div><button class="btn-submit">Guardar cambios</button>
    </form>
  </div>
</div><?php legacyEnd(); ?>