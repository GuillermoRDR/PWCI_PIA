<?php

namespace Core;

use PDO;

class Database
{
  public PDO $connection;
  public mixed $statement;

  public function __construct(mixed $config, $username = 'root', $password = '')
  {
    // ['host' => 'localhost', 'dbname' => 'x'] -> mysql:host=localhost;dbname=x
    $dsn = 'mysql:' . http_build_query($config, '', ';');

    $this->connection = new PDO( $dsn, $username, $password,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
      ]
    );
  }

  public function query(mixed $query, $params = [])
  {
    $this->statement = $this->connection->prepare($query);

    $this->statement->execute($params);

    return $this;
  }

  public function get()
  {
    return $this->statement->fetchAll();
  }

  public function first()
  {
    return $this->statement->fetch();
  }
}
