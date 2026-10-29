<header>
  <a href="<?= url('/') ?>" class="logo">
    <i class="fa-solid fa-graduation-cap"></i>
    Kdemy
  </a>

  <nav class="nav-links">

    <?php if (!empty($_SESSION['user_id'])): ?>

      <span>
        <?= htmlspecialchars(
          $_SESSION['nombre'] ?? 'Usuario',
          ENT_QUOTES,
          'UTF-8'
        ) ?>
      </span>

      <?php if ($_SESSION['rol'] === 'administrador'): ?>
        <a href="<?= url('/admin') ?>" class="btn btn-secondary">
          Panel administrador
        </a>
      <?php elseif ($_SESSION['rol'] === 'profesor'): ?>
        <a href="<?= url('/profesor') ?>" class="btn btn-secondary">
          Panel profesor
        </a>
      <?php elseif ($_SESSION['rol'] === 'alumno'): ?>
        <a href="<?= url('/alumno') ?>" class="btn btn-secondary">
          Mis cursos
        </a>
      <?php endif; ?>

      <form action="<?= url('/logout') ?>" method="POST">
        <button type="submit" class="btn btn-primary">
          Cerrar sesión
        </button>
      </form>

    <?php else: ?>

      <a href="<?= url('/login') ?>" class="btn btn-primary">
        Iniciar sesión
      </a>

      <a href="<?= url('/register') ?>" class="btn btn-primary">
        Registrarse
      </a>

    <?php endif; ?>

  </nav>
</header>