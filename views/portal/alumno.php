<?php require base_path('views/portal/layout.php');
inicioPortal('Mi Kardex', $aviso); ?>
<div class="acciones"><a class="boton" href="<?= e(url('/buscar')) ?>">Buscar cursos</a><a class="boton secundario"
    href="<?= e(url('/mensajes')) ?>">Mensajes</a></div>
<section class="panel">
  <h2>Filtros</h2>
  <form action="<?= e(url('/alumno')) ?>" method="get" class="grid">
    <div><label for="desde">Inscripción desde</label><input id="desde" type="date" name="desde"
        value="<?= e($_GET['desde'] ?? '') ?>"></div>
    <div><label for="hasta">Inscripción hasta</label><input id="hasta" type="date" name="hasta"
        value="<?= e($_GET['hasta'] ?? '') ?>"></div>
    <div><label for="cat">Categoría</label><select id="cat" name="categoria_id">
        <option value="">Todas</option><?php foreach ($categorias as $categoria): ?>
          <option value="<?= (int) $categoria['categoria_id'] ?>" <?= (string) ($_GET['categoria_id'] ?? '') === (string) $categoria['categoria_id'] ? 'selected' : '' ?>><?= e($categoria['nombre']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label for="terminados">Terminación</label><select id="terminados" name="terminados">
        <option value="">Todos</option>
        <option value="1" <?= ($_GET['terminados'] ?? '') === '1' ? 'selected' : '' ?>>Terminados</option>
      </select></div>
    <div><label for="activos">Cursos</label><select id="activos" name="activos">
        <option value="">Todos</option>
        <option value="1" <?= ($_GET['activos'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
      </select></div>
    <div><button>Filtrar</button></div>
  </form>
</section>
<section class="panel">
  <h2>Inscripciones</h2><?php if (!$cursos): ?>
    <p class="vacio">No hay cursos con estos filtros.</p><?php else: ?>
    <div class="desplazar">
      <table class="tabla">
        <thead>
          <tr>
            <th>Curso</th>
            <th>Inscripción</th>
            <th>Última actividad</th>
            <th>Terminación</th>
            <th>Progreso</th>
            <th>Estado</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody><?php foreach ($cursos as $curso): ?>
            <tr>
              <td><?= e($curso['titulo']) ?></td>
              <td><?= e(fechaPortal($curso['inscrito_en'])) ?></td>
              <td><?= e(fechaPortal($curso['ultima_actividad'])) ?></td>
              <td><?= e(fechaPortal($curso['completada_en'])) ?></td>
              <td><?= (int) $curso['niveles_completados'] ?> / <?= (int) $curso['total_niveles'] ?></td>
              <td><?= e($curso['inscripcion_estado']) ?> · <?= e($curso['curso_estado']) ?></td>
              <td><?php if ($curso['curso_estado'] === 'activo'): ?><a
                    href="<?= e(url('/curso?id=' . (int) $curso['curso_id'])) ?>">Continuar</a><?php endif; ?><?php if ($curso['completada_en']): ?>
                  <a href="<?= e(url('/certificado?curso_id=' . (int) $curso['curso_id'])) ?>">Certificado</a><?php endif; ?>
              </td>
            </tr><?php endforeach; ?>
        </tbody>
      </table>
    </div><?php endif; ?>
</section><?php finPortal(); ?>