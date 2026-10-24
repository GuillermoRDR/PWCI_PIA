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
