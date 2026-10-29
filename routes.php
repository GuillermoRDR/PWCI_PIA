<?php

$router->get('/', 'controllers/main.php');

// Ruta API (Buscar como proteger api)

$router->get('/register', 'controllers/auth/register.php');
$router->post('/register', 'controllers/auth/register.php');

$router->get('/login', 'controllers/auth/login.php');
$router->post('/login', 'controllers/auth/login.php');

$router->post('/logout', 'controllers/auth/logout.php')->only('auth');

$router->get('/admin', 'controllers/main.php')->only('admin');
$router->get('/profesor', 'controllers/main.php')->only('profesor');
$router->get('/alumno', 'controllers/main.php')->only('alumno');