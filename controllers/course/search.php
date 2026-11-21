<?php

use Models\Categoria;
use Models\CursoCatalogo;

$termino = is_string($_GET['q'] ?? null) ? trim(mb_substr($_GET['q'], 0, 150)) : '';
$categoriaId = filter_input(INPUT_GET, 'categoria_id', FILTER_VALIDATE_INT);
$categoriaId = $categoriaId && $categoriaId > 0 ? $categoriaId : null;

$normalizarFecha = static function ($valor): ?string {
  if (!is_string($valor) || $valor === '')
    return null;

  $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
  return $fecha && $fecha->format('Y-m-d') === $valor ? $valor : null;
};

$cursosEncontrados = (new CursoCatalogo())->buscar(
  $termino,
  $categoriaId,
  $normalizarFecha($_GET['desde'] ?? null),
  $normalizarFecha($_GET['hasta'] ?? null)
);

view('resultado_busqueda.php', [
  'categorias' => (new Categoria())->activas(),
  'cursosEncontrados' => $cursosEncontrados,
]);
