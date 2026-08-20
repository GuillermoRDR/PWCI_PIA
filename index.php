<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once 'config/database.php';
require_once 'controllers/UserController.php';
require_once 'controllers/SiniestroController.php';

$dbClass = new Database();
$db = $dbClass->connect();

if (!$db) {
    die("No hay conexión a la base de datos");
}
require_once 'routes/web.php';