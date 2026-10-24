<?php

$router->get('/', 'controllers/main.php');

// Ruta API (Buscar como proteger api)

$router->get('/login', 'controllers/auth/login.php');
$router->post('/login', 'controllers/auth/login.php');

$router->get('/register', 'controllers/auth/register.php');
$router->post('/register', 'controllers/auth/register.php');