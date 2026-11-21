<?php

use Models\Portal;
use Services\PayPalSandbox;

if (($_SESSION['rol'] ?? '') !== 'alumno') {
  http_response_code(403);
  exit('Acceso denegado.');
}
$portal = new Portal();
$alumno = (int) $_SESSION['user_id'];
$path = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$curso = (int) ($_POST['curso_id'] ?? $_SESSION['paypal_curso'] ?? 0);
$destino = url('/curso?id=' . $curso);

try {
  if ($path === 'paypal-iniciar') {
    if (!hash_equals((string) ($_SESSION['csrf_token'] ?? ''), (string) ($_POST['csrf_token'] ?? '')) || empty($_SESSION['csrf_token']))
      throw new RuntimeException('Solicitud inválida.');
    $nivel = (int) ($_POST['nivel_id'] ?? 0);
    if ($curso < 1 || $nivel < 0)
      throw new RuntimeException('Curso inválido.');
    $paypal = new PayPalSandbox();
    try {
      if ($nivel)
        $portal->solicitarNivel($alumno, $curso, $nivel, 'paypal');
      else
        $portal->solicitarCurso($alumno, $curso, 'paypal');
    } catch (RuntimeException $e) {
      if ($e->getMessage() !== 'Ya existe una solicitud.')
        throw $e;
    }
    $pago = $portal->pagoPayPalPendiente($alumno, $curso, $nivel);
    if (!empty($pago['referencia_externa']))
      throw new RuntimeException('Ya hay una orden pendiente. Espera a que concluya o contacta al administrador.');
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    if (!preg_match('/^(localhost|127\.0\.0\.1)(:\d{1,5})?$/i', $host))
      throw new RuntimeException('Configura el dominio público permitido antes de usar PayPal fuera de localhost.');
    $base = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $host;
    $state = bin2hex(random_bytes(24));
    $orden = $paypal->create((int) $pago['pedido_id'], (string) $pago['total'], (string) $pago['moneda'], $base . url('/paypal-retorno?state=' . $state), $base . url('/paypal-cancelar?state=' . $state));
    $id = (string) ($orden['id'] ?? '');
    $enlace = '';
    foreach (($orden['links'] ?? []) as $link)
      if (in_array($link['rel'] ?? '', ['payer-action', 'approve'], true))
        $enlace = $link['href'] ?? '';
    if (!preg_match('/^[A-Z0-9]{10,30}$/i', $id) || !str_starts_with($enlace, 'https://www.sandbox.paypal.com/'))
      throw new RuntimeException('PayPal no devolvió un enlace de aprobación válido.');
    $portal->vincularPayPal($alumno, (int) $pago['pedido_id'], $id);
    $_SESSION['paypal_state'] = $state;
    $_SESSION['paypal_order'] = $id;
    $_SESSION['paypal_curso'] = $curso;
    redirect($enlace);
  }
  if (!hash_equals((string) ($_SESSION['paypal_state'] ?? ''), (string) ($_GET['state'] ?? '')) || empty($_SESSION['paypal_order']))
    throw new RuntimeException('La sesión de pago expiró.');
  if ($path === 'paypal-cancelar') {
    $portal->cancelarPayPal($alumno, $_SESSION['paypal_order']);
    unset($_SESSION['paypal_state'], $_SESSION['paypal_order'], $_SESSION['paypal_curso']);
    $_SESSION['aviso'] = ['info', 'Cancelaste la aprobación en PayPal.'];
    redirect($destino);
  }
  $id = (string) ($_GET['token'] ?? '');
  if (!hash_equals($_SESSION['paypal_order'], $id))
    throw new RuntimeException('La orden de PayPal no coincide.');
  $captura = (new PayPalSandbox())->capture($id);
  $portal->capturarPayPal($alumno, $id, $captura);
  unset($_SESSION['paypal_state'], $_SESSION['paypal_order'], $_SESSION['paypal_curso']);
  $_SESSION['aviso'] = ['success', 'Pago confirmado. Ya tienes acceso al contenido.'];
} catch (Throwable $e) {
  error_log('PayPal Sandbox: ' . $e->getMessage());
  $_SESSION['aviso'] = ['error', $e instanceof RuntimeException ? $e->getMessage() : 'No se pudo completar el pago.'];
}
redirect($destino);
