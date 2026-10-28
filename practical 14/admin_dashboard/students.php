<?php
// students.php : admin + faculty
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'faculty']);

$students = $pdo->query('SELECT id, enrollment_no, name, department, created_at FROM students ORDER BY id DESC')->fetchAll();

$pageTitle = 'Students';
require __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Students <span class="badge bg-secondary"><?= count($students) ?></span></h3>
<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead><tr><th>#</th><th>Enrollment No</th><th>Name</th><th>Department</th><th>Added On</th></tr></thead>
      <tbody>
      <?php foreach ($students as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= e($s['enrollment_no']) ?></td>
          <td><?= e($s['name']) ?></td>
          <td><?= e($s['department']) ?></td>
          <td><?= e($s['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
