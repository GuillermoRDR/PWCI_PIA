<?php

function verificarSesion()
{
  if (!isset($_SESSION['usuario'])) {
    header(
      "Location: /index.php?action=loginPage"
    );

    exit();
  }
}

function verificarRol($rolesPermitidos)
{
  verificarSesion();
  $rolUsuario =
    $_SESSION['usuario']['rol'];

  if (
    !in_array(
      $rolUsuario,
      $rolesPermitidos
    )
  ) {
    header(
      "Location: /index.php?action=403"
    );

    exit();
  }
}

function requiereRol($rolesPermitidos)
{
  verificarSesion();

  if (
    !in_array(
      $_SESSION['usuario']['rol'],
      $rolesPermitidos
    )
  ) {
    die("Acceso denegado");
  }
}

function obtenerIdUsuario($db)
{
  $model = new UserModel($db);

  $usuario =
    $model->obtenerUsuarioIdCredencial(
      $_SESSION['usuario']['id']
    );

  return $usuario['id_usuario'];
}
