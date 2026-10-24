  <header>
    <a href="index.php" class="logo">
      <i class="fa-solid fa-graduation-cap"></i> Kdemy
    </a>

    <form href="index.php?action=resultado_busqueda" method="GET" class="search-bar">
      <input type="hidden" name="action" value="search">
      <input type="text" name="query" placeholder="Buscar curso activo..." required>
      <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>

    <nav class="nav-links">
      <?php if (isset($_SESSION['user_id'])): ?>

      <?php else: ?>
        <a href="index.php?action=dashboard_admin" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Panel de Control
        </a>
        <a href="index.php?action=panel_control" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <a href="index.php?action=reportes" class="btn btn-secondary">
          <i class="fa-solid fa-chart-line"></i> Reportes
        </a>
        <a href="index.php?action=mis_cursos" class="btn btn-secondary">
          <i class="fa-solid fa-book-open"></i> Mis Cursos
        </a>
        <a href="index.php?action=logearse" class="btn btn-primary">
          <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
        </a>
        <a href="index.php?action=mi_perfil" class="btn btn-secondary">
          <i class="fa-solid fa-user"></i> Mi Perfil
        </a>
        <a href="<?= url('/login') ?>" class="btn btn-primary">
          <i class="fa-solid fa-right-to-bracket "></i> Iniciar Sesión
        </a>
        <a href="<?= url('/register') ?>" class="btn btn-primary">
          <i class="fa-solid fa-user-plus"></i> Registrarse
        </a>
      <?php endif; ?>
    </nav>
  </header>