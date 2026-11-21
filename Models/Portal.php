<?php

namespace Models;

use Core\App;
use Core\Database;
use PDO;
use RuntimeException;

class Portal
{
  private PDO $db;

  public function __construct(?Database $database = null)
  {
    $this->db = ($database ?? App::resolve(Database::class))->connection;
  }

  private function rows(string $sql, array $params = []): array
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  private function row(string $sql, array $params = []): array|false
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
  }

  private function run(string $sql, array $params = []): int
  {
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
  }

  public function usuario(int $id): array|false
  {
    return $this->row('SELECT u.*, r.nombre AS rol FROM usuarios u JOIN roles r ON r.rol_id=u.rol_id WHERE u.usuario_id=?', [$id]);
  }

  public function perfil(int $id, string $nombre, string $correo, string $genero, string $fecha, ?string $avatar): void
  {
    $nacimiento = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    if (!in_array($genero, ['Masculino', 'Femenino', 'Otro', 'Prefiero no decirlo'], true) || !$nacimiento || $nacimiento->format('Y-m-d') !== $fecha || $nacimiento > new \DateTimeImmutable('today'))
      throw new RuntimeException('Datos inválidos.');
    if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 150 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      throw new RuntimeException('Datos inválidos.');
    }
    $this->run('UPDATE usuarios SET nombre_completo=?, correo=?,genero=?,fecha_nacimiento=?,foto_url=COALESCE(?,foto_url) WHERE usuario_id=? AND estado=\'activo\'', [$nombre, $correo, $genero, $fecha, $avatar, $id]);
  }

  public function categorias(): array
  {
    return $this->rows('SELECT * FROM categorias ORDER BY nombre');
  }

  public function guardarCategoria(int $admin, string $nombre, string $descripcion, int $id = 0): void
  {
    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100 || mb_strlen($descripcion) > 500)
      throw new RuntimeException('Datos inválidos.');
    if ($id) {
      $this->run('UPDATE categorias SET nombre=?, descripcion=? WHERE categoria_id=?', [$nombre, $descripcion, $id]);
    } else {
      $this->run('INSERT INTO categorias (nombre,descripcion,creada_por) VALUES (?,?,?)', [$nombre, $descripcion, $admin]);
    }
  }

  public function estadoCategoria(int $id, string $estado): void
  {
    if (!in_array($estado, ['activa', 'inactiva'], true) || !$this->run('UPDATE categorias SET estado=? WHERE categoria_id=?', [$estado, $id]))
      throw new RuntimeException('Categoría no encontrada.');
  }

  public function cursosProfesor(int $profesor): array
  {
    return $this->rows('SELECT c.*, (SELECT COUNT(*) FROM niveles n WHERE n.curso_id=c.curso_id AND n.estado=\'activo\') AS niveles_count,
      (SELECT COUNT(*) FROM inscripciones i WHERE i.curso_id=c.curso_id) AS alumnos_count
      FROM cursos c WHERE profesor_id=? AND estado<>\'eliminado\' ORDER BY creado_en DESC', [$profesor]);
  }

  public function cursoProfesor(int $profesor, int $curso): array|false
  {
    return $this->row('SELECT c.*,(SELECT categoria_id FROM curso_categorias cc WHERE cc.curso_id=c.curso_id LIMIT 1) AS categoria_id FROM cursos c WHERE c.curso_id=? AND c.profesor_id=? AND c.estado<>\'eliminado\'', [$curso, $profesor]);
  }

  public function cursoParaReporte(int $profesor, int $curso): array|false
  {
    return $this->row('SELECT * FROM cursos WHERE curso_id=? AND profesor_id=?', [$curso, $profesor]);
  }

  public function guardarCurso(int $profesor, array $datos): int
  {
    $id = (int) ($datos['curso_id'] ?? 0);
    $titulo = trim((string) ($datos['titulo'] ?? ''));
    $descripcion = trim((string) ($datos['descripcion'] ?? ''));
    $categoria = (int) ($datos['categoria_id'] ?? 0);
    $precio = filter_var($datos['precio'] ?? null, FILTER_VALIDATE_FLOAT);
    $modalidad = (string) ($datos['modalidad_precio'] ?? 'curso_completo');
    $imagen = trim((string) ($datos['imagen_url'] ?? ''));
    if (filter_var($imagen, FILTER_VALIDATE_URL) && !preg_match('#^https?://#i', $imagen))
      throw new RuntimeException('Imagen inválida.');
    $niveles = $datos['niveles'] ?? [];
    if (!in_array($modalidad, ['curso_completo', 'por_nivel'], true))
      throw new RuntimeException('Modalidad inválida.');
    if (is_array($niveles))
      $niveles = array_filter($niveles, fn($nivel) => is_array($nivel) && trim((string) ($nivel['titulo'] ?? '')) !== '');
    if (mb_strlen($titulo) < 3 || mb_strlen($descripcion) < 10 || !$categoria || $precio === false || $precio < 0 || !is_array($niveles) || (!$id && !$niveles) || ($imagen !== '' && !filter_var($imagen, FILTER_VALIDATE_URL) && !preg_match('#^[a-zA-Z0-9_./-]+$#', $imagen))) {
      throw new RuntimeException('Datos inválidos.');
    }
    if (!$this->row('SELECT categoria_id FROM categorias WHERE categoria_id=? AND estado=\'activa\'', [$categoria]))
      throw new RuntimeException('Categoría inválida.');
    foreach ($niveles as $nivel) {
      if (!is_array($nivel))
        throw new RuntimeException('Nivel inválido.');
      if (!preg_match('#^https?://#i', (string) ($nivel['video_url'] ?? '')) && !preg_match('#^cursos/recursos/[a-f0-9]{32}\.(mp4|webm)$#', (string) ($nivel['video_url'] ?? '')))
        throw new RuntimeException('Video inválido.');
      foreach (['pdf', 'imagen', 'enlace'] as $tipo) {
        $enlace = trim((string) ($nivel[$tipo] ?? ''));
        if ($enlace !== '' && (!filter_var($enlace, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $enlace)))
          throw new RuntimeException('Enlace inválido.');
      }
      if (filter_var($nivel['precio'] ?? 0, FILTER_VALIDATE_FLOAT) === false || (float) ($nivel['precio'] ?? 0) < 0)
        throw new RuntimeException('Precio inválido.');
      if (mb_strlen(trim((string) ($nivel['titulo'] ?? ''))) < 3)
        throw new RuntimeException('Nivel inválido.');
    }
    $this->db->beginTransaction();
    try {
      if ($id) {
        $actual = $this->cursoProfesor($profesor, $id);
        if (!$actual)
          throw new RuntimeException('Curso no encontrado.');
        if ($actual['modalidad_precio'] !== $modalidad && $this->row('SELECT inscripcion_id FROM inscripciones WHERE curso_id=? LIMIT 1', [$id]))
          throw new RuntimeException('Modalidad en uso.');
        if ($niveles && $this->row('SELECT inscripcion_id FROM inscripciones WHERE curso_id=? AND estado=\'completada\' LIMIT 1', [$id]))
          throw new RuntimeException('Curso terminado por alumnos.');
        $this->run('UPDATE cursos SET titulo=?, descripcion=?, imagen_url=?, precio=?, modalidad_precio=?, estado=\'activo\', publicado_en=COALESCE(publicado_en,NOW()) WHERE curso_id=? AND profesor_id=?', [$titulo, $descripcion, $imagen ?: null, $precio, $modalidad, $id, $profesor]);
        // Los niveles existentes se conservan. El formulario agrega niveles nuevos.
      } else {
        $this->run('INSERT INTO cursos (profesor_id,titulo,descripcion,imagen_url,modalidad_precio,precio,estado,publicado_en) VALUES (?,?,?,?,?,?,\'activo\',NOW())', [$profesor, $titulo, $descripcion, $imagen ?: null, $modalidad, $precio]);
        $id = (int) $this->db->lastInsertId();
      }
      $this->run('DELETE FROM curso_categorias WHERE curso_id=?', [$id]);
      $this->run('INSERT INTO curso_categorias (curso_id,categoria_id) VALUES (?,?)', [$id, $categoria]);
      $orden = (int) ($this->row('SELECT COALESCE(MAX(numero_orden),0) AS ultimo FROM niveles WHERE curso_id=?', [$id])['ultimo'] ?? 0);
      foreach ($niveles as $nivel) {
        $orden++;
        $precioNivel = $modalidad === 'por_nivel' ? (float) ($nivel['precio'] ?? 0) : 0;
        $gratis = $modalidad === 'por_nivel' && $precioNivel === 0.0 ? 1 : 0;
        $this->run('INSERT INTO niveles (curso_id,numero_orden,titulo,descripcion,es_gratuito,precio,estado) VALUES (?,?,?,?,?,?,\'activo\')', [$id, $orden, trim($nivel['titulo']), trim((string) ($nivel['descripcion'] ?? '')), $gratis, $precioNivel]);
        $nivelId = (int) $this->db->lastInsertId();
        $this->run('INSERT INTO recursos_nivel (nivel_id,tipo,titulo,recurso_url,orden) VALUES (?,\'video\',?,?,1)', [$nivelId, 'Video del nivel', trim($nivel['video_url'])]);
        if ($modalidad === 'curso_completo')
          $this->run('INSERT IGNORE INTO accesos_nivel (inscripcion_id,nivel_id,origen) SELECT inscripcion_id,?,IF(origen=\'curso_gratuito\',\'gratuito\',\'curso_completo\') FROM inscripciones WHERE curso_id=? AND origen IN (\'curso_gratuito\',\'compra_curso\')', [$nivelId, $id]);
        $extra = trim((string) ($nivel['texto'] ?? ''));
        if ($extra !== '')
          $this->run('INSERT INTO recursos_nivel (nivel_id,tipo,titulo,contenido_texto,orden) VALUES (?,\'texto\',?,?,2)', [$nivelId, 'Lectura', $extra]);
        foreach (['pdf', 'imagen', 'enlace'] as $tipo) {
          $enlace = trim((string) ($nivel[$tipo] ?? ''));
          if ($enlace !== '' && filter_var($enlace, FILTER_VALIDATE_URL)) {
            $ordenRecurso = 3 + array_search($tipo, ['pdf', 'imagen', 'enlace'], true);
            $this->run('INSERT INTO recursos_nivel (nivel_id,tipo,titulo,recurso_url,orden) VALUES (?,?,?,?,?)', [$nivelId, $tipo, ucfirst($tipo), $enlace, $ordenRecurso]);
          }
        }
        $ordenRecurso = 5;
        foreach (($nivel['recursos'] ?? []) as $recurso) {
          $ordenRecurso++;
          $this->run(
            'INSERT INTO recursos_nivel (nivel_id,tipo,titulo,recurso_url,orden) VALUES (?,?,?,?,?)',
            [$nivelId, $recurso['tipo'], $recurso['titulo'], $recurso['ruta'], $ordenRecurso]
          );
        }
      }
      $this->db->commit();
      return $id;
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function bajaCurso(int $profesor, int $curso): void
  {
    if (!$this->run('UPDATE cursos SET estado=\'eliminado\', eliminado_en=NOW() WHERE curso_id=? AND profesor_id=? AND estado<>\'eliminado\'', [$curso, $profesor]))
      throw new RuntimeException('Curso no encontrado.');
  }

  public function misCursos(int $alumno, array $filtros = []): array
  {
    $condiciones = ['i.alumno_id=?'];
    $parametros = [$alumno];
    if (!empty($filtros['desde'])) {
      $condiciones[] = 'DATE(i.inscrito_en)>=?';
      $parametros[] = $filtros['desde'];
    }
    if (!empty($filtros['hasta'])) {
      $condiciones[] = 'DATE(i.inscrito_en)<=?';
      $parametros[] = $filtros['hasta'];
    }
    if (!empty($filtros['categoria_id'])) {
      $condiciones[] = 'EXISTS(SELECT 1 FROM curso_categorias cc WHERE cc.curso_id=c.curso_id AND cc.categoria_id=?)';
      $parametros[] = (int) $filtros['categoria_id'];
    }
    if (($filtros['terminados'] ?? '') === '1')
      $condiciones[] = 'i.estado=\'completada\'';
    if (($filtros['activos'] ?? '') === '1')
      $condiciones[] = 'c.estado=\'activo\'';
    return $this->rows('SELECT i.inscripcion_id,i.inscrito_en,i.completada_en,i.estado AS inscripcion_estado,c.curso_id,c.titulo,c.imagen_url,c.estado AS curso_estado,
      (SELECT COUNT(*) FROM niveles n WHERE n.curso_id=c.curso_id AND n.estado=\'activo\') AS total_niveles,
      (SELECT COUNT(*) FROM progreso_niveles p WHERE p.inscripcion_id=i.inscripcion_id AND p.estado=\'completado\') AS niveles_completados,
      (SELECT MAX(p.actualizado_en) FROM progreso_niveles p WHERE p.inscripcion_id=i.inscripcion_id) AS ultima_actividad
      FROM inscripciones i JOIN cursos c ON c.curso_id=i.curso_id WHERE ' . implode(' AND ', $condiciones) . ' ORDER BY i.inscrito_en DESC', $parametros);
  }

  public function acceso(int $alumno, int $curso): array|false
  {
    return $this->row('SELECT i.* FROM inscripciones i WHERE i.alumno_id=? AND i.curso_id=? AND i.estado<>\'cancelada\'', [$alumno, $curso]);
  }

  public function progreso(int $alumno, int $curso): array
  {
    return $this->rows('SELECT p.nivel_id FROM progreso_niveles p JOIN inscripciones i ON i.inscripcion_id=p.inscripcion_id WHERE i.alumno_id=? AND i.curso_id=? AND p.estado=\'completado\'', [$alumno, $curso]);
  }

  public function accesosNivel(int $alumno, int $curso): array
  {
    return $this->rows('SELECT a.nivel_id FROM accesos_nivel a JOIN inscripciones i ON i.inscripcion_id=a.inscripcion_id WHERE i.alumno_id=? AND i.curso_id=? AND i.estado<>\'cancelada\'', [$alumno, $curso]);
  }

  private function inscribir(int $alumno, int $curso, string $origen, int $nivel = 0): void
  {
    $this->run('INSERT INTO inscripciones (alumno_id,curso_id,origen) VALUES (?,?,?) ON DUPLICATE KEY UPDATE estado=\'activa\'', [$alumno, $curso, $origen]);
    $inscripcion = $this->acceso($alumno, $curso);
    if ($nivel) {
      $this->run('INSERT IGNORE INTO accesos_nivel (inscripcion_id,nivel_id,origen) VALUES (?,?,?)', [$inscripcion['inscripcion_id'], $nivel, $origen === 'nivel_gratuito' ? 'gratuito' : 'nivel_individual']);
    } else {
      $this->run('INSERT IGNORE INTO accesos_nivel (inscripcion_id,nivel_id,origen) SELECT ?,nivel_id,? FROM niveles WHERE curso_id=? AND estado=\'activo\'', [$inscripcion['inscripcion_id'], $origen === 'curso_gratuito' ? 'gratuito' : 'curso_completo', $curso]);
    }
  }

  public function solicitarCurso(int $alumno, int $curso, string $metodo): void
  {
    $datos = $this->row('SELECT curso_id,titulo,precio,modalidad_precio FROM cursos WHERE curso_id=? AND estado=\'activo\'', [$curso]);
    if ($datos && $datos['modalidad_precio'] !== 'curso_completo')
      throw new RuntimeException('Selecciona un nivel.');
    if (!$datos || $this->acceso($alumno, $curso))
      throw new RuntimeException('Curso no disponible.');
    if ((float) $datos['precio'] > 0 && !in_array($metodo, ['transferencia', 'paypal'], true))
      throw new RuntimeException('Método inválido.');
    $this->db->beginTransaction();
    try {
      if ((float) $datos['precio'] === 0.0) {
        $this->inscribir($alumno, $curso, 'curso_gratuito');
      } else {
        $pendiente = $this->row('SELECT p.pedido_id FROM pedidos p JOIN detalles_pedido d ON d.pedido_id=p.pedido_id WHERE p.alumno_id=? AND d.curso_id=? AND p.estado=\'pendiente\' LIMIT 1', [$alumno, $curso]);
        if ($pendiente)
          throw new RuntimeException('Ya existe una solicitud.');
        $this->run('INSERT INTO pedidos (alumno_id,estado,subtotal,total) VALUES (?,\'pendiente\',?,?)', [$alumno, $datos['precio'], $datos['precio']]);
        $pedido = (int) $this->db->lastInsertId();
        $this->run('INSERT INTO detalles_pedido (pedido_id,tipo_item,curso_id,descripcion_historica,precio_unitario,cantidad,importe) VALUES (?,\'curso\',?,?,?,1,?)', [$pedido, $curso, $datos['titulo'], $datos['precio'], $datos['precio']]);
        $metodoId = $this->row('SELECT metodo_pago_id FROM metodos_pago WHERE nombre=? AND activo=1', [$metodo]);
        if (!$metodoId)
          throw new RuntimeException('Método no disponible.');
        $this->run('INSERT INTO pagos (pedido_id,metodo_pago_id,estado,importe) VALUES (?,?,\'pendiente\',?)', [$pedido, $metodoId['metodo_pago_id'], $datos['precio']]);
      }
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function solicitarNivel(int $alumno, int $curso, int $nivel, string $metodo): void
  {
    $datos = $this->row('SELECT n.nivel_id,n.precio,n.titulo FROM niveles n JOIN cursos c ON c.curso_id=n.curso_id WHERE n.nivel_id=? AND c.curso_id=? AND c.estado=\'activo\' AND c.modalidad_precio=\'por_nivel\' AND n.estado=\'activo\'', [$nivel, $curso]);
    if (!$datos)
      throw new RuntimeException('Nivel no disponible.');
    $acceso = $this->accesosNivel($alumno, $curso);
    if (in_array((string) $nivel, array_map('strval', array_column($acceso, 'nivel_id')), true))
      throw new RuntimeException('Ya tienes acceso.');
    $this->db->beginTransaction();
    try {
      if ((float) $datos['precio'] === 0.0)
        $this->inscribir($alumno, $curso, 'nivel_gratuito', $nivel);
      else {
        if (!in_array($metodo, ['paypal', 'transferencia'], true))
          throw new RuntimeException('Método inválido.');
        $pendiente = $this->row('SELECT p.pedido_id FROM pedidos p JOIN detalles_pedido d ON d.pedido_id=p.pedido_id WHERE p.alumno_id=? AND d.nivel_id=? AND p.estado=\'pendiente\' LIMIT 1', [$alumno, $nivel]);
        if ($pendiente)
          throw new RuntimeException('Ya existe una solicitud.');
        $metodoId = $this->row('SELECT metodo_pago_id FROM metodos_pago WHERE nombre=? AND activo=1', [$metodo]);
        if (!$metodoId)
          throw new RuntimeException('Método no disponible.');
        $this->run('INSERT INTO pedidos (alumno_id,estado,subtotal,total) VALUES (?,\'pendiente\',?,?)', [$alumno, $datos['precio'], $datos['precio']]);
        $pedido = (int) $this->db->lastInsertId();
        $this->run('INSERT INTO detalles_pedido (pedido_id,tipo_item,nivel_id,descripcion_historica,precio_unitario,cantidad,importe) VALUES (?,\'nivel\',?,?,?,1,?)', [$pedido, $nivel, $datos['titulo'], $datos['precio'], $datos['precio']]);
        $this->run('INSERT INTO pagos (pedido_id,metodo_pago_id,estado,importe) VALUES (?,?,\'pendiente\',?)', [$pedido, $metodoId['metodo_pago_id'], $datos['precio']]);
      }
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function pedidosPendientes(): array
  {
    return $this->rows('SELECT p.pedido_id,p.total,p.creado_en,u.nombre_completo AS alumno,c.titulo AS curso,m.nombre AS metodo FROM pedidos p JOIN usuarios u ON u.usuario_id=p.alumno_id JOIN detalles_pedido d ON d.pedido_id=p.pedido_id LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN cursos c ON c.curso_id=COALESCE(d.curso_id,n.curso_id) JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id WHERE p.estado=\'pendiente\' AND m.nombre=\'transferencia\' ORDER BY p.creado_en');
  }

  public function pagoPayPalPendiente(int $alumno, int $curso, int $nivel = 0): array
  {
    $pago = $this->row('SELECT p.pedido_id,p.total,p.moneda,pa.pago_id,pa.referencia_externa FROM pedidos p JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id JOIN detalles_pedido d ON d.pedido_id=p.pedido_id LEFT JOIN niveles n ON n.nivel_id=d.nivel_id WHERE p.alumno_id=? AND COALESCE(d.curso_id,n.curso_id)=? AND COALESCE(d.nivel_id,0)=? AND p.estado=\'pendiente\' AND m.nombre=\'paypal\' ORDER BY p.pedido_id DESC LIMIT 1', [$alumno, $curso, $nivel]);
    if (!$pago)
      throw new RuntimeException('No se encontró el pedido de PayPal.');
    return $pago;
  }

  public function vincularPayPal(int $alumno, int $pedido, string $orden): void
  {
    if ($this->run('UPDATE pagos pa JOIN pedidos p ON p.pedido_id=pa.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id SET pa.referencia_externa=? WHERE p.pedido_id=? AND p.alumno_id=? AND p.estado=\'pendiente\' AND m.nombre=\'paypal\' AND pa.referencia_externa IS NULL', [$orden, $pedido, $alumno]) !== 1)
      throw new RuntimeException('No se pudo vincular el pago.');
  }

  public function capturarPayPal(int $alumno, string $orden, array $captura): void
  {
    $this->db->beginTransaction();
    try {
      $pago = $this->row('SELECT p.pedido_id,p.total,p.moneda,p.estado,pa.estado AS estado_pago FROM pedidos p JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id WHERE p.alumno_id=? AND pa.referencia_externa=? AND m.nombre=\'paypal\' FOR UPDATE', [$alumno, $orden]);
      if (!$pago)
        throw new RuntimeException('Orden no encontrada.');
      if ($pago['estado'] === 'pagado' && $pago['estado_pago'] === 'aprobado') {
        $this->db->commit();
        return;
      }
      $unidad = $captura['purchase_units'][0] ?? [];
      $datos = $unidad['payments']['captures'][0] ?? [];
      if (($captura['id'] ?? '') !== $orden || ($captura['status'] ?? '') !== 'COMPLETED' || ($datos['status'] ?? '') !== 'COMPLETED' || ($datos['amount']['currency_code'] ?? '') !== $pago['moneda'] || !is_numeric($datos['amount']['value'] ?? null) || (int) round((float) $datos['amount']['value'] * 100) !== (int) round((float) $pago['total'] * 100) || $pago['estado'] !== 'pendiente')
        throw new RuntimeException('El pago no fue confirmado por PayPal.');
      $datosPedido = $this->row('SELECT p.alumno_id,c.curso_id,d.nivel_id FROM pedidos p JOIN detalles_pedido d ON d.pedido_id=p.pedido_id LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN cursos c ON c.curso_id=COALESCE(d.curso_id,n.curso_id) WHERE p.pedido_id=?', [$pago['pedido_id']]);
      if (!$datosPedido)
        throw new RuntimeException('Curso no disponible.');
      $this->run('UPDATE pedidos SET estado=\'pagado\' WHERE pedido_id=?', [$pago['pedido_id']]);
      $this->run('UPDATE pagos SET estado=\'aprobado\' WHERE pedido_id=?', [$pago['pedido_id']]);
      $this->inscribir((int) $datosPedido['alumno_id'], (int) $datosPedido['curso_id'], $datosPedido['nivel_id'] ? 'compra_nivel' : 'compra_curso', (int) $datosPedido['nivel_id']);
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function cancelarPayPal(int $alumno, string $orden): void
  {
    $this->run('UPDATE pedidos p JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id SET p.estado=\'cancelado\',pa.estado=\'cancelado\' WHERE p.alumno_id=? AND pa.referencia_externa=? AND p.estado=\'pendiente\' AND m.nombre=\'paypal\'', [$alumno, $orden]);
  }

  public function confirmarPedido(int $pedido): void
  {
    $this->db->beginTransaction();
    try {
      $datos = $this->row('SELECT p.alumno_id,c.curso_id,d.nivel_id FROM pedidos p JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id JOIN detalles_pedido d ON d.pedido_id=p.pedido_id LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN cursos c ON c.curso_id=COALESCE(d.curso_id,n.curso_id) AND c.estado=\'activo\' WHERE p.pedido_id=? AND p.estado=\'pendiente\' AND m.nombre=\'transferencia\' AND (n.nivel_id IS NULL OR n.estado=\'activo\') FOR UPDATE', [$pedido]);
      if (!$datos)
        throw new RuntimeException('Pedido no encontrado.');
      $this->run('UPDATE pedidos SET estado=\'pagado\' WHERE pedido_id=?', [$pedido]);
      $this->run('UPDATE pagos SET estado=\'aprobado\' WHERE pedido_id=?', [$pedido]);
      $this->inscribir((int) $datos['alumno_id'], (int) $datos['curso_id'], $datos['nivel_id'] ? 'compra_nivel' : 'compra_curso', (int) $datos['nivel_id']);
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function completarNivel(int $alumno, int $nivel): void
  {
    $acceso = $this->row('SELECT i.inscripcion_id,i.curso_id FROM accesos_nivel a JOIN inscripciones i ON i.inscripcion_id=a.inscripcion_id JOIN niveles n ON n.nivel_id=a.nivel_id AND n.curso_id=i.curso_id AND n.estado=\'activo\' WHERE i.alumno_id=? AND a.nivel_id=? AND i.estado IN (\'activa\',\'completada\')', [$alumno, $nivel]);
    if (!$acceso)
      throw new RuntimeException('Nivel no disponible.');
    $this->db->beginTransaction();
    try {
      $this->run('INSERT INTO progreso_niveles (inscripcion_id,nivel_id,estado,iniciado_en,completado_en,porcentaje) VALUES (?,?,\'completado\',NOW(),NOW(),100) ON DUPLICATE KEY UPDATE estado=\'completado\',completado_en=NOW(),porcentaje=100', [$acceso['inscripcion_id'], $nivel]);
      $faltan = $this->row('SELECT COUNT(*) AS total FROM niveles n LEFT JOIN progreso_niveles p ON p.nivel_id=n.nivel_id AND p.inscripcion_id=? WHERE n.curso_id=? AND n.estado=\'activo\' AND (p.estado IS NULL OR p.estado<>\'completado\')', [$acceso['inscripcion_id'], $acceso['curso_id']]);
      if ((int) $faltan['total'] === 0) {
        $this->run('UPDATE inscripciones SET estado=\'completada\',completada_en=COALESCE(completada_en,NOW()) WHERE inscripcion_id=?', [$acceso['inscripcion_id']]);
        $this->run('INSERT IGNORE INTO certificados (inscripcion_id,folio,nombre_alumno_historico,nombre_curso_historico,nombre_certificador_historico) SELECT i.inscripcion_id,?,a.nombre_completo,c.titulo,p.nombre_completo FROM inscripciones i JOIN usuarios a ON a.usuario_id=i.alumno_id JOIN cursos c ON c.curso_id=i.curso_id JOIN usuarios p ON p.usuario_id=c.profesor_id WHERE i.inscripcion_id=?', [bin2hex(random_bytes(12)), $acceso['inscripcion_id']]);
      }
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function abrirNivel(int $alumno, int $nivel): string
  {
    $datos = $this->row('SELECT i.inscripcion_id,r.recurso_url FROM inscripciones i JOIN accesos_nivel a ON a.inscripcion_id=i.inscripcion_id JOIN niveles n ON n.nivel_id=a.nivel_id AND n.estado=\'activo\' JOIN cursos c ON c.curso_id=i.curso_id AND c.estado=\'activo\' JOIN recursos_nivel r ON r.nivel_id=n.nivel_id AND r.tipo=\'video\' WHERE i.alumno_id=? AND n.nivel_id=? AND i.estado<>\'cancelada\' ORDER BY r.orden LIMIT 1', [$alumno, $nivel]);
    if (!$datos)
      throw new RuntimeException('Video no disponible.');
    $this->run('INSERT INTO progreso_niveles (inscripcion_id,nivel_id,estado,iniciado_en) VALUES (?,?,\'en_progreso\',NOW()) ON DUPLICATE KEY UPDATE iniciado_en=COALESCE(iniciado_en,NOW()),actualizado_en=NOW()', [$datos['inscripcion_id'], $nivel]);
    if (preg_match('#^cursos/recursos/[a-f0-9]{32}\.(mp4|webm)$#', $datos['recurso_url']))
      return (new \Services\FirebaseStorage())->getUrl($datos['recurso_url']);
    if (!filter_var($datos['recurso_url'], FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $datos['recurso_url']))
      throw new RuntimeException('Video no disponible.');
    return $datos['recurso_url'];
  }

  public function abrirRecurso(int $alumno, int $recurso): string
  {
    $fila = $this->row('SELECT r.recurso_url FROM recursos_nivel r JOIN niveles n ON n.nivel_id=r.nivel_id AND n.estado=\'activo\' JOIN cursos c ON c.curso_id=n.curso_id AND c.estado=\'activo\' JOIN accesos_nivel a ON a.nivel_id=n.nivel_id JOIN inscripciones i ON i.inscripcion_id=a.inscripcion_id AND i.curso_id=c.curso_id WHERE r.recurso_id=? AND i.alumno_id=? AND i.estado<>\'cancelada\' LIMIT 1', [$recurso, $alumno]);
    if (!$fila || !$fila['recurso_url'])
      throw new RuntimeException('Recurso no disponible.');
    if (str_starts_with($fila['recurso_url'], 'cursos/recursos/'))
      return (new \Services\FirebaseStorage())->getUrl($fila['recurso_url']);
    if (!filter_var($fila['recurso_url'], FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $fila['recurso_url']))
      throw new RuntimeException('Recurso no disponible.');
    return $fila['recurso_url'];
  }

  public function certificado(int $alumno, int $curso): array|false
  {
    return $this->row('SELECT ce.*,i.completada_en FROM certificados ce JOIN inscripciones i ON i.inscripcion_id=ce.inscripcion_id WHERE i.alumno_id=? AND i.curso_id=?', [$alumno, $curso]);
  }

  public function comentar(int $alumno, int $curso, int $calificacion, string $texto): void
  {
    if ($calificacion < 1 || $calificacion > 5 || mb_strlen($texto) < 3 || mb_strlen($texto) > 2000 || !$this->certificado($alumno, $curso))
      throw new RuntimeException('Comentario inválido.');
    $this->run('INSERT INTO comentarios (alumno_id,curso_id,calificacion,comentario) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE calificacion=VALUES(calificacion),comentario=VALUES(comentario),estado=\'visible\'', [$alumno, $curso, $calificacion, $texto]);
  }

  public function usuarios(): array
  {
    return $this->rows('SELECT u.usuario_id,u.nombre_completo,u.nombre_usuario,u.correo,u.estado,u.registrado_en,r.nombre AS rol FROM usuarios u JOIN roles r ON r.rol_id=u.rol_id ORDER BY u.registrado_en DESC');
  }

  public function estadoUsuario(int $admin, int $usuario, string $estado): void
  {
    if ($admin === $usuario || !in_array($estado, ['activo', 'bloqueado'], true))
      throw new RuntimeException('Acción inválida.');
    $this->run('UPDATE usuarios SET estado=?,intentos_fallidos=0,bloqueado_por=IF(?=\'bloqueado\',?,NULL),bloqueado_en=IF(?=\'bloqueado\',NOW(),NULL),motivo_bloqueo=IF(?=\'bloqueado\',\'Bloqueo administrativo\',NULL) WHERE usuario_id=?', [$estado, $estado, $admin, $estado, $estado, $usuario]);
  }

  public function comentarios(): array
  {
    return $this->rows('SELECT co.comentario_id,co.comentario,co.estado,co.motivo_moderacion,u.nombre_completo,c.titulo FROM comentarios co JOIN usuarios u ON u.usuario_id=co.alumno_id JOIN cursos c ON c.curso_id=co.curso_id ORDER BY co.creado_en DESC');
  }

  public function moderar(int $admin, int $id, string $motivo): void
  {
    if (mb_strlen($motivo) < 3 || mb_strlen($motivo) > 500)
      throw new RuntimeException('Motivo inválido.');
    $this->run('UPDATE comentarios SET estado=\'oculto\',moderado_en=NOW(),moderado_por=?,motivo_moderacion=? WHERE comentario_id=?', [$admin, $motivo, $id]);
  }

  public function mensajes(int $usuario): array
  {
    return $this->rows('SELECT m.contenido,m.enviado_en,u.nombre_completo,u.foto_url FROM mensajes m JOIN usuarios u ON u.usuario_id=m.remitente_id JOIN conversaciones c ON c.conversacion_id=m.conversacion_id WHERE c.participante_1_id=? OR c.participante_2_id=? ORDER BY m.enviado_en DESC LIMIT 100', [$usuario, $usuario]);
  }

  public function enviarMensaje(int $usuario, int $destinatario, string $contenido): void
  {
    $receptor = $this->usuario($destinatario);
    if (!$receptor || $receptor['estado'] !== 'activo')
      throw new RuntimeException('Destinatario no disponible.');
    if ($usuario === $destinatario || mb_strlen($contenido) < 1 || mb_strlen($contenido) > 2000 || !$this->usuario($destinatario))
      throw new RuntimeException('Mensaje inválido.');
    $a = min($usuario, $destinatario);
    $b = max($usuario, $destinatario);
    $this->db->beginTransaction();
    try {
      $conversacion = $this->row('SELECT conversacion_id FROM conversaciones WHERE participante_1_id=? AND participante_2_id=? LIMIT 1', [$a, $b]);
      if (!$conversacion) {
        $this->run('INSERT INTO conversaciones (participante_1_id,participante_2_id) VALUES (?,?)', [$a, $b]);
        $conversacion = ['conversacion_id' => $this->db->lastInsertId()];
      }
      $this->run('INSERT INTO mensajes (conversacion_id,remitente_id,contenido) VALUES (?,?,?)', [$conversacion['conversacion_id'], $usuario, $contenido]);
      $this->db->commit();
    } catch (\Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  public function ventas(int $profesor, array $filtros = []): array
  {
    $w = ['c.profesor_id=?'];
    $p = [$profesor];
    if (!empty($filtros['desde'])) {
      $w[] = 'DATE(c.creado_en)>=?';
      $p[] = $filtros['desde'];
    }
    if (!empty($filtros['hasta'])) {
      $w[] = 'DATE(c.creado_en)<=?';
      $p[] = $filtros['hasta'];
    }
    if (!empty($filtros['categoria_id'])) {
      $w[] = 'EXISTS(SELECT 1 FROM curso_categorias cc WHERE cc.curso_id=c.curso_id AND cc.categoria_id=?)';
      $p[] = (int) $filtros['categoria_id'];
    }
    if (($filtros['activos'] ?? '') === '1')
      $w[] = 'c.estado=\'activo\'';
    return $this->rows('SELECT c.curso_id,c.titulo,c.creado_en,c.estado,(SELECT COUNT(*) FROM inscripciones i WHERE i.curso_id=c.curso_id) AS alumnos,(SELECT COALESCE(AVG((SELECT COUNT(*) FROM progreso_niveles pr WHERE pr.inscripcion_id=i.inscripcion_id AND pr.estado=\'completado\')),0) FROM inscripciones i WHERE i.curso_id=c.curso_id) AS avance_promedio,(SELECT COALESCE(SUM(d.importe),0) FROM detalles_pedido d LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN pedidos p ON p.pedido_id=d.pedido_id AND p.estado=\'pagado\' WHERE COALESCE(d.curso_id,n.curso_id)=c.curso_id) AS ingresos FROM cursos c WHERE ' . implode(' AND ', $w) . ' ORDER BY c.creado_en DESC', $p);
  }

  public function detalleVentas(int $profesor, int $curso): array
  {
    if (!$this->row('SELECT curso_id FROM cursos WHERE curso_id=? AND profesor_id=?', [$curso, $profesor]))
      throw new RuntimeException('Curso no encontrado.');
    return $this->rows('SELECT u.nombre_completo,i.inscrito_en,(SELECT COUNT(*) FROM progreso_niveles pr WHERE pr.inscripcion_id=i.inscripcion_id AND pr.estado=\'completado\') AS niveles_completados,COALESCE((SELECT SUM(d.importe) FROM detalles_pedido d LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN pedidos p ON p.pedido_id=d.pedido_id WHERE COALESCE(d.curso_id,n.curso_id)=i.curso_id AND p.alumno_id=i.alumno_id AND p.estado=\'pagado\'),0) AS pagado,(SELECT m.nombre FROM detalles_pedido d LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN pedidos p ON p.pedido_id=d.pedido_id JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id WHERE COALESCE(d.curso_id,n.curso_id)=i.curso_id AND p.alumno_id=i.alumno_id AND p.estado=\'pagado\' LIMIT 1) AS metodo FROM inscripciones i JOIN usuarios u ON u.usuario_id=i.alumno_id WHERE i.curso_id=? ORDER BY i.inscrito_en DESC', [$curso]);
  }

  public function ventasPorMetodo(int $profesor): array
  {
    return $this->rows('SELECT m.nombre AS metodo,SUM(d.importe) AS total FROM detalles_pedido d LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN cursos c ON c.curso_id=COALESCE(d.curso_id,n.curso_id) JOIN pedidos p ON p.pedido_id=d.pedido_id AND p.estado=\'pagado\' JOIN pagos pa ON pa.pedido_id=p.pedido_id JOIN metodos_pago m ON m.metodo_pago_id=pa.metodo_pago_id WHERE c.profesor_id=? GROUP BY m.nombre ORDER BY m.nombre', [$profesor]);
  }

  public function reporteUsuarios(): array
  {
    return $this->rows('SELECT u.nombre_usuario,u.nombre_completo,u.registrado_en,r.nombre AS rol,(SELECT COUNT(*) FROM cursos c WHERE c.profesor_id=u.usuario_id) AS cursos,(SELECT COUNT(*) FROM inscripciones i WHERE i.alumno_id=u.usuario_id) AS inscripciones,(SELECT COUNT(*) FROM inscripciones i WHERE i.alumno_id=u.usuario_id AND i.estado=\'completada\') AS terminados,(SELECT COALESCE(SUM(d.importe),0) FROM cursos c JOIN detalles_pedido d LEFT JOIN niveles n ON n.nivel_id=d.nivel_id JOIN pedidos p ON p.pedido_id=d.pedido_id AND p.estado=\'pagado\' WHERE COALESCE(d.curso_id,n.curso_id)=c.curso_id AND c.profesor_id=u.usuario_id) AS ganancias FROM usuarios u JOIN roles r ON r.rol_id=u.rol_id WHERE u.estado=\'activo\' ORDER BY u.registrado_en DESC');
  }
}
