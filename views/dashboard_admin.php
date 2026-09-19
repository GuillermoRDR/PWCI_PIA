<?php
$usuarios = $usuarios ?? [
  ['id' => 1, 'username' => 'cmendoza', 'nombre' => 'Carlos Mendoza', 'rol' => 'Instructor', 'estatus' => 'activa'],
  ['id' => 2, 'username' => 'amartinez', 'nombre' => 'Ana Martínez', 'rol' => 'Estudiante', 'estatus' => 'activa'],
  ['id' => 3, 'username' => 'troller99', 'nombre' => 'Juan Pérez', 'rol' => 'Estudiante', 'estatus' => 'bloqueada']
];

$cursosPendientes = $cursosPendientes ?? [
  ['id' => 201, 'titulo' => 'Introducción a Machine Learning', 'instructor' => 'Ing. Carlos Mendoza', 'categoria' => 'Inteligencia Artificial', 'fecha' => '2026-02-18'],
  ['id' => 202, 'titulo' => 'Seguridad Web y Pentesting', 'instructor' => 'Lic. Roberto Gómez', 'categoria' => 'Ciberseguridad', 'fecha' => '2026-02-19']
];

$categorias = $categorias ?? [
  ['id' => 1, 'nombre' => 'Programación Web', 'cursos_count' => 12],
  ['id' => 2, 'nombre' => 'Diseño UI/UX', 'cursos_count' => 5],
  ['id' => 3, 'nombre' => 'Bases de Datos', 'cursos_count' => 8]
];

$comentariosReportados = $comentariosReportados ?? [
  [
    'id' => 501,
    'usuario' => 'Usuario_Anonimo',
    'curso' => 'Desarrollo Web Full Stack',
    'comentario' => 'Este curso es una porquería, el profesor no sabe nada.',
    'fecha' => '2026-02-15'
  ]
];

$error = $error ?? null;
$mensaje = $mensaje ?? null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Administrador - Portal de Cursos</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="views/css/dashboard_admin.css">
</head>

