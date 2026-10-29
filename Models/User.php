<?php

namespace Models;

use Core\App;
use Core\Database;

class User
{
  private Database $db;
  public function __construct()
  {
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

  public function autenticar(string $identificador, string $password): array
  {
    $pdo = $this->db->connection;

    $pdo->beginTransaction();

    try {
      $stmt = $pdo->prepare("
        SELECT
            u.usuario_id,
            u.nombre_completo,
            u.nombre_usuario,
            u.correo,
            u.password_hash,
            u.estado,
            u.intentos_fallidos,
            r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON r.rol_id = u.rol_id
        WHERE u.correo = :correo
           OR u.nombre_usuario = :usuario
        LIMIT 1
        FOR UPDATE
        ");

      $stmt->execute([
        'correo' => $identificador,
        'usuario' => $identificador
      ]);

      $usuario = $stmt->fetch();

      if (!$usuario) {
        password_verify($password, '$2y$12$abcdefghijklmnopqrstuuZzZzZzZzZzZzZzZzZzZzZzZzZzZzZz');

        $pdo->commit();

        return [
          'ok' => false,
          'codigo' => 401,
          'mensaje' => 'Credenciales incorrectas.'
        ];
      }

      if ($usuario['estado'] !== 'activo') {
        $pdo->commit();

        return [
          'ok' => false,
          'codigo' => 403,
          'mensaje' => 'La cuenta está deshabilitada. Contacta al administrador.'
        ];
      }

      if (!password_verify($password, $usuario['password_hash'])) {
        $intentos = min(3, (int)$usuario['intentos_fallidos'] + 1);

        if ($intentos >= 3) {
          $update = $pdo->prepare("
                    UPDATE usuarios
                    SET intentos_fallidos = 3,
                        estado = 'bloqueado',
                        bloqueado_en = NOW(),
                        motivo_bloqueo = 'Tres intentos fallidos consecutivos'
                    WHERE usuario_id = :id
                ");
        } else {
          $update = $pdo->prepare("
                    UPDATE usuarios
                    SET intentos_fallidos = :intentos
                    WHERE usuario_id = :id
                ");
          $update->bindValue(':intentos', $intentos, \PDO::PARAM_INT);
        }

        $update->bindValue(':id', $usuario['usuario_id'], \PDO::PARAM_INT);
        $update->execute();

        $pdo->commit();

        return [
          'ok' => false,
          'codigo' => $intentos >= 3 ? 403 : 401,
          'mensaje' => $intentos >= 3
            ? 'Cuenta bloqueada después de tres intentos fallidos.'
            : 'Credenciales incorrectas.'
        ];
      }

      $update = $pdo->prepare("
            UPDATE usuarios
            SET intentos_fallidos = 0,
                ultimo_acceso_en = NOW()
            WHERE usuario_id = :id
        ");

      $update->execute(['id' => $usuario['usuario_id']]);

      $pdo->commit();

      return [
        'ok' => true,
        'codigo' => 200,
        'usuario' => [
          'id' => (int)$usuario['usuario_id'],
          'nombre' => $usuario['nombre_completo'],
          'usuario' => $usuario['nombre_usuario'],
          'rol' => $usuario['rol']
        ]
      ];
    } catch (\Throwable $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      throw $e;
    }
  }

  public function buscarSesion(int $id): array|false
  {
    $stmt = $this->db->connection->prepare("
        SELECT u.usuario_id, u.nombre_completo,
               u.estado, r.nombre AS rol
        FROM usuarios u
        JOIN roles r ON r.rol_id = u.rol_id
        WHERE u.usuario_id = :id
        LIMIT 1
    ");

    $stmt->execute(['id' => $id]);

    return $stmt->fetch();
  }
}
