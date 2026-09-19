<?php

$action = $_POST['action'] ?? $_GET['action'] ?? 'home';

$db = $GLOBALS['db'] ?? null;
$controller = new UserController($db);
$controllerSiniestro = new SiniestroController($db);

switch ($action) {

  case 'registrarse':
    require 'views/registro.php';
    break;

  case 'logearse':
    require 'views/inicio_sesion.php';
    break;

  case 'mi_perfil':
    require 'views/mi_perfil.php';
    break;

  case 'detalle_curso':
    require 'views/detalle_curso.php';
    break;

  case 'mis_cursos':
    require 'views/mis_cursos.php';
    break;

  case 'panel_control':
    require 'views/panel_control.php';
    break;

  case 'alta_edicion_curso':
    require 'views/alta_edicion_curso.php';
    break;

  case 'reportes':
    require 'views/reportes.php';
    break;

  case 'dashboard_admin':
    require 'views/dashboard_admin.php';
    break;

  case 'resultado_busqueda':
    require 'views/resultado_busqueda.php';
    break;



  case 'payments':
    require 'views/payments.php';
    break;

  case 'guarantees_close':
    require 'views/guarantees_close.php';
    break;

  case 'claim_create':
    require 'views/claim_create.php';
    break;

  /*case 'claims_list':
        require 'views/claims_list.php';
        break;*/

  case 'claim_detail':
    //require 'views/claim_detail.php';
    $controllerSiniestro->claimDetail();
    break;

  case 'verMultimedia':
    $controllerSiniestro->verMultimedia();
    break;

  case 'logout':
    require 'views/logout.php';
    break;

  case '403':
    require 'views/403.php';
    break;

  case 'registrar':
    $controller->registrar($_POST, $_FILES);
    break;

  case 'login':
    $controller->login($_POST);
    break;

  case 'actualizarUsuario':
    $controller->actualizarUsuario($_POST, $_FILES);
    break;

  case 'editarPerfil':
    $controller->mostrarDatos();
    break;

  case 'buscarAsegurado':
    $controllerSiniestro->buscarAsegurado($_GET['texto']);
    break;

  case 'obtenerFotoUsuario':
    $controllerSiniestro->obtenerFotoUsuario($_GET['id']);
    break;

  case 'registrarSiniestro':
    $controllerSiniestro->registrarSiniestro($_POST, $_FILES);
    break;

  case 'claims_list':
    $controllerSiniestro->claimsList();
    break;

  case 'followup':
    // No necesitas crear $controller de nuevo, ya tienes $controllerSiniestro arriba
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $controllerSiniestro->registrarComentario($_POST);
    } else {
      $controllerSiniestro->followup($_GET['id'] ?? null);
    }
    break;


  default:
    require 'views/home.php';
}
