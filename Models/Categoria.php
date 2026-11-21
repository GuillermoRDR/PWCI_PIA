<?php

namespace Models;

use Core\App;
use Core\Database;

class Categoria
{
  private Database $db;

  public function __construct(?Database $db = null)
  {
    $this->db = $db ?? App::resolve(Database::class);
  }

  public function activas(): array
  {
    return $this->db->query(
      "SELECT categoria_id AS id, nombre, descripcion
       FROM categorias
       WHERE estado = 'activa'
       ORDER BY nombre ASC"
    )->get();
  }
}
