<?php
function e($valor): string
{
  return htmlspecialchars(is_scalar($valor) ? (string) $valor : '', ENT_QUOTES, 'UTF-8');
}
function foto($valor): string
{
  if (!$valor)
    return url('/images/placeholder-curso.svg');
  if (filter_var($valor, FILTER_VALIDATE_URL))
    return $valor;
  try {
    return (new \Services\FirebaseStorage())->getUrl($valor);
  } catch (\Throwable $error) {
    error_log($error->getMessage());
    return url('/images/placeholder-curso.svg');
  }
}
function inicioPortal(string $titulo, ?array $aviso = null): void
{ ?>
  <!doctype html>
  <html lang="es">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | Kdemy</title>
    <link rel="stylesheet" href="<?= e(url('/css/portal.css')) ?>">
    <link rel="stylesheet" href="<?= e(url('/css/navbar.css')) ?>">
    <?php if ($titulo === 'Certificado'): ?>
      <link rel="stylesheet" href="<?= e(url('/css/certificado.css')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= e(url('/css/footer.css')) ?>">
  </head>

  <body>
    <header class="barra">
      <a class="marca" href="<?= e(url('/')) ?>">
        <span aria-hidden="true">🎓</span> Kdemy
      </a>
      <nav>
        <a href="<?= e(url('/')) ?>">Inicio</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="<?= e(url('/' . ($_SESSION['rol'] === 'administrador' ? 'admin' : $_SESSION['rol']))) ?>">Mi panel</a>
          <a href="<?= e(url('/mensajes')) ?>">Mensajes</a>
          <a href="<?= e(url('/perfil')) ?>">Perfil</a>
          <form action="<?= e(url('/logout')) ?>" method="post">
            <button type="submit" class="nav-auth">Salir</button>
          </form>
        <?php else: ?><a class="nav-auth" href="<?= e(url('/login')) ?>">Ingresar</a><?php endif; ?>
      </nav>
    </header>
    <main class="contenedor">
      <h1><?= e($titulo) ?></h1>
      <?php if ($aviso): ?>
        <div id="aviso" data-tipo="<?= e($aviso[0]) ?>" data-texto="<?= e($aviso[1]) ?>" hidden></div><?php endif; ?>
    <?php }
function finPortal(): void
{ ?>
    </main><?php require base_path('views/partials/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= e(url('/js/notifications.js')) ?>"></script>
    <script>
      const aviso = document.getElementById('aviso');
      if (aviso) {
        if (window.KdemyNotifications) {
          KdemyNotifications.toast({
            icon: aviso.dataset.tipo,
            title: aviso.dataset.texto
          });
        } else {
          console.log(aviso.dataset.texto);
        }
      }
    </script>
    <script>
      document.querySelectorAll('form[data-confirmar]').forEach(form => form.addEventListener('submit', async event => {
        if (form.dataset.aprobado) return;
        event.preventDefault();
        let aceptar = false;
        if (window.Swal) {
          const respuesta = await Swal.fire({
            title: form.dataset.confirmar,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
          });
          aceptar = respuesta.isConfirmed
        } else aceptar = confirm(form.dataset.confirmar);
        if (aceptar) {
          form.dataset.aprobado = '1';
          form.requestSubmit()
        }
      }));
    </script>
  </body>

  </html><?php }
function csrfPortal(): void
{ ?><input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
<?php }
function fechaPortal($valor): string
{
  if (!$valor)
    return '—';
  $fecha = strtotime($valor);
  $meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
  return date('d', $fecha) . ' ' . $meses[(int) date('n', $fecha) - 1] . ' ' . date('Y', $fecha);
}
