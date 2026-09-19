<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/auth.php';

class UserController
{

  private $db;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function registrar($input, $files)
  {
    requiereRol([1, 2]); // Solo Administrador y Supervisor pueden registrar usuarios
    //echo $this->db ? "Conexión a DB exitosa\n" : "Error al conectar a DB\n";
    // Validación de contraseña
    if (strlen($input['contrasenia']) < 8) {
      echo "Contraseña inválida";
      return;
    }

    // Hash
    $input['contrasenia'] = password_hash($input['contrasenia'], PASSWORD_BCRYPT);

    $model = new UserModel($this->db);

    if ($model->registrar($input, $files)) {
      header("Location: /pages/register.php?success=1");
      exit();
    } else {
      header("Location: /pages/register.php?error=1");
      exit();
    }
  }

  public function login($input)
  { //Funcion de login, recibe el correo, tipo y contraseña del formulario de login   

    $model = new UserModel($this->db);
    // echo "Intentando login con correo: " . $input['correo'] . "\n"; // Debug

    $user = $model->login($input['correo']);

    if ($user && password_verify($input['contrasenia'], $user['contrasenia'])) {
      ////session_start();

      $_SESSION['usuario'] = [
        'id' => $user['id_credencial'],
        'nombre' => $user['nombre'],
        'rol' => $user['id_rol_c']
      ];

      /*echo " Login exitoso para el usuario: " . $user['id_credencial'] . " con rol: " . $user['id_rol_c'] . ". ";
            die("Redirigiendo a dashboard..."); // Debug*/
      header("Location: /index.php?action=dashboard&loginSuccess=1");
    } else {
      header("Location: /index.php?action=login&loginError=1");
      // echo "Credenciales inválidas";
    }
  }

  public function actualizarUsuario($input, $files)
  {

    if (!empty($input['contrasenia'])) {
      $input['contrasenia'] = password_hash($input['contrasenia'], PASSWORD_BCRYPT);
    } else {
      $input['contrasenia'] = null; // no actualizar
    }

    echo "Datos recibidos para actualización: " . json_encode($input) . "\n"; // Debug


    $model = new UserModel($this->db);
    if ($model->actualizarUsuario($input, $files, $_SESSION['usuario']['id'])) {
      header("Location: /index.php?action=editarPerfil&actualizarUsuarioSuccess=1");
      exit();
    } else {
      header("Location: /index.php?action=editarPerfil&actualizarUsuarioError=1");
      exit();
    }
  }

  public function mostrarDatos()
  {

    if (!isset($_SESSION['usuario'])) {

      header(
        "Location: /index.php"
      );

      exit();
    }

    $model = new UserModel($this->db);
    $usuario = $model->obtenerUsuarioIdCredencial($_SESSION['usuario']['id']);

    require __DIR__ . '/../pages/actualizar_Usuario.php';
  }
}
