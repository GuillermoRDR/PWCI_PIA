<?php
class Database
{
  private $host = "localhost";
  private $db_name = "pia_bdm";
  private $username = "root";
  private $password = "@DarkSoulDragon020804";
  public $conn;

  public function connect()
  {
    $this->conn = null;

    try {
      $this->conn = new PDO(
        "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
        $this->username,
        $this->password
      );
      //echo "Conexión exitosa a la base de datos: " . $this->db_name . "\n";

      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $this->conn;
    } catch (PDOException $e) {
      die("Error de conexión: " . $e->getMessage());
    }
  }
}
