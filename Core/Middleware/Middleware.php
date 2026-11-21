<?php
namespace Core\Middleware;

class Middleware
{
  public const MAP = [
    'auth' => Auth::class,
    'admin' => Auth::class,
    'profesor' => Auth::class,
    'alumno' => Auth::class,
    'cors' => Cors::class
  ];

  public function resolve($name)
  {
    if (!$name) {
      return;
    }

    if (!isset(self::MAP[$name])) {
      throw new \RuntimeException('Middleware no registrado.');
    }

    if ($name === 'cors') {
      (new Cors())->handle();
      return;
    }

    $rol = match ($name) {
      'admin' => 'administrador',
      'profesor' => 'profesor',
      'alumno' => 'alumno',
      default => null
    };

    (new Auth())->handle($rol);
  }
}
