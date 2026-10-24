<?php

use Models\Curso;

$cursos = (new Curso())->all();

view('home.php', ['cursos' => $cursos, 'user' => $user ?? null]);

