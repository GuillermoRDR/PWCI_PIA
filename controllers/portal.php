<?php

use Models\Portal;

$portal = new Portal();
$usuarioId = (int) $_SESSION['user_id'];
$rol = $_SESSION['rol'];
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$accion = basename($ruta);
$esPost = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($accion === 'recurso') {
  try {
    redirect($portal->abrirRecurso($usuarioId, (int) ($_GET['id'] ?? 0)));
  } catch (\Throwable $e) {
    abort(404);
  }
}
if ($accion === 'nivel-video') {
  if ($rol !== 'alumno') {
    http_response_code(403);
    exit('Acceso denegado.');
  }
  try {
    redirect($portal->abrirNivel($usuarioId, (int) ($_GET['id'] ?? 0)));
  } catch (\Throwable $e) {
    abort(404);
  }
}

if ($esPost) {
  // PHP vacía $_POST y $_FILES cuando la solicitud supera post_max_size.
  $limitePost = ini_get('post_max_size');
  $unidad = strtolower(substr((string) $limitePost, -1));
  $numero = (float) $limitePost;
  $multiplicador = ['g' => 1024 * 1024 * 1024, 'm' => 1024 * 1024, 'k' => 1024][$unidad] ?? 1;
  $maximoPost = (int) ($numero * $multiplicador);
  if ($maximoPost > 0 && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $maximoPost) {
    http_response_code(413);
    $_SESSION['aviso'] = ['error', 'Los archivos superan el límite de carga del servidor (post_max_size: ' . $limitePost . '). Aumenta post_max_size y upload_max_filesize en el php.ini de Apache.'];
    redirect(url($accion === 'curso-guardar' ? '/curso-nuevo' : '/'));
  }
  foreach ($_POST as $llave => $valor)
    if ($llave !== 'niveles' && !is_scalar($valor))
      unset($_POST[$llave]);
  if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string) ($_POST['csrf_token'] ?? ''))) {
    http_response_code(403);
    $_SESSION['aviso'] = ['error', 'Solicitud inválida.'];
    redirect(url('/'));
  }
  $permisos = [
    'perfil-guardar' => ['administrador', 'profesor', 'alumno'],
    'curso-guardar' => ['profesor'],
    'curso-baja' => ['profesor'],
    'inscribir' => ['alumno'],
    'solicitar-nivel' => ['alumno'],
    'nivel-completar' => ['alumno'],
    'nivel-abrir' => ['alumno'],
    'comentar' => ['alumno'],
    'categoria-guardar' => ['administrador'],
    'categoria-estado' => ['administrador'],
    'usuario-estado' => ['administrador'],
    'comentario-moderar' => ['administrador'],
    'pedido-confirmar' => ['administrador'],
    'mensaje-enviar' => ['administrador', 'profesor', 'alumno'],
  ];
  if (!in_array($rol, $permisos[$accion] ?? [], true)) {
    http_response_code(403);
    exit('Acceso denegado.');
  }

  try {
    switch ($accion) {
      case 'perfil-guardar':
        $foto = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
          $archivo = $_FILES['avatar'];
          if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024)
            throw new \RuntimeException('Imagen inválida.');
          $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
          $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
          if (!isset($tipos[$mime]) || !getimagesize($archivo['tmp_name']))
            throw new \RuntimeException('Imagen inválida.');
          $foto = (new \Services\FirebaseStorage())->uploadAvatar($archivo['tmp_name'], $tipos[$mime], $mime);
        }
        $portal->perfil($usuarioId, trim((string) ($_POST['nombre'] ?? '')), trim((string) ($_POST['correo'] ?? '')), (string) ($_POST['genero'] ?? ''), (string) ($_POST['fecha_nacimiento'] ?? ''), $foto);
        break;
      case 'curso-guardar':
        $uploads = null;
        $coverPath = null;
        if (isset($_FILES['imagen_curso']) && $_FILES['imagen_curso']['error'] !== UPLOAD_ERR_NO_FILE) {
          $archivo = $_FILES['imagen_curso'];
          if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 3 * 1024 * 1024)
            throw new \RuntimeException('Imagen inválida.');
          $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
          $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
          if (!isset($tipos[$mime]) || !getimagesize($archivo['tmp_name']))
            throw new \RuntimeException('Imagen inválida.');
          $coverPath = (new \Services\FirebaseStorage())->uploadCourseImage($archivo['tmp_name'], $tipos[$mime], $mime);
          $_POST['imagen_url'] = $coverPath;
        }
        if (!empty($_POST['niveles']) && is_array($_POST['niveles'])) {
          foreach ($_POST['niveles'] as &$nivelEntrada) {
            if (!is_array($nivelEntrada))
              throw new \RuntimeException('Nivel inválido.');
            unset($nivelEntrada['recursos']);
            $videoEntrada = (string) ($nivelEntrada['video_url'] ?? '');
            if ($videoEntrada !== '' && !preg_match('#^https?://#i', $videoEntrada))
              throw new \RuntimeException('Video inválido.');
          }
          unset($nivelEntrada);
          $uploads = new \Services\CourseUploads();
          $uploads->process($_POST['niveles'], $_FILES);
        }
        $portal->guardarCurso($usuarioId, $_POST);
        break;
      case 'curso-baja':
        $portal->bajaCurso($usuarioId, (int) ($_POST['curso_id'] ?? 0));
        break;
      case 'inscribir':
        if (($_POST['metodo'] ?? '') === 'paypal')
          throw new \RuntimeException('Usa el botón de PayPal para pagar.');
        $portal->solicitarCurso($usuarioId, (int) ($_POST['curso_id'] ?? 0), (string) ($_POST['metodo'] ?? ''));
        break;
      case 'solicitar-nivel':
        if (($_POST['metodo'] ?? '') === 'paypal')
          throw new \RuntimeException('Usa el botón de PayPal para pagar.');
        $portal->solicitarNivel($usuarioId, (int) ($_POST['curso_id'] ?? 0), (int) ($_POST['nivel_id'] ?? 0), (string) ($_POST['metodo'] ?? ''));
        break;
      case 'nivel-completar':
        $portal->completarNivel($usuarioId, (int) ($_POST['nivel_id'] ?? 0));
        break;
      case 'nivel-abrir':
        redirect($portal->abrirNivel($usuarioId, (int) ($_POST['nivel_id'] ?? 0)));
      case 'comentar':
        $portal->comentar($usuarioId, (int) ($_POST['curso_id'] ?? 0), (int) ($_POST['calificacion'] ?? 0), trim((string) ($_POST['comentario'] ?? '')));
        break;
      case 'categoria-guardar':
        $portal->guardarCategoria($usuarioId, trim((string) ($_POST['nombre'] ?? '')), trim((string) ($_POST['descripcion'] ?? '')), (int) ($_POST['categoria_id'] ?? 0));
        break;
      case 'categoria-estado':
        $portal->estadoCategoria((int) ($_POST['categoria_id'] ?? 0), (string) ($_POST['estado'] ?? ''));
        break;
      case 'usuario-estado':
        $portal->estadoUsuario($usuarioId, (int) ($_POST['usuario_id'] ?? 0), (string) ($_POST['estado'] ?? ''));
        break;
      case 'comentario-moderar':
        $portal->moderar($usuarioId, (int) ($_POST['comentario_id'] ?? 0), trim((string) ($_POST['motivo'] ?? '')));
        break;
      case 'pedido-confirmar':
        $portal->confirmarPedido((int) ($_POST['pedido_id'] ?? 0));
        break;
      case 'mensaje-enviar':
        $portal->enviarMensaje($usuarioId, (int) ($_POST['destinatario_id'] ?? 0), trim((string) ($_POST['contenido'] ?? '')));
        break;
    }
    $_SESSION['aviso'] = ['success', 'Listo.'];
  } catch (\RuntimeException $e) {
    if (isset($uploads) && $uploads)
      $uploads->cleanup();
    if (!empty($coverPath))
      try {
        (new \Services\FirebaseStorage())->delete($coverPath);
      } catch (\Throwable $cleanupError) {
        error_log($cleanupError->getMessage());
      }
    http_response_code(400);
    $_SESSION['aviso'] = ['error', $accion === 'curso-guardar' ? $e->getMessage() : 'Revisa los datos.'];
  } catch (\Throwable $e) {
    if (isset($uploads) && $uploads)
      $uploads->cleanup();
    if (!empty($coverPath))
      try {
        (new \Services\FirebaseStorage())->delete($coverPath);
      } catch (\Throwable $cleanupError) {
        error_log($cleanupError->getMessage());
      }
    error_log($e->getMessage());
    http_response_code(500);
    $_SESSION['aviso'] = ['error', 'No se pudo guardar.'];
  }
  $destino = match ($rol) {
    'administrador' => '/admin',
    'profesor' => '/profesor',
    default => '/alumno',
  };
  if (in_array($accion, ['inscribir', 'solicitar-nivel', 'nivel-abrir', 'nivel-completar', 'comentar'], true)) {
    $curso = (int) ($_POST['curso_id'] ?? 0);
    if ($curso > 0)
      $destino = '/curso?id=' . $curso;
  }
  redirect(url($destino));
}

