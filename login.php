<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'api/config.php';
$mensaje = "";
$exito = false;

if (isset($_GET['registrado'])) {
    $exito = true;
    $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, nombre, password_hash FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre'];
            header("Location: dashboard.php");
            exit();
        } else {
            $mensaje = "Correo o contraseña incorrectos.";
        }
    } else {
        $mensaje = "Ingresa tu correo y contraseña.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - ConstruTec</title>
  <style>
    :root { --bg-color: #0f172a; --card-bg: #1e293b; --primary: #38bdf8; --text-main: #f8fafc; --text-muted: #94a3b8; }
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: var(--bg-color); color: var(--text-main); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
    .card { background-color: var(--card-bg); width: 100%; max-width: 380px; border-radius: 20px; padding: 32px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.05); }
    h2 { font-size: 1.5rem; text-align: center; margin-bottom: 8px; }
    p.subtitle { text-align: center; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px; }
    .alert { padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 16px; text-align: center; }
    .alert.error { background: rgba(248, 113, 113, 0.15); color: #f87171; border: 1px solid rgba(248, 113, 113, 0.3); }
    .alert.success { background: rgba(52, 211, 153, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); }
    .form-group { margin-bottom: 16px; }
    label { display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; }
    input { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #334155; background: #0f172a; color: #fff; font-size: 0.95rem; }
    input:focus { outline: none; border-color: var(--primary); }
    button { width: 100%; padding: 12px; background: var(--primary); border: none; border-radius: 10px; color: #0f172a; font-weight: bold; font-size: 1rem; cursor: pointer; margin-top: 8px; }
    .footer-link { text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted); }
    .footer-link a { color: var(--primary); text-decoration: none; font-weight: 600; }
  </style>
</head>
<body>
  <div class="card">
    <h2>Iniciar Sesión</h2>
    <p class="subtitle">Plataforma ConstruTec</p>
    <?php if ($mensaje): ?>
      <div class="alert <?= $exito ? 'success' : 'error' ?>"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Correo Electrónico</label>
        <input type="email" name="email" required placeholder="correo@ejemplo.com">
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit">Ingresar</button>
    </form>
    <div class="footer-link">
      ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
    </div>
  </div>
</body>
</html>