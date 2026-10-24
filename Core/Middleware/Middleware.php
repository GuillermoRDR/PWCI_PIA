<?php

namespace Core\Middleware;

class Middleware
{
  public const MAP = [
    'auth' => Auth::class,
  ];

  public function resolve($name)
  {
    if (!$name) {
      return;
    }

    $middleware = static::MAP[$name] ?? false;

    if (!$middleware) {
      throw new \Exception("No middleware found with name {$name}");
    }

    (new $middleware())->handle();
  }
}