$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
foreach ($_GET as $llave => $valor)
  if (!is_scalar($valor))
    unset($_GET[$llave]);
$filtros = [];
foreach (['desde', 'hasta'] as $campo) {
  $valor = $_GET[$campo] ?? null;
  if (is_string($valor) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    if ($fecha && $fecha->format('Y-m-d') === $valor)
      $filtros[$campo] = $valor;
  }
}
if (isset($_GET['categoria_id']) && is_string($_GET['categoria_id']) && ctype_digit($_GET['categoria_id']))
  $filtros['categoria_id'] = (int) $_GET['categoria_id'];
foreach (['activos', 'terminados'] as $campo)
  if (($_GET[$campo] ?? '') === '1')
    $filtros[$campo] = '1';
$aviso = $_SESSION['aviso'] ?? null;
unset($_SESSION['aviso']);
if ($aviso)
  $_SESSION['aviso'] = $aviso;
$perfil = $portal->usuario($usuarioId);

if ($accion === 'certificado') {
  if ($rol !== 'alumno') {
    http_response_code(403);
    exit('Acceso denegado.');
  }
  $certificado = $portal->certificado($usuarioId, (int) ($_GET['curso_id'] ?? 0));
  if (!$certificado)
    abort(404);
  view('portal/certificado.php', compact('certificado'));
  return;
}
if ($accion === 'reportes') {
  view('reportes.php', ['reporte' => $portal->reporteUsuarios()]);
  return;
}

