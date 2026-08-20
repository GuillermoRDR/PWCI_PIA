<?php
////session_start();
require_once __DIR__ . '/../helpers/auth.php';
if (!isset($_SESSION['usuario'])) {
    header("Location: /index.php?action=loginPage"); // login
    exit;
}else {
  verificarRol([1, 2, 3, 4]); // Administrador, Supervisor, Asegurado, Ajustador    
}
$rol = $_SESSION['usuario']['rol'];
$nombreRol = '';
switch($rol){
    case 1:
        $nombreRol = 'Administrador';
    break;
    case 2:
        $nombreRol = 'Supervisor';
    break;
    case 3:
        $nombreRol = 'Asegurado';
    break;
    case 4:
        $nombreRol = 'Ajustador';
    break;
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>AutoSiniestros · Actualizar Perfil</title>
  <link rel="stylesheet" href="/pages/css/styles.css" />
</head>
<body>

    <header class="nav">
        <div class="nav-inner">
            <a class="brand" href="/index.php?action=loginPage">
            <div class="logo"><span>AS</span></div>
            <div>
                <div style="font-weight:900">AutoSiniestros</div>
            </div>
            </a>
            <nav class="nav-links">
                <a class="pill" href="/index.php?action=dashboard">Dashboard</a>
                <a class="pill" href="/index.php?action=claim_create">Nuevo Siniestro</a>
                <a class="pill" href="/index.php?action=claims_list">Listado / Consulta</a>
                <a class="pill" href="/index.php?action=claim_detail">Detalle</a>
                <a class="pill" href="/index.php?action=approvals">Aprobaciones</a>
                <a class="pill" href="/index.php?action=followup">Seguimiento</a>
                <a class="pill" href="/index.php?action=payments">Pagos</a>
                <a class="pill" href="/index.php?action=guarantees_close">Garantías / Cierre</a>
                <a class="pill" href="/index.php?action=editarPerfil">Actualizar Perfil</a>
                <a class="pillLogout" href="/index.php?action=logout">Cerrar sesión</a>
                <span class="badge">
                Rol:
                <b><?php echo $nombreRol; ?></b>
                </span>
            </nav>
        </div>
    </header>

    <main class="container">

        <section class="card pad" style="margin-bottom:16px;">
            <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">

            <div>
                <img id="previewPerfil" alt="Foto de perfil" src="data:image/jpeg;base64,<?= base64_encode($usuario['foto']) ?>"/>
            </div>

            <div>
                <h1 class="h1" style="margin:0;">
                <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido_paterno']) ?>
                </h1>
                <div class="sub">@<?= htmlspecialchars($usuario['alias']) ?></div>

                <div style="margin-top:8px;">
                    <?php if ($usuario['activo'] == 1): ?>
                        <span class="badge ok">Activo</span>
                    <?php else: ?>
                        <span class="badge">Inactivo</span>
                    <?php endif; ?>
                </div>
            </div>

            </div>
        </section>

        <section class="card pad">
            <h2 style="margin-top:0;">Editar información</h2>

            <form id="actualizarUsuarioForm" action="/index.php?action=actualizarUsuario" method="post" enctype="multipart/form-data">

                <div class="row">
                    <div>
                    <label for="foto">Cambiar foto</label>
                    <input id="foto" type="file" name="foto" accept="image/*" />
                    <!-- Preview -->
                    <img id="preview" src=""/>
                    </div>
                </div>

                <div class="row">
                    <div>
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />
                    </div>
                    <div>
                    <label for="apellidoPaterno">Apellido Paterno</label>
                    <input id="apellidoPaterno" name="apellidoPaterno" value="<?= htmlspecialchars($usuario['apellido_paterno']) ?>" required />
                    </div>
                    <div>
                    <label for="apellidoMaterno">Apellido Materno</label>
                    <input id="apellidoMaterno" name="apellidoMaterno" value="<?= htmlspecialchars($usuario['apellido_materno']) ?>" required />
                    </div>
                </div>

                <div class="row">
                    <div>
                    <label for="fechaNacimiento">Fecha de nacimiento</label>
                    <input id="fechaNacimiento" type="date" name="fechaNacimiento"
                            value="<?= htmlspecialchars($usuario['fecha_nacimiento']) ?>" required />
                    </div>

                    <div>
                    <label for="genero">Género</label>
                    <select id="genero" name="genero" required>
                        <option value="Femenino" <?= $usuario['genero'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                        <option value="Masculino" <?= $usuario['genero'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                        <option value="No binario" <?= $usuario['genero'] === 'No binario' ? 'selected' : '' ?>>No binario</option>
                        <option value="Prefiero no decir" <?= $usuario['genero'] === 'Prefiero no decir' ? 'selected' : '' ?>>Prefiero no decir</option>
                    </select>
                    </div>
                </div>

                <div class="row">
                    <div>
                    <label for="correo">Correo</label>
                    <input id="correo" type="email" name="correo"
                            value="<?= htmlspecialchars($usuario['correo']) ?>" required />
                    </div>

                    <div>
                    <label for="alias">Alias</label>
                    <input id="alias" name="alias"
                            value="<?= htmlspecialchars($usuario['alias']) ?>" required />
                    </div>
                </div>

                <div class="row">
                    <div>
                    <label for="contrasenia">Nueva contraseña</label>
                    <input id="contrasenia" type="password" name="contrasenia"
                            placeholder="Dejar vacío para no cambiar" />
                    </div>

                    <div>
                    <label for="contrasenia2">Confirmar contraseña</label>
                    <input id="contrasenia2" type="password" name="contrasenia2" />
                    </div>
                </div>

                <!-- ACCIONES -->
                <div class="btns">
                    <button class="btn primary" type="submit">Guardar cambios</button>
                    <a class="btn" href="/pages/dashboard.php">Cancelar</a>
                </div>

            </form>
        </section>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/pages/js/actualizar_Usuario.js"></script>
</body>
</html>