<?php

namespace Models;
use Core\App;
use Core\Database;

class User
{
  private Database $db;
  public function __construct() {
    $this->db = App::resolve(Database::class);
  }

  public function exists(string $correo, string $usuario): bool
  {
    $stmt = $this->db->connection->prepare(
      "SELECT usuario_id FROM usuarios
      WHERE correo = :correo OR nombre_usuario = :usuario
      LIMIT 1"
    );

    $stmt->execute([
      'correo' => $correo,
      'usuario' => $usuario
    ]);

    return (bool) $stmt->fetch();
  }

  public function create(array $data): int
  {
    $sql = "INSERT INTO usuarios
    (
      rol_id,
      nombre_completo,
      nombre_usuario,
      correo,
      genero,
      fecha_nacimiento,
      foto_url,
      password_hash
    )
    SELECT
      rol_id,
      :nombre_completo,
      :nombre_usuario,
      :correo,
      :genero,
      :fecha_nacimiento,
      :foto_url,
      :password_hash
    FROM roles
    WHERE nombre = :rol AND nombre IN ('alumno', 'profesor')";

    $stmt = $this->db->connection->prepare($sql);

    $stmt->execute([
      'rol' => $data['rol'],
      'nombre_completo' => $data['nombre_completo'],
      'nombre_usuario' => $data['nombre_usuario'],
      'correo' => $data['correo'],
      'genero' => $data['genero'],
      'fecha_nacimiento' => $data['fecha_nacimiento'],
      'foto_url' => $data['foto_url'],
      'password_hash' => $data['password_hash']
    ]);

    if ($stmt->rowCount() !== 1) {
      throw new \RuntimeException('Rol no disponible.');
    }

    return (int) $this->db->connection->lastInsertId();
  }
}