if ($accion === 'curso-editar') {
  if ($rol !== 'profesor') {
    http_response_code(403);
    exit('Acceso denegado.');
  }
  $curso = $portal->cursoProfesor($usuarioId, (int) ($_GET['id'] ?? 0));
  if (!$curso)
    abort(404);
  view('alta_edicion_curso.php', ['curso' => $curso, 'categorias' => $portal->categorias(), 'nivelesExistentes' => (new \Models\CursoCatalogo())->nivelesActivos((int) $curso['curso_id'])]);
  return;
}

if ($accion === 'curso-nuevo') {
  if ($rol !== 'profesor') {
    http_response_code(403);
    exit('Acceso denegado.');
  }
  view('alta_edicion_curso.php', ['curso' => null, 'categorias' => $portal->categorias()]);
  return;
}

if ($accion === 'ventas-detalle') {
  if ($rol !== 'profesor') {
    http_response_code(403);
    exit('Acceso denegado.');
  }
  $curso = $portal->cursoParaReporte($usuarioId, (int) ($_GET['id'] ?? 0));
  if (!$curso)
    abort(404);
  view('portal/ventas-detalle.php', ['curso' => $curso, 'filas' => $portal->detalleVentas($usuarioId, (int) $curso['curso_id'])]);
  return;
}

if ($accion === 'mensajes') {
  view('portal/mensajes.php', ['mensajes' => $portal->mensajes($usuarioId), 'usuarios' => $portal->usuarios(), 'aviso' => $aviso]);
  return;
}

if ($accion === 'perfil') {
  view('mi_perfil.php', compact('perfil', 'aviso'));
  return;
}

if ($accion === 'profesor' && $rol === 'profesor') {
  view('panel_control.php', [
    'cursos' => $portal->cursosProfesor($usuarioId),
    'ventas' => $portal->ventas($usuarioId, $filtros),
    'metodos' => $portal->ventasPorMetodo($usuarioId),
    'filtros' => $filtros,
    'categorias' => $portal->categorias(),
    'aviso' => $aviso,
  ]);
  return;
}

if ($accion === 'alumno' && $rol === 'alumno') {
  view('mis_cursos.php', ['cursos' => $portal->misCursos($usuarioId, $filtros), 'categorias' => $portal->categorias(), 'filtros' => $filtros, 'aviso' => $aviso]);
  return;
}

if ($accion === 'admin' && $rol === 'administrador') {
  view('dashboard_admin.php', [
    'usuarios' => $portal->usuarios(),
    'reporte' => $portal->reporteUsuarios(),
    'categorias' => $portal->categorias(),
    'comentarios' => $portal->comentarios(),
    'pedidos' => $portal->pedidosPendientes(),
    'aviso' => $aviso,
  ]);
  return;
}

http_response_code(403);
exit('Acceso denegado.');
