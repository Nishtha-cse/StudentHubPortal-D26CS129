<?php
// dashboard.php : ADMIN ONLY. Stats + latest registrations
require_once __DIR__ . '/includes/auth.php';
require_role(['admin']);               // RBAC check -> 403 for everyone else

/* ---------- Dashboard queries ---------- */
$totalUsers    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalStudents = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalEvents   = (int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$totalRegs     = (int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();

$roleStats = $pdo->query(
    'SELECT r.name, COUNT(u.id) AS total
     FROM roles r LEFT JOIN users u ON u.role_id = r.id
     GROUP BY r.id, r.name'
)->fetchAll();

$latest = $pdo->query(
    'SELECT s.name AS student, s.enrollment_no, e.title AS event, g.registered_at
     FROM registrations g
     JOIN students s ON s.id = g.student_id
     JOIN events   e ON e.id = g.event_id
     ORDER BY g.registered_at DESC
     LIMIT 5'
)->fetchAll();

log_action('VIEW_DASHBOARD', 'Admin opened dashboard');

$pageTitle = 'Dashboard';
require __DIR__ . '/includes/header.php';

$cards = [
    ['Total Users',   $totalUsers,    'bi-person-badge',   'bg-primary'],
    ['Students',      $totalStudents, 'bi-mortarboard',    'bg-success'],
    ['Events',        $totalEvents,   'bi-calendar-event', 'bg-warning text-dark'],
    ['Registrations', $totalRegs,     'bi-clipboard-check','bg-danger'],
];
?>
<h3 class="mb-4">Welcome, <?= e(current_user()['name']) ?> 👋</h3>

<div class="row g-3 mb-4">
  <?php foreach ($cards as [$label, $value, $icon, $bg]): ?>
    <div class="col-6 col-lg-3">
      <div class="card stat-card <?= $bg ?> shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="small text-uppercase"><?= $label ?></div>
            <div class="display-6 fw-bold"><?= $value ?></div>
          </div>
          <i class="bi <?= $icon ?>"></i>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Latest Registrations</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead><tr><th>#</th><th>Student</th><th>Enrollment</th><th>Event</th><th>Registered At</th></tr></thead>
          <tbody>
          <?php foreach ($latest as $i => $r): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= e($r['student']) ?></td>
              <td><?= e($r['enrollment_no']) ?></td>
              <td><?= e($r['event']) ?></td>
              <td><?= e($r['registered_at']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$latest): ?><tr><td colspan="5" class="text-center text-muted">No registrations yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white fw-semibold">Users by Role</div>
      <ul class="list-group list-group-flush">
        <?php foreach ($roleStats as $r): ?>
          <li class="list-group-item d-flex justify-content-between text-capitalize">
            <?= e($r['name']) ?>
            <span class="badge bg-dark rounded-pill"><?= (int)$r['total'] ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
