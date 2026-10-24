<?php
namespace Models;

use Core\App;
use Core\Database;

class Curso
{
  private $conn;
  private $table = 'cursos';

  public function __construct()
  {
    $this->conn = App::resolve(Database::class);
  }

  public function all()
  {
    // Usando el método query y get() de tu clase Database personalizada:
    return App::resolve(Database::class)
      ->query("SELECT * FROM cursos")
      ->get();
  }
}
