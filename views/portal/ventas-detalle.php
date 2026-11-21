<?php require base_path('views/portal/layout.php');
inicioPortal('Ventas: ' . $curso['titulo']); ?>
<section class="panel">
  <h2>Alumnos inscritos</h2>
  <div class="desplazar">
    <table class="tabla">
      <thead>
        <tr>
          <th>Alumno</th>
          <th>Inscripción</th>
          <th>Niveles completados</th>
          <th>Pagado</th>
          <th>Método</th>
        </tr>
      </thead>
      <tbody><?php $total = 0;
      foreach ($filas as $fila):
        $total += (float) $fila['pagado']; ?>
          <tr>
            <td><?= e($fila['nombre_completo']) ?></td>
            <td><?= e(fechaPortal($fila['inscrito_en'])) ?></td>
            <td><?= (int) $fila['niveles_completados'] ?></td>
            <td>$<?= number_format((float) $fila['pagado'], 2) ?> MXN</td>
            <td><?= e($fila['metodo'] ?? 'Gratis') ?></td>
          </tr><?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <h3>Total: $<?= number_format($total, 2) ?> MXN</h3>
</section><?php finPortal(); ?>