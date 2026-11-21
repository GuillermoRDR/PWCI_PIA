<?php

use Models\Categoria;
use Models\CursoCatalogo;

$cursoModel = new CursoCatalogo();
$categorias = (new Categoria())->activas();
$cursosRecientes = $cursoModel->recientes();
$cursosMejorCalificados = $cursoModel->mejorCalificados();
$cursosMasVendidos = $cursoModel->masVendidos();

view('home.php', [
  'categorias' => $categorias,
  'cursosRecientes' => $cursosRecientes,
  'cursosMejorCalificados' => $cursosMejorCalificados,
  'cursosMasVendidos' => $cursosMasVendidos,
  'user' => $user ?? null,
]);

