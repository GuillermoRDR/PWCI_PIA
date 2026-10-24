<?php

namespace Core;

class Container
{
  protected $bindings = [];
  protected $instances = [];

  public function bind($key, $resolver)
  {
    $this->bindings[$key] = $resolver;
  }

  public function singleton($key, $resolver)
  {
    $this->bind($key, function () use ($key, $resolver) {
      return $this->instances[$key] ??= call_user_func($resolver);
    });
  }

  public function resolve($key)
  {
    if (! array_key_exists($key, $this->bindings)) {
      throw new \Exception("No matching binding found for {$key}");
    }

    return call_user_func($this->bindings[$key]);
  }
}
