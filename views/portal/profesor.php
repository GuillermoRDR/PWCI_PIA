<?php require base_path('views/portal/layout.php');
inicioPortal('Panel del profesor', $aviso); ?>
<div class="acciones"><a class="boton" href="<?= e(url('/curso-nuevo')) ?>">Crear curso</a><a class="boton secundario"
    href="<?= e(url('/mensajes')) ?>">Mensajes</a></div>
<section class="panel">
  <h2>Mis cursos</h2><?php if (!$cursos): ?>
    <p class="vacio">Aún no has creado cursos.</p><?php else: ?>
    <div class="grid">
      <?php foreach ($cursos as $curso): ?>
        <article class="tarjeta"><img src="<?= e(foto($curso['imagen_url'])) ?>"
            onerror="this.src='<?= e(url('/images/placeholder-curso.svg')) ?>'" alt="Portada">
          <div>
            <h3><?= e($curso['titulo']) ?></h3>
            <p><?= (int) $curso['niveles_count'] ?> niveles · <?= (int) $curso['alumnos_count'] ?> alumnos ·
              <?= e($curso['estado']) ?></p>
            <div class="acciones"><a class="boton"
                href="<?= e(url('/curso-editar?id=' . (int) $curso['curso_id'])) ?>">Editar</a>
              <form method="post" action="<?= e(url('/curso-baja')) ?>" data-confirmar="¿Dar de baja este curso?">
                <?php csrfPortal(); ?><input type="hidden" name="curso_id" value="<?= (int) $curso['curso_id'] ?>"><button
                  type="submit">Dar de baja</button></form>
            </div>
          </div>
        </article><?php endforeach; ?>
    </div><?php endif; ?>
</section>
<section class="panel">
  <h2>Reporte de ventas</h2>
  <form method="get" action="<?= e(url('/profesor')) ?>" class="grid">
    <div><label for="desde">Creado desde</label><input id="desde" type="date" name="desde"
        value="<?= e($_GET['desde'] ?? '') ?>"></div>
    <div><label for="hasta">Creado hasta</label><input id="hasta" type="date" name="hasta"
        value="<?= e($_GET['hasta'] ?? '') ?>"></div>
    <div><label for="cat">Categoría</label><select id="cat" name="categoria_id">
        <option value="">Todas</option><?php foreach ($categorias as $categoria): ?>
          <option value="<?= (int) $categoria['categoria_id'] ?>" <?= (string) ($_GET['categoria_id'] ?? '') === (string) $categoria['categoria_id'] ? 'selected' : '' ?>><?= e($categoria['nombre']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label for="activos">Estado</label><select id="activos" name="activos">
        <option value="">Todos</option>
        <option value="1" <?= ($_GET['activos'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
      </select></div>
    <div><button>Filtrar</button></div>
  </form>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Curso</th>
          <th>Creación</th>
          <th>Estado</th>
          <th>Alumnos</th>
          <th>Avance promedio</th>
          <th>Ingresos</th>
          <th>Detalle</th>
        </tr>
      </thead>
      <tbody><?php $total = 0;
      foreach ($ventas as $venta):
        $total += (float) $venta['ingresos']; ?>
          <tr>
            <td><?= e($venta['titulo']) ?></td>
            <td><?= e(fechaPortal($venta['creado_en'])) ?></td>
            <td><?= e($venta['estado']) ?></td>
            <td><?= (int) $venta['alumnos'] ?></td>
            <td><?= number_format((float) $venta['avance_promedio'], 1) ?> niveles</td>
            <td>$<?= number_format((float) $venta['ingresos'], 2) ?> MXN</td>
            <td><a href="<?= e(url('/ventas-detalle?id=' . (int) $venta['curso_id'])) ?>">Ver</a></td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <h3>Total: $<?= number_format($total, 2) ?> MXN</h3>
</section>
<section class="panel">
  <h2>Ingresos por forma de pago de todos los cursos</h2>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Método</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody><?php foreach ($metodos as $metodo): ?>
          <tr>
            <td><?= e($metodo['metodo']) ?></td>
            <td>$<?= number_format((float) $metodo['total'], 2) ?> MXN</td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php finPortal(); ?>