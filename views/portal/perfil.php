<?php require base_path('views/portal/layout.php');
inicioPortal('Mi perfil', $aviso); ?>
<section class="panel detalle"><img src="<?= e(foto($perfil['foto_url'] ?? null)) ?>"
    onerror="this.src='<?= e(url('/images/placeholder-curso.svg')) ?>'" alt="Imagen de perfil">
  <form action="<?= e(url('/perfil-guardar')) ?>" method="post" enctype="multipart/form-data"><?php csrfPortal(); ?>
    <p>Usuario: <?= e($perfil['nombre_usuario']) ?> · Rol: <?= e($perfil['rol']) ?></p><label>Nombre<input name="nombre"
        required minlength="3" maxlength="150" value="<?= e($perfil['nombre_completo']) ?>"></label><label>Correo<input
        type="email" name="correo" required maxlength="254"
        value="<?= e($perfil['correo']) ?>"></label><label>Género<select name="genero"
        required><?php foreach (['Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo'] as $opcion): ?>
          <option <?= $perfil['genero'] === $opcion ? 'selected' : '' ?> value="<?= e($opcion) ?>"><?= e($opcion) ?></option>
        <?php endforeach; ?>
      </select></label><label>Fecha de nacimiento<input type="date" name="fecha_nacimiento" required
        max="<?= date('Y-m-d') ?>" value="<?= e($perfil['fecha_nacimiento']) ?>"></label><label>Actualizar avatar<input
        type="file" name="avatar" accept="image/png,image/jpeg,image/webp"></label>
    <p><button>Guardar perfil</button></p>
  </form>
</section><?php finPortal(); ?>