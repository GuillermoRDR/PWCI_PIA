<?php

use Core\App;
use Core\Container;
use Core\Database;

$container = new Container();

$container->singleton(Database::class, function () {
  $config = require base_path('config.php');

  return new Database($config['database'], $config['username'], $config['password']);
});

App::setContainer($container);
