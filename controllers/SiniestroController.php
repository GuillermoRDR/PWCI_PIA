<?php
require_once __DIR__ . '/../models/SiniestroModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class SiniestroController {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function buscarAsegurado($texto){

        $model = new SiniestroModel($this->db);

        $resultado = $model->buscarAsegurado($texto);
        
        header('Content-Type: application/json');

        echo json_encode($resultado);
    }
    
    public function obtenerFotoUsuario($idUsuario){
        $model = new SiniestroModel($this->db);
        $foto = $model->obtenerFotoUsuario($idUsuario);
        if(!$foto){
            http_response_code(404);
            exit;
        }
        header("Content-Type: image/jpeg");
        echo $foto;
    }

    public function registrarSiniestro($data, $files){
        try {
            requiereRol([4]); // Ajustador
            ###################################### Validar datos #####################################
            if (empty($data['id_Unidad'])) {
                // Puedes redirigir con un error para SweetAlert como antes
                header("Location: /index.php?action=claim_create&error=datos_vacios");
                exit(); // Detiene la ejecución de ESTA función
            }

            //session_start();
            $userModel =
                new UserModel($this->db);
            $siniestroModel = new SiniestroModel($this->db);
            // ##################################### Obtener credencial #####################################
            $idCredencial = $_SESSION['usuario']['id'];
            // ##################################### Obtener usuario #####################################
            $usuario = $userModel->obtenerUsuarioIdCredencial($idCredencial);
            $idUsuario = $usuario['id_usuario'];
            // ##################################### Registrar #####################################
            $siniestroModel->registrarSiniestro($data, $files, $idUsuario);
            header("Location: /index.php?action=claim_create&registroSiniestroSuccess=1");
            exit('Siniestro registrado exitosamente');
        } catch (Exception $e) {
            $errorMsg = urlencode(
                $e->getMessage()
            );
            header(
                "Location: /index.php?action=claim_create&error=" . $errorMsg
            );
            exit('Error al registrar siniestro: ' . $e->getMessage());
        }
    }

    public function claimsList(){

        if(!isset($_SESSION['usuario'])){

            header("Location: /index.php");
            exit();
        }

        $userModel = new UserModel($this->db);

        $usuario =
            $userModel->obtenerUsuarioIdCredencial(
                $_SESSION['usuario']['id']
            );

        $idUsuario = $usuario['id_usuario'];

        $rol = $_SESSION['usuario']['rol'];

        $desde = isset($_GET['desde']) && $_GET['desde'] !== ''
            ? $_GET['desde']
            : null;

        $hasta = isset($_GET['hasta']) && $_GET['hasta'] !== ''
            ? $_GET['hasta']
            : null;

        $estatus = isset($_GET['estatus']) && $_GET['estatus'] !== ''
            ? $_GET['estatus']
            : null;

        $busqueda = isset($_GET['q']) && trim($_GET['q']) !== ''
            ? trim($_GET['q'])
            : null;

            echo "Parámetros de búsqueda - Desde: " . ($desde ?? 'null') . ", Hasta: " . ($hasta ?? 'null') . ", Estatus: " . ($estatus ?? 'null') . ", Búsqueda: " . ($busqueda ?? 'null') . "\n"; // Debug    
            echo "ID Usuario: " . $idUsuario . ", Rol: " . $rol . "\n"; // Debug

        $model = new SiniestroModel($this->db);
         // Detiene la ejecución después de mostrar los datos de depuración
        $siniestros =
            $model->listarSiniestros(
                $idUsuario,
                $rol,
                $busqueda,
                $desde,
                $hasta
            );
            echo "Siniestros obtenidos: " . count($siniestros) . "\n"; // Debug
            //die("Mostrando siniestros..."); // Debug

        require __DIR__ .
            '/../pages/claims_list.php';
    }

    public function claimDetail(){

        if(!isset($_GET['id'])){

            header("Location: /index.php?action=claims_list");
            exit();
        }

        $idSiniestro = $_GET['id'];

        $model = new SiniestroModel($this->db);

        $detalle = $model->obtenerDetalleSiniestro($idSiniestro);

        require __DIR__ . '/../pages/claim_detail.php';
    }

    public function verMultimedia(){

        if(!isset($_GET['id'])){
            exit();
        }

        $model = new SiniestroModel($this->db);

        $archivo = $model->obtenerArchivo($_GET['id']);

        if(!$archivo){
            exit();
        }

        header("Content-Type: " . $archivo['mime_type']);

        echo $archivo['archivo'];
    }

    public function followup($idSiniestro){

        if(!$idSiniestro){

            echo("ID inválido");

            return; 

        }

        $model = new SiniestroModel($this->db);

        $datos = $model->obtenerSeguimiento(
            $idSiniestro
        );
        $siniestro = $datos['siniestro'];
        $comentarios = $datos['comentarios'];

        require __DIR__ . '/../pages/followup.php';
    }

    public function registrarComentario($data){

        try{

            $idSiniestro = $data['id_siniestro'];
            $comentario = trim($data['comentario']);

            $idCredencial = $_SESSION['usuario']['id'];

            $userModel = new UserModel($this->db);

            $usuario = $userModel
                ->obtenerUsuarioIdCredencial(
                    $idCredencial
                );

            $idUsuario = $usuario['id_usuario'];

            $model = new SiniestroModel($this->db);

            $model->registrarComentario(

                $idSiniestro,
                $idUsuario,
                $comentario

            );

            header(
                "Location: /index.php?action=followup&id=$idSiniestro"
            );

            exit();

        }catch(Exception $e){

            die($e->getMessage());

        }
    }

}