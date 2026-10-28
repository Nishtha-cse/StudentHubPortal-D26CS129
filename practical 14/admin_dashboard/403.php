<?php
// 403.php : Forbidden page (included by require_role(), can also be opened directly)
require_once __DIR__ . '/includes/auth.php';
require_login();
http_response_code(403);
$pageTitle = '403 Forbidden';
require __DIR__ . '/includes/header.php';
?>
<div class="text-center py-5">
  <h1 class="display-1 fw-bold text-danger">403</h1>
  <h4 class="mb-3">Access Denied</h4>
  <p class="text-muted">
    Your role (<strong class="text-uppercase"><?= e(current_user()['role']) ?></strong>)
    is not allowed to view this page. This attempt has been recorded.
  </p>
  <a href="<?= current_user()['role'] === 'admin' ? 'dashboard.php' : 'events.php' ?>" class="btn btn-primary">Go Back</a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
