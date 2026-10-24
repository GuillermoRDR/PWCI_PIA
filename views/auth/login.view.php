<?php require base_path('views/partials/head.php')?>
<?php require base_path('views/partials/nav.php') ?>

<main>
  <form id="loginForm" action="index.php?action=login_process" method="POST" novalidate>

    <div class="form-group">
      <label for="email">Correo Electrónico</label>
      <div class="input-group">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" id="email" name="email" class="form-control" placeholder="ejemplo@correo.com" required>
      </div>
      <span id="emailError" class="js-error">Ingresa un correo electrónico válido.</span>
    </div>

    <div class="form-group">
      <label for="password">Contraseña</label>
      <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <span id="passwordError" class="js-error">La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.</span>
    </div>

    <button type="submit" class="btn-submit">Ingresar</button>
  </form>

  <div class="login-footer">
    <p>¿No tienes una cuenta? <a href="index.php?action=register">Regístrate aquí</a></p>
  </div>
</main>