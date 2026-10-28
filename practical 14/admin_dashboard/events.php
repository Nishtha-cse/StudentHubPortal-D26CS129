<?php
// events.php : all roles can view; only admin can add / delete (and those actions are audit-logged)
require_once __DIR__ . '/includes/auth.php';
require_role(['admin', 'faculty', 'student']);

$isAdmin = current_user()['role'] === 'admin';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_role(['admin']);          // server-side guard: non-admin POST => 403
    csrf_check();

    if (($_POST['do'] ?? '') === 'add') {
        $title = trim($_POST['title'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $date  = $_POST['event_date'] ?? '';
        if ($title && $venue && $date) {
            $pdo->prepare('INSERT INTO events (title, venue, event_date) VALUES (?, ?, ?)')
                ->execute([$title, $venue, $date]);
            log_action('ADD_EVENT', "Added event: $title");
            $msg = 'Event added.';
        }
    } elseif (($_POST['do'] ?? '') === 'delete') {
        $id  = (int)($_POST['id'] ?? 0);
        $row = $pdo->prepare('SELECT title FROM events WHERE id = ?');
        $row->execute([$id]);
        if ($ev = $row->fetch()) {
            $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
            log_action('DELETE_EVENT', "Deleted event #$id: " . $ev['title']);
            $msg = 'Event deleted.';
        }
    }
}

$events = $pdo->query(
    'SELECT e.id, e.title, e.venue, e.event_date, COUNT(g.id) AS regs
     FROM events e LEFT JOIN registrations g ON g.event_id = e.id
     GROUP BY e.id ORDER BY e.event_date'
)->fetchAll();

$pageTitle = 'Events';
require __DIR__ . '/includes/header.php';
?>
<h3 class="mb-3">Events <span class="badge bg-secondary"><?= count($events) ?></span></h3>
<?php if ($msg): ?><div class="alert alert-success py-2"><?= e($msg) ?></div><?php endif; ?>

<?php if ($isAdmin): ?>
<div class="card shadow-sm border-0 mb-4">
  <div class="card-header bg-white fw-semibold">Add Event (admin only)</div>
  <div class="card-body">
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="do" value="add">
      <div class="col-md-4"><input name="title" class="form-control" placeholder="Title" required></div>
      <div class="col-md-3"><input name="venue" class="form-control" placeholder="Venue" required></div>
      <div class="col-md-3"><input type="date" name="event_date" class="form-control" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead><tr><th>#</th><th>Title</th><th>Venue</th><th>Date</th><th>Registrations</th><?php if ($isAdmin): ?><th></th><?php endif; ?></tr></thead>
      <tbody>
      <?php foreach ($events as $ev): ?>
        <tr>
          <td><?= $ev['id'] ?></td>
          <td><?= e($ev['title']) ?></td>
          <td><?= e($ev['venue']) ?></td>
          <td><?= e($ev['event_date']) ?></td>
          <td><span class="badge bg-info text-dark"><?= (int)$ev['regs'] ?></span></td>
          <?php if ($isAdmin): ?>
          <td class="text-end">
            <form method="post" onsubmit="return confirm('Delete this event?')">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="do" value="delete">
              <input type="hidden" name="id" value="<?= $ev['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
