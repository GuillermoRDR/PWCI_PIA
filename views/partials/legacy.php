<?php
require_once base_path('views/portal/layout.php');
function legacyStart(string $title, string $css): void
{ ?>
  <!doctype html>
  <html lang="es">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | Kdemy</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e(url('/css/' . $css . '.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('/css/legacy-connect.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('/css/navbar.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('/css/footer.css')) ?>">
  </head>

  <body>
    <header class="site-header"><a href="<?= e(url('/')) ?>" class="logo"><i class="fa-solid fa-graduation-cap"></i>
        Kdemy</a>
      <nav class="nav-links"><a class="btn-link"
          href="<?= e(url('/')) ?>">Inicio</a><?php if (isset($_SESSION['user_id'])): ?><a class="btn-link"
            href="<?= e(url('/' . ($_SESSION['rol'] === 'administrador' ? 'admin' : $_SESSION['rol']))) ?>">Mi panel</a><a
            class="btn-link" href="<?= e(url('/mensajes')) ?>">Mensajes</a><a class="btn-link"
            href="<?= e(url('/perfil')) ?>">Perfil</a>
          <form method="post" action="<?= e(url('/logout')) ?>"><button type="submit" class="nav-auth">Salir</button></form>
        <?php else: ?><a class="btn-link nav-auth" href="<?= e(url('/login')) ?>">Ingresar</a><a class="btn-link"
            href="<?= e(url('/register')) ?>">Registrarse</a><?php endif; ?>
      </nav>
    </header>
  <?php }
function legacyEnd(): void
{
  $notice = $_SESSION['aviso'] ?? null;
  unset($_SESSION['aviso']); ?>
    <?php require base_path('views/partials/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($notice): ?>Swal.fire({
            icon: <?= json_encode($notice[0]) ?>,
            text: <?= json_encode($notice[1]) ?>
          });
      <?php endif; ?>
      document.querySelectorAll('form[data-confirmar]').forEach(f => f.addEventListener('submit', async e => {
        if (f.dataset.aprobado) return;
        e.preventDefault();
        const r = await Swal.fire({
          title: f.dataset.confirmar,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Confirmar',
          cancelButtonText: 'Cancelar'
        });
        if (r.isConfirmed) {
          f.dataset.aprobado = '1';
          f.requestSubmit()
        }
      }));
    </script>
  </body>

  </html>
<?php }
