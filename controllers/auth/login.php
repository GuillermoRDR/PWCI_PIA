<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // dd($email, $password);

  $userModel = new \Models\User();
  $user = null;

  // dd($user);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header('Location: /');
    exit;
  } else {
    $error = 'Invalid email or password';
  }
}

view('auth/login.view.php', [
  'error' => $error ?? false,
  'errors' => $error ?? [],
  'email' => $email ?? ''
]);
