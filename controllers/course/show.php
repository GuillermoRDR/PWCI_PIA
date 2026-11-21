<?php

use Models\CursoCatalogo;
use Models\Comentario;
use Models\Portal;
use Models\Categoria;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1)
  abort(404);
$catalogo = new CursoCatalogo();
$curso = $catalogo->findActivo($id);
if (!$curso)
  abort(404);
$niveles = $catalogo->nivelesActivos($id);
$comentarios = (new Comentario())->obtenerPorCurso($id);
$categorias = (new Categoria())->activas();
$portal = new Portal();
$alumno = ($_SESSION['rol'] ?? '') === 'alumno' ? (int) $_SESSION['user_id'] : 0;
$acceso = $alumno ? $portal->acceso($alumno, $id) : false;
$accesosNivel = $alumno ? array_column($portal->accesosNivel($alumno, $id), 'nivel_id') : [];
$progreso = $alumno && $acceso ? array_column($portal->progreso($alumno, $id), 'nivel_id') : [];
$certificado = $alumno ? $portal->certificado($alumno, $id) : false;
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
$aviso = $_SESSION['aviso'] ?? null;
unset($_SESSION['aviso']);
if ($aviso)
  $_SESSION['aviso'] = $aviso;
view('detalle_curso.php', compact('curso', 'niveles', 'categorias', 'comentarios', 'acceso', 'accesosNivel', 'progreso', 'certificado', 'aviso', 'alumno'));
