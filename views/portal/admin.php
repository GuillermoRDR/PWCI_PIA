<?php require base_path('views/portal/layout.php');
inicioPortal('Administración', $aviso); ?>
<div class="dos">
  <section class="panel">
    <h2>Categorías</h2>
    <form action="<?= e(url('/categoria-guardar')) ?>" method="post"><?php csrfPortal(); ?><label
        for="nombre">Nombre</label><input id="nombre" name="nombre" required minlength="2" maxlength="100"><label
        for="descripcion">Descripción</label><textarea id="descripcion" name="descripcion"
        maxlength="500"></textarea><button>Agregar</button></form>
    <div class="desplazar">
      <table class="tabla">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody><?php foreach ($categorias as $categoria): ?>
            <tr>
              <td><?= e($categoria['nombre']) ?></td>
              <td><?= e($categoria['estado']) ?></td>
              <td>
                <details>
                  <summary>Editar</summary>
                  <form action="<?= e(url('/categoria-guardar')) ?>" method="post"><?php csrfPortal(); ?><input
                      type="hidden" name="categoria_id" value="<?= (int) $categoria['categoria_id'] ?>"><label>Nombre<input
                        name="nombre" value="<?= e($categoria['nombre']) ?>" required
                        maxlength="100"></label><label>Descripción<textarea name="descripcion"
                        maxlength="500"><?= e($categoria['descripcion']) ?></textarea></label><button>Guardar</button>
                  </form>
                </details>
                <form action="<?= e(url('/categoria-estado')) ?>" method="post"
                  <?= $categoria['estado'] === 'activa' ? 'data-confirmar="¿Desactivar esta categoría?"' : '' ?>>
                  <?php csrfPortal(); ?><input type="hidden" name="categoria_id"
                    value="<?= (int) $categoria['categoria_id'] ?>"><input type="hidden" name="estado"
                    value="<?= $categoria['estado'] === 'activa' ? 'inactiva' : 'activa' ?>"><button><?= $categoria['estado'] === 'activa' ? 'Desactivar' : 'Activar' ?></button>
                </form>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
  <section class="panel">
    <h2>Pedidos pendientes</h2><?php if (!$pedidos): ?>
      <p class="vacio">No hay solicitudes pendientes.</p><?php endif; ?>
    <div class="desplazar">
      <table class="tabla">
        <thead>
          <tr>
            <th>Alumno</th>
            <th>Curso</th>
            <th>Método</th>
            <th>Total</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody><?php foreach ($pedidos as $pedido): ?>
            <tr>
              <td><?= e($pedido['alumno']) ?></td>
              <td><?= e($pedido['curso']) ?></td>
              <td><?= e($pedido['metodo']) ?></td>
              <td>$<?= number_format((float) $pedido['total'], 2) ?> MXN</td>
              <td>
                <form method="post" action="<?= e(url('/pedido-confirmar')) ?>" data-confirmar="¿Ya verificaste el pago?">
                  <?php csrfPortal(); ?><input type="hidden" name="pedido_id"
                    value="<?= (int) $pedido['pedido_id'] ?>"><button>Confirmar</button></form>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="muted">Verifica el pago por separado antes de confirmar.</p>
  </section>
</div>
<section class="panel">
  <h2>Usuarios</h2>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Usuario</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody><?php foreach ($usuarios as $usuario): ?>
          <tr>
            <td><?= e($usuario['nombre_completo']) ?></td>
            <td><?= e($usuario['nombre_usuario']) ?></td>
            <td><?= e($usuario['rol']) ?></td>
            <td><?= e($usuario['estado']) ?></td>
            <td><?php if ((int) $usuario['usuario_id'] !== (int) $_SESSION['user_id']): ?>
                <form method="post" action="<?= e(url('/usuario-estado')) ?>"
                  <?= $usuario['estado'] === 'activo' ? 'data-confirmar="¿Bloquear usuario?"' : '' ?>><?php csrfPortal(); ?><input
                    type="hidden" name="usuario_id" value="<?= (int) $usuario['usuario_id'] ?>"><input type="hidden"
                    name="estado"
                    value="<?= $usuario['estado'] === 'activo' ? 'bloqueado' : 'activo' ?>"><button><?= $usuario['estado'] === 'activo' ? 'Bloquear' : 'Activar' ?></button>
                </form><?php endif; ?>
            </td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<section class="panel">
  <h2>Comentarios</h2><?php if (!$comentarios): ?>
    <p class="vacio">Sin comentarios.</p><?php endif; ?>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Autor</th>
          <th>Curso</th>
          <th>Comentario</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody><?php foreach ($comentarios as $comentario): ?>
          <tr>
            <td><?= e($comentario['nombre_completo']) ?></td>
            <td><?= e($comentario['titulo']) ?></td>
            <td><?= e($comentario['comentario']) ?></td>
            <td><?= e($comentario['estado']) ?></td>
            <td><?php if ($comentario['estado'] === 'visible'): ?>
                <form method="post" action="<?= e(url('/comentario-moderar')) ?>" data-confirmar="¿Ocultar comentario?">
                  <?php csrfPortal(); ?><input type="hidden" name="comentario_id"
                    value="<?= (int) $comentario['comentario_id'] ?>"><input aria-label="Motivo" name="motivo"
                    placeholder="Motivo" minlength="3" maxlength="500" required><button>Ocultar</button></form>
              <?php else: ?>    <?= e($comentario['motivo_moderacion']) ?>  <?php endif; ?>
            </td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<section class="panel">
  <h2>Reporte de usuarios activos</h2><label for="filtrarRol">Filtrar por rol</label><select id="filtrarRol">
    <option value="">Todos</option>
    <option value="profesor">Profesor</option>
    <option value="alumno">Alumno</option>
  </select>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Rol</th>
          <th>Ingreso</th>
          <th>Cursos</th>
          <th>Ganancias</th>
          <th>Inscripciones</th>
          <th>Terminados</th>
        </tr>
      </thead>
      <tbody><?php foreach ($reporte as $fila): ?>
          <tr data-rol="<?= e($fila['rol']) ?>">
            <td><?= e($fila['nombre_usuario']) ?></td>
            <td><?= e($fila['nombre_completo']) ?></td>
            <td><?= e($fila['rol']) ?></td>
            <td><?= e(fechaPortal($fila['registrado_en'])) ?></td>
            <td><?= (int) $fila['cursos'] ?></td>
            <td>$<?= number_format((float) $fila['ganancias'], 2) ?> MXN</td>
            <td><?= (int) $fila['inscripciones'] ?></td>
            <td><?= $fila['inscripciones'] ? round(100 * (int) $fila['terminados'] / (int) $fila['inscripciones']) : 0 ?>%</td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<script>document.getElementById('filtrarRol').addEventListener('change', function () { document.querySelectorAll('[data-rol]').forEach(f => f.hidden = this.value && f.dataset.rol !== this.value) })</script>
<?php finPortal(); ?>