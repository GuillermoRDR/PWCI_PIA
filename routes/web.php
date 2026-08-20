<?php

$action = $_POST['action'] ?? $_GET['action'] ?? 'home';

$controller = new UserController($db);
$controllerSiniestro = new SiniestroController($db);

switch ($action) {

    case 'dashboard':
        require 'pages/dashboard.php';
        break;

    case 'registerPage':
        require 'pages/register.php';
        break;

    case 'loginPage':
        require 'pages/login.php';
        break;

    case 'approvals':
        require 'pages/approvals.php';
        break;
/*
    case 'followup':
        require 'pages/followup.php';
        break;*/
    
    case 'payments':
        require 'pages/payments.php';
        break;

    case 'guarantees_close':
        require 'pages/guarantees_close.php';
        break;

    case 'claim_create':
        require 'pages/claim_create.php';
        break;

    /*case 'claims_list':
        require 'pages/claims_list.php';
        break;*/

    case 'claim_detail':
        //require 'pages/claim_detail.php';
        $controllerSiniestro->claimDetail();
        break;

    case 'verMultimedia':
        $controllerSiniestro->verMultimedia();
        break;

    case 'logout':
        require 'pages/logout.php';
        break;

    case '403':
        require 'pages/403.php';
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
        require 'pages/login.php';
}