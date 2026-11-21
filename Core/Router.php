<?php

namespace Core;

use Core\Middleware\Middleware;

class Router
{
  protected $routes = [];

  public function add($method, $uri, $controller)
  {
    $this->routes[] = [
      'method' => $method,
      'uri' => $uri,
      'controller' => $controller,
      'middleware' => null
    ];

    return $this;
  }

  function only($middleware)
  {
    $this->routes[count($this->routes) - 1]['middleware'] = $middleware;

    return $this;
  }

  public function get($uri, $controller)
  {
    return $this->add('GET', $uri, $controller);
  }

  public function post($uri, $controller)
  {
    return $this->add('POST', $uri, $controller);
  }

  public function put($uri, $controller)
  {
    return $this->add('PUT', $uri, $controller);
  }

  public function patch($uri, $controller)
  {
    return $this->add('PATCH', $uri, $controller);
  }

  public function delete($uri, $controller)
  {
    return $this->add('DELETE', $uri, $controller);
  }

  public function route($uri, $method)
  {
    if ($uri === '/api/v1/cursos') {
      (new Middleware())->resolve('cors');

      if (strtoupper($method) === 'OPTIONS') {
        http_response_code(204);
        return;
      }

      if (strtoupper($method) === 'GET') {
        require base_path('controllers/api/cursos.php');
        return;
      }

      http_response_code(405);
      header('Content-Type: application/json; charset=utf-8');
      header('Allow: GET, OPTIONS');
      echo json_encode(['error' => 'Método no permitido.']);
      return;
    }

    if (
      in_array(strtoupper($method), ['GET', 'POST', 'OPTIONS'], true) &&
      preg_match(
        '#^/api/v1/cursos/[1-9][0-9]*/comentarios/?$#',
        $uri
      )
    ) {
      (new Middleware())->resolve('cors');

      if (strtoupper($method) === 'OPTIONS') {
        http_response_code(204);
        return;
      }

      require base_path('controllers/api/comentarios.php');
      return;
    }

    if ($uri === '/api/v1/sesion') {
      (new Middleware())->resolve('cors');
      header('Content-Type: application/json; charset=utf-8');
      if (strtoupper($method) === 'OPTIONS') {
        http_response_code(204);
        return;
      }
      if (strtoupper($method) !== 'GET') {
        http_response_code(405);
        header('Allow: GET, OPTIONS');
        echo json_encode(['error' => 'Método no permitido.']);
        return;
      }
      $usuario = empty($_SESSION['user_id']) ? false : (new \Models\User())->buscarSesion((int) $_SESSION['user_id']);
      if (!$usuario || $usuario['estado'] !== 'activo') {
        http_response_code(401);
        echo json_encode(['error' => 'Inicia sesión.']);
        return;
      }
      $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
      echo json_encode(['usuario_id' => (int) $_SESSION['user_id'], 'csrf_token' => $_SESSION['csrf_token']]);
      return;
    }

    if (str_starts_with($uri, '/api/')) {
      http_response_code(404);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['error' => 'Recurso no encontrado.']);
      return;
    }

    //dd($uri, $method, $this->routes);
    foreach ($this->routes as $route) {
      if ($route['uri'] === $uri && $route['method'] === strtoupper($method)) {
        (new Middleware())->resolve($route['middleware']);
        return require base_path($route['controller']);
      }
    }
    abort();
  }
}
