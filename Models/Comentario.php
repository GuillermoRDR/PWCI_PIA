<?php
namespace Models;

use Core\App;
use Core\Database;

class Comentario
{
  private Database $db;

  public function __construct()
  {
    $this->db = App::resolve(Database::class);
  }

  public function obtenerPorCurso(int $cursoId): array
  {
    $stmt = $this->db->connection->prepare("
      SELECT
        c.comentario_id,
        u.nombre_completo AS alumno,
        c.calificacion,
        c.comentario,
        c.creado_en
      FROM comentarios c
      INNER JOIN usuarios u
        ON u.usuario_id = c.alumno_id
      WHERE c.curso_id = :curso_id
        AND c.estado = 'visible'
      ORDER BY c.creado_en DESC");

    $stmt->execute(['curso_id' => $cursoId]);

    return $stmt->fetchAll();
  }

  public function obtenerPromedio(int $cursoId): array
  {
    $stmt = $this->db->connection->prepare("
      SELECT COUNT(*) AS total,
      ROUND(AVG(calificacion), 2) AS promedio
      FROM comentarios
      WHERE curso_id = :curso_id AND estado = 'visible'");
    $stmt->execute(['curso_id' => $cursoId]);

    return $stmt->fetch();
  }

  public function existeCurso(int $cursoId): bool
  {
    $stmt = $this->db->connection->prepare("
      SELECT 1 FROM cursos
      WHERE curso_id = :curso_id AND estado = 'activo' LIMIT 1");
    $stmt->execute(['curso_id' => $cursoId]);

    return (bool)$stmt->fetchColumn();
  }
}
