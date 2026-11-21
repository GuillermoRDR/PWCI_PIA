<?php

namespace Models;

use Core\App;
use Core\Database;

class CursoCatalogo
{
  private Database $db;

  public function __construct(?Database $db = null)
  {
    $this->db = $db ?? App::resolve(Database::class);
  }

  public function recientes(int $limite = 6): array
  {
    $limite = max(1, min($limite, 50));

    return $this->db->query(
      "SELECT curso_id AS id, titulo, descripcion,
              imagen_url AS imagen, precio, modalidad_precio, publicado_en
       FROM cursos
       WHERE estado = 'activo'
       ORDER BY COALESCE(publicado_en, creado_en) DESC
       LIMIT {$limite}"
    )->get();
  }

  public function mejorCalificados(int $limite = 6): array
  {
    $limite = max(1, min($limite, 50));

    return $this->db->query(
      "SELECT c.curso_id AS id, c.titulo, c.descripcion,
              c.imagen_url AS imagen, c.precio,c.modalidad_precio,
              COALESCE(ROUND(AVG(CASE WHEN co.estado = 'visible'
                THEN co.calificacion END), 2), 0) AS calificacion
       FROM cursos c
       LEFT JOIN comentarios co ON co.curso_id = c.curso_id
       WHERE c.estado = 'activo'
       GROUP BY c.curso_id, c.titulo, c.descripcion,
                c.imagen_url, c.precio,c.modalidad_precio
       ORDER BY calificacion DESC, c.titulo ASC
       LIMIT {$limite}"
    )->get();
  }

  public function masVendidos(int $limite = 6): array
  {
    $limite = max(1, min($limite, 50));

    return $this->db->query(
      "SELECT c.curso_id AS id, c.titulo, c.descripcion,
              c.imagen_url AS imagen, c.precio,c.modalidad_precio,
              COUNT(p.pedido_id) AS ventas
       FROM cursos c
       LEFT JOIN detalles_pedido dp ON dp.curso_id = c.curso_id OR dp.nivel_id IN (SELECT nivel_id FROM niveles WHERE curso_id=c.curso_id)
       LEFT JOIN pedidos p ON p.pedido_id = dp.pedido_id
          AND p.estado = 'pagado'
       WHERE c.estado = 'activo'
       GROUP BY c.curso_id, c.titulo, c.descripcion,
                c.imagen_url, c.precio,c.modalidad_precio
       ORDER BY ventas DESC, c.titulo ASC
       LIMIT {$limite}"
    )->get();
  }

  public function findActivo(int $cursoId): array|false
  {
    $stmt = $this->db->connection->prepare(
      "SELECT c.curso_id AS id, c.titulo, c.descripcion,
              c.imagen_url AS imagen, c.precio AS precio_completo,c.modalidad_precio,
              u.nombre_completo AS instructor,
              COALESCE(ROUND(AVG(CASE WHEN co.estado = 'visible'
                THEN co.calificacion END), 2), 0) AS promedio_calificacion
       FROM cursos c
       INNER JOIN usuarios u ON u.usuario_id = c.profesor_id
       LEFT JOIN comentarios co ON co.curso_id = c.curso_id
       WHERE c.curso_id = :curso_id AND c.estado = 'activo'
       GROUP BY c.curso_id, c.titulo, c.descripcion, c.imagen_url,
                c.precio,c.modalidad_precio, u.nombre_completo
       LIMIT 1"
    );
    $stmt->execute(['curso_id' => $cursoId]);

    return $stmt->fetch();
  }

  public function buscar(
    string $termino = '',
    ?int $categoriaId = null,
    ?string $desde = null,
    ?string $hasta = null
  ): array {
    $where = ["c.estado = 'activo'"];
    $params = [];

    if ($termino !== '') {
      $where[] = "(c.titulo LIKE :titulo
        OR c.descripcion LIKE :descripcion
        OR u.nombre_completo LIKE :instructor)";
      $valor = '%' . $termino . '%';
      $params['titulo'] = $valor;
      $params['descripcion'] = $valor;
      $params['instructor'] = $valor;
    }

    if ($categoriaId !== null && $categoriaId > 0) {
      $where[] = "EXISTS (
        SELECT 1 FROM curso_categorias cc_filtro
        WHERE cc_filtro.curso_id = c.curso_id
          AND cc_filtro.categoria_id = :categoria_id
      )";
      $params['categoria_id'] = $categoriaId;
    }

    if ($desde !== null) {
      $where[] = 'DATE(COALESCE(c.publicado_en, c.creado_en)) >= :desde';
      $params['desde'] = $desde;
    }

    if ($hasta !== null) {
      $where[] = 'DATE(COALESCE(c.publicado_en, c.creado_en)) <= :hasta';
      $params['hasta'] = $hasta;
    }

    $sql = "SELECT c.curso_id AS id, c.titulo, c.descripcion,
                   c.imagen_url AS imagen, c.precio,c.modalidad_precio,
                   u.nombre_completo AS instructor,
                   COALESCE(ROUND(AVG(CASE WHEN co.estado = 'visible'
                     THEN co.calificacion END), 2), 0) AS calificacion
            FROM cursos c
            INNER JOIN usuarios u ON u.usuario_id = c.profesor_id
            LEFT JOIN comentarios co ON co.curso_id = c.curso_id
            WHERE " . implode(' AND ', $where) . "
            GROUP BY c.curso_id, c.titulo, c.descripcion,
                     c.imagen_url, c.precio,c.modalidad_precio, u.nombre_completo
            ORDER BY COALESCE(c.publicado_en, c.creado_en) DESC";

    return $this->db->query($sql, $params)->get();
  }

  public function nivelesActivos(int $cursoId): array
  {
    $stmt = $this->db->connection->prepare(
      "SELECT n.nivel_id AS id, n.numero_orden AS numero,n.precio,n.es_gratuito,
              n.titulo, n.descripcion, r.tipo,
              r.recurso_id,r.titulo AS recurso_titulo, r.recurso_url,r.contenido_texto,
              r.orden
       FROM niveles n
       LEFT JOIN recursos_nivel r ON r.nivel_id = n.nivel_id
       WHERE n.curso_id = :curso_id AND n.estado = 'activo'
       ORDER BY n.numero_orden ASC, r.orden ASC"
    );
    $stmt->execute(['curso_id' => $cursoId]);

    $niveles = [];

    foreach ($stmt->fetchAll() as $fila) {
      $id = (int) $fila['id'];

      if (!isset($niveles[$id])) {
        $niveles[$id] = [
          'id' => $id,
          'numero' => (int) $fila['numero'],
          'titulo' => $fila['titulo'],
          'descripcion' => $fila['descripcion'],
          'precio' => $fila['precio'],
          'es_gratuito' => $fila['es_gratuito'],
          'video_url' => null,
          'video_id' => null,
          'archivos' => [],
        ];
      }

      if (!$fila['tipo'])
        continue;

      if ($fila['tipo'] === 'video' && !$niveles[$id]['video_url']) {
        $niveles[$id]['video_url'] = $fila['recurso_url'];
        $niveles[$id]['video_id'] = (int) $fila['recurso_id'];
      }

      if ($fila['tipo'] !== 'video') {
        $niveles[$id]['archivos'][] = [
          'tipo' => $fila['tipo'],
          'id' => (int) $fila['recurso_id'],
          'nombre' => $fila['recurso_titulo'],
          'url' => $fila['recurso_url'] ?: '#',
          'texto' => $fila['contenido_texto'],
        ];
      }
    }

    return array_values($niveles);
  }
}
