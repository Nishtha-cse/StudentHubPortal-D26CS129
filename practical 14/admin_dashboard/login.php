<?php
// login.php
require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    header('Location: ' . (current_user()['role'] === 'admin' ? 'dashboard.php' : 'events.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT u.id, u.name, u.email, u.password_hash, r.name AS role
         FROM users u JOIN roles r ON r.id = u.role_id
         WHERE u.email = ?'
    );
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'role' => $u['role']];
        log_action('LOGIN', 'Successful login as ' . $u['role']);
        header('Location: ' . ($u['role'] === 'admin' ? 'dashboard.php' : 'events.php'));
        exit;
    }
    $error = 'Invalid email or password.';
    log_action('LOGIN_FAILED', 'Email tried: ' . $email);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center" style="min-height:100vh">
<div class="container" style="max-width:420px">
  <div class="card shadow border-0">
    <div class="card-body p-4">
      <h4 class="text-center mb-3">Admin Panel Login</h4>
      <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
      <form method="post" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>
      <hr>
      <small class="text-muted d-block">Demo accounts (password: <code>password</code>)</small>
      <small class="text-muted">admin@demo.com · faculty@demo.com · student@demo.com</small>
    </div>
  </div>
</div>
</body>
</html>
