<?php require base_path('views/partials/legacy.php');
legacyStart('Administración', 'dashboard_admin'); ?>
<div class="container">
  <div class="page-header">
    <h1 class="page-title">Administración</h1><a class="btn-primary" href="<?= e(url('/reportes')) ?>">Reporte de
      usuarios</a>
  </div>
  <section class="card-section category-section">
    <h2>Categorías</h2>
    <form class="category-create-form" method="post" action="<?= e(url('/categoria-guardar')) ?>">
      <?php csrfPortal(); ?>
      <label for="categoria-nombre">Nombre<input id="categoria-nombre" class="form-control" name="nombre" required
          minlength="2"></label>
      <label for="categoria-descripcion">Descripción<textarea id="categoria-descripcion" class="form-control"
          name="descripcion" rows="2"></textarea></label>
      <button class="btn-primary" type="submit">Agregar</button>
    </form>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categorias as $cat): ?>
            <tr>
              <td>
                <label class="category-field" for="category-name-<?= (int) $cat['categoria_id'] ?>">Nombre
                  <input id="category-name-<?= (int) $cat['categoria_id'] ?>"
                    form="category-form-<?= (int) $cat['categoria_id'] ?>" class="form-control" name="nombre"
                    value="<?= e($cat['nombre']) ?>" required minlength="2">
                </label>
                <label class="category-field" for="category-description-<?= (int) $cat['categoria_id'] ?>">Descripción
                  <textarea id="category-description-<?= (int) $cat['categoria_id'] ?>"
                    form="category-form-<?= (int) $cat['categoria_id'] ?>" class="form-control" name="descripcion"
                    rows="2"><?= e($cat['descripcion'] ?? '') ?></textarea>
                </label>
              </td>
              <td><?= e($cat['estado']) ?></td>
              <td>
                <div class="category-actions">
                  <form id="category-form-<?= (int) $cat['categoria_id'] ?>" method="post"
                    action="<?= e(url('/categoria-guardar')) ?>">
                    <?php csrfPortal(); ?><input type="hidden" name="categoria_id"
                      value="<?= (int) $cat['categoria_id'] ?>">
                    <button class="btn-sm" type="submit">Actualizar</button>
                  </form>
                  <form method="post" action="<?= e(url('/categoria-estado')) ?>"
                    data-confirmar="¿Cambiar estado de la categoría?">
                    <?php csrfPortal(); ?><input type="hidden" name="categoria_id"
                      value="<?= (int) $cat['categoria_id'] ?>">
                    <input type="hidden" name="estado" value="<?= $cat['estado'] === 'activa' ? 'inactiva' : 'activa' ?>">
                    <button class="btn-sm" type="submit"><?= $cat['estado'] === 'activa' ? 'Desactivar' : 'Activar' ?></button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <section class="card-section">
    <h2>Pedidos pendientes</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Alumno</th>
            <th>Curso</th>
            <th>Método</th>
            <th>Total</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody><?php foreach ($pedidos as $p): ?>
            <tr>
              <td><?= e($p['alumno']) ?></td>
              <td><?= e($p['curso']) ?></td>
              <td><?= e($p['metodo']) ?></td>
              <td>$<?= number_format((float) $p['total'], 2) ?></td>
              <td>
                <form method="post" action="<?= e(url('/pedido-confirmar')) ?>"
                  data-confirmar="¿Verificaste el pago por separado?"><?php csrfPortal(); ?><input type="hidden"
                    name="pedido_id" value="<?= (int) $p['pedido_id'] ?>"><button class="btn-sm">Confirmar</button></form>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <section class="card-section">
    <h2>Usuarios</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody><?php foreach ($usuarios as $u): ?>
            <tr>
              <td><?= e($u['nombre_usuario']) ?></td>
              <td><?= e($u['nombre_completo']) ?></td>
              <td><?= e($u['rol']) ?></td>
              <td><?= e($u['estado']) ?></td>
              <td><?php if ((int) $u['usuario_id'] !== (int) $_SESSION['user_id']): ?>
                  <form method="post" action="<?= e(url('/usuario-estado')) ?>"
                    data-confirmar="¿Cambiar estado de la cuenta?"><?php csrfPortal(); ?><input type="hidden"
                      name="usuario_id" value="<?= (int) $u['usuario_id'] ?>"><input type="hidden" name="estado"
                      value="<?= $u['estado'] === 'activo' ? 'bloqueado' : 'activo' ?>"><button
                      class="btn-sm"><?= $u['estado'] === 'activo' ? 'Bloquear' : 'Activar' ?></button></form><?php endif; ?>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <section class="card-section">
    <h2>Moderación de comentarios</h2>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Alumno</th>
            <th>Curso</th>
            <th>Comentario</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody><?php foreach ($comentarios as $c): ?>
            <tr>
              <td><?= e($c['nombre_completo']) ?></td>
              <td><?= e($c['titulo']) ?></td>
              <td><?= e($c['comentario']) ?></td>
              <td><?= e($c['estado']) ?></td>
              <td><?php if ($c['estado'] === 'visible'): ?>
                  <form method="post" action="<?= e(url('/comentario-moderar')) ?>" data-confirmar="¿Ocultar comentario?">
                    <?php csrfPortal(); ?><input type="hidden" name="comentario_id"
                      value="<?= (int) $c['comentario_id'] ?>"><input class="form-control" name="motivo" placeholder="Motivo"
                      required minlength="3"><button class="btn-sm">Ocultar</button></form>
                <?php else: ?>    <?= e($c['motivo_moderacion']) ?>  <?php endif; ?>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div><?php legacyEnd(); ?>