<body>
  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-user-shield"></i> Panel de Administración
    </a>
    <div class="nav-links">
      <a href="index.php?action=reportes" class="btn-link"><i class="fa-solid fa-chart-line"></i> Reportes</a>
      <a href="index.php?action=logout" class="btn-link"><i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión</a>
    </div>
  </header>

  <div class="container">
    <h2 class="page-title"><i class="fa-solid fa-gauge-high"></i> Menú Principal de Control</h2>

    <div class="admin-card">
      <h3><i class="fa-solid fa-users-gear"></i> Gestión de Usuarios Registrados</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre Completo</th>
              <th>Rol</th>
              <th>Estatus Actual</th>
              <th>Cambiar Estatus</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td>#<?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['rol']) ?></td>
                <td>
                  <span class="badge <?= $u['estatus'] === 'activa' ? 'badge-active' : 'badge-blocked' ?>">
                    <?= ucfirst($u['estatus']) ?>
                  </span>
                </td>
                <td>
                  <form action="index.php?action=change_user_status" method="POST" style="display: flex; gap: 8px;">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <select name="estatus" class="form-control" style="padding: 4px 8px; font-size: 0.85rem;">
                      <option value="activa" <?= $u['estatus'] === 'activa' ? 'selected' : '' ?>>Activa</option>
                      <option value="bloqueada" <?= $u['estatus'] === 'bloqueada' ? 'selected' : '' ?>>Bloqueada</option>
                    </select>
                    <button type="submit" class="btn-sm btn-primary">Guardar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Módulo 2: Cursos en Proceso de Publicación -->
    <div class="admin-card">
      <h3><i class="fa-solid fa-file-circle-check"></i> Cursos en Proceso de Publicación</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Título del Curso</th>
              <th>Instructor</th>
              <th>Categoría</th>
              <th>Fecha Solicitud</th>
              <th>Decisión / Publicación</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($cursosPendientes)): ?>
              <?php foreach ($cursosPendientes as $cp): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($cp['titulo']) ?></strong></td>
                  <td><?= htmlspecialchars($cp['instructor']) ?></td>
                  <td><?= htmlspecialchars($cp['categoria']) ?></td>
                  <td><?= htmlspecialchars($cp['fecha']) ?></td>
                  <td>
                    <div style="display: flex; gap: 8px;">
                      <a href="index.php?action=approve_course&id=<?= $cp['id'] ?>" class="btn-sm btn-approve">
                        <i class="fa-solid fa-check"></i> Publicar
                      </a>
                      <a href="index.php?action=reject_course&id=<?= $cp['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('¿Rechazar este curso?');">
                        <i class="fa-solid fa-xmark"></i> Rechazar
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; opacity: 0.8;">No hay cursos pendientes de aprobación.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Módulo 3: Categorías Registradas -->
    <div class="admin-card">
      <h3><i class="fa-solid fa-folder-tree"></i> Categorías Registradas</h3>

      <!-- Agregar Nueva Categoría -->
      <form action="index.php?action=add_category" method="POST" class="inline-form">
        <input type="text" name="nombre_categoria" class="form-control" placeholder="Nombre de la nueva categoría..." required style="flex: 1; max-width: 350px;">
        <button type="submit" class="btn-primary"><i class="fa-solid fa-plus"></i> Agregar Categoría</button>
      </form>

      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Categoría</th>
              <th>Cursos Asociados</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categorias as $cat): ?>
              <tr>
                <td>#<?= $cat['id'] ?></td>
                <td><strong><?= htmlspecialchars($cat['nombre']) ?></strong></td>
                <td><?= $cat['cursos_count'] ?> curso(s)</td>
                <td>
                  <a href="index.php?action=delete_category&id=<?= $cat['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('¿Deseas eliminar esta categoría?');">
                    <i class="fa-solid fa-trash"></i> Eliminar
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Módulo 4: Moderación de Comentarios Offensivos -->
    <div class="admin-card">
      <h3><i class="fa-solid fa-comment-slash"></i> Moderación de Comentarios</h3>
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Curso</th>
              <th>Comentario</th>
              <th>Fecha</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($comentariosReportados)): ?>
              <?php foreach ($comentariosReportados as $com): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($com['usuario']) ?></strong></td>
                  <td><?= htmlspecialchars($com['curso']) ?></td>
                  <td><em>"<?= htmlspecialchars($com['comentario']) ?>"</em></td>
                  <td><?= htmlspecialchars($com['fecha']) ?></td>
                  <td>
                    <button class="btn-sm btn-danger" onclick="openDeleteModal(<?= $com['id'] ?>)">
                      <i class="fa-solid fa-ban"></i> Eliminar Comentario
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" style="text-align: center; opacity: 0.8;">No hay comentarios pendientes de moderación.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Modal para especificar la causa de eliminación del comentario -->
  <div id="deleteCommentModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
      <h3 style="margin-bottom: 15px;"><i class="fa-solid fa-triangle-exclamation"></i> Eliminar Comentario Offensivo</h3>
      <form action="index.php?action=delete_comment_admin" method="POST">
        <input type="hidden" id="modal_comment_id" name="comment_id" value="">

        <div style="display: flex; flex-direction: column; gap: 6px; margin-bottom: 15px;">
          <label for="causa" style="font-weight: bold;">Causa / Motivo de Eliminación *</label>
          <textarea name="causa" id="causa" class="form-control" rows="3" placeholder="Ej. El comentario viola las normas de la comunidad al incluir lenguaje ofensivo." required></textarea>
        </div>

        <button type="submit" class="btn-sm btn-danger" style="width: 100%; justify-content: center; padding: 10px;">
          Confirmar Eliminación
        </button>
      </form>
    </div>
  </div>

  <script>
    function openDeleteModal(commentId) {
      document.getElementById('modal_comment_id').value = commentId;
      document.getElementById('deleteCommentModal').style.display = 'flex';
    }

    function closeDeleteModal() {
      document.getElementById('deleteCommentModal').style.display = 'none';
    }
  </script>

  <footer>
    <p>&copy; <?= date('Y') ?> Kdemy - Panel de Administración.</p>
  </footer>

</body>

</html>