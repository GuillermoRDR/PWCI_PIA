<?php

$router->get('/', 'controllers/main.php');
$router->get('/curso', 'controllers/course/show.php');
$router->get('/buscar', 'controllers/course/search.php');

$router->get('/register', 'controllers/auth/register.php');
$router->post('/register', 'controllers/auth/register.php');

$router->get('/login', 'controllers/auth/login.php');
$router->post('/login', 'controllers/auth/login.php');

$router->post('/logout', 'controllers/auth/logout.php')->only('auth');

$router->get('/admin', 'controllers/portal.php')->only('admin');
$router->get('/reportes', 'controllers/portal.php')->only('admin');
$router->get('/profesor', 'controllers/portal.php')->only('profesor');
$router->get('/alumno', 'controllers/portal.php')->only('alumno');
$router->get('/recurso', 'controllers/portal.php')->only('alumno');
$router->get('/nivel-video', 'controllers/portal.php')->only('alumno');
$router->post('/paypal-iniciar', 'controllers/paypal.php')->only('alumno');
$router->get('/paypal-retorno', 'controllers/paypal.php')->only('alumno');
$router->get('/paypal-cancelar', 'controllers/paypal.php')->only('alumno');
foreach (['perfil', 'mensajes', 'certificado', 'curso-nuevo', 'curso-editar', 'ventas-detalle'] as $pagina) {
  $router->get('/' . $pagina, 'controllers/portal.php')->only('auth');
}
foreach (['perfil-guardar', 'curso-guardar', 'curso-baja', 'inscribir', 'solicitar-nivel', 'nivel-abrir', 'nivel-completar', 'comentar', 'categoria-guardar', 'categoria-estado', 'usuario-estado', 'comentario-moderar', 'pedido-confirmar', 'mensaje-enviar'] as $accion) {
  $router->post('/' . $accion, 'controllers/portal.php')->only('auth');
}
