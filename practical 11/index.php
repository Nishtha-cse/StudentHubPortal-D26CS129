<?php
session_start();
include 'db.php';
include 'helpers.php';

$courses = ['Computer Science', 'Web Development', 'Data Science'];
$genders = ['Male', 'Female'];

// ---------- CREATE ----------
if (isset($_POST['add_student'])) {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $course = $_POST['course'] ?? '';
    $gender = $_POST['gender'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || !in_array($course, $courses, true) || !in_array($gender, $genders, true)) {
        flash('error', 'Please fill all fields with valid values.');
    } else {
        try {
            run_query($conn,
                "INSERT INTO students (name, email, course, gender, status) VALUES (?, ?, ?, ?, 'active')",
                'ssss', [$name, $email, $course, $gender]);
            flash('success', "Student '$name' added successfully.");
        } catch (Exception $e) {
            flash('error', 'Could not add student (email may already exist).');
        }
    }
    header("Location: index.php");
    exit();
}

// ---------- DEACTIVATE (soft delete: status -> inactive) ----------
if (isset($_POST['deactivate'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = run_query($conn, "UPDATE students SET status = 'inactive' WHERE id = ?", 'i', [$id]);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            flash('success', "Student #$id deactivated (status changed to inactive).");
        } else {
            flash('error', 'Student not found or already inactive.');
        }
    } catch (Exception $e) {
        flash('error', 'Failed to deactivate student.');
    }
    header("Location: index.php");
    exit();
}

// ---------- ACTIVATE ----------
if (isset($_POST['activate'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = run_query($conn, "UPDATE students SET status = 'active' WHERE id = ?", 'i', [$id]);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            flash('success', "Student #$id activated again.");
        } else {
            flash('error', 'Student not found or already active.');
        }
    } catch (Exception $e) {
        flash('error', 'Failed to activate student.');
    }
    header("Location: index.php");
    exit();
}

// ---------- DELETE (permanent) ----------
if (isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    try {
        $stmt = run_query($conn, "DELETE FROM students WHERE id = ?", 'i', [$id]);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            flash('success', "Student #$id permanently deleted.");
        } else {
            flash('error', 'Student not found.');
        }
    } catch (Exception $e) {
        flash('error', 'Failed to delete student.');
    }
    header("Location: index.php");
    exit();
}

// ---------- READ (search + filters) ----------
$search        = trim($_GET['search'] ?? '');
$course_filter = $_GET['course_filter'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

list($where, $types, $params) = build_where($_GET);
$stmt   = run_query($conn, "SELECT * FROM students $where ORDER BY id ASC", $types, $params);
$result = mysqli_stmt_get_result($stmt);

$export_url = 'export.php?' . http_build_query([
    'search' => $search, 'course_filter' => $course_filter, 'status_filter' => $status_filter
]);

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Management System</title>
<style>
* { box-sizing: border-box; }
body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f3f6; margin: 0; padding: 30px 20px; color: #222; }
.card { background: #fff; max-width: 1000px; margin: 0 auto 20px; padding: 22px 26px; border-radius: 10px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
h2 { margin: 0 0 16px; font-size: 20px; }
input, select { padding: 8px 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; }
.btn { padding: 8px 14px; border: none; border-radius: 4px; color: #fff; font-weight: 600; font-size: 13px; cursor: pointer; text-decoration: none; display: inline-block; }
.btn-primary { background: #3f5bdb; }
.btn-grey { background: #6c757d; }
.btn-green { background: #217346; }
.row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
table { width: 100%; border-collapse: collapse; font-size: 12px; }
thead th { background: #1f2937; color: #fff; text-align: left; padding: 10px 8px; }
td { padding: 12px 8px; border-bottom: 1px solid #eee; }
tr.inactive-row td { color: #999; background: #fafafa; }
.badge { padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
.badge.active { background: #d4f5dd; color: #1b7a38; }
.badge.inactive { background: #fde2e2; color: #b42318; }
.act { background: none; border: none; padding: 0; margin-right: 8px; font-size: 11px; font-weight: 700; cursor: pointer; text-decoration: none; }
.act.edit { color: #3f5bdb; }
.act.deact { color: #e67e00; }
.act.activate { color: #1b7a38; }
.act.del { color: #c62828; }
.alert { max-width: 1000px; margin: 0 auto 16px; padding: 10px 14px; border-radius: 6px; font-size: 14px; }
.alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
/* Popups */
.modal-bg { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); align-items: center; justify-content: center; z-index: 10; }
.modal-bg.show { display: flex; }
.modal { background: #fff; padding: 24px; border-radius: 8px; width: 360px; }
.modal h3 { margin: 0 0 8px; }
.modal p { margin: 0; font-size: 14px; color: #444; }
.modal .actions { text-align: right; margin-top: 20px; display: flex; gap: 8px; justify-content: flex-end; }
.btn-orange { background: #e67e00; }
.btn-red { background: #c62828; }
</style>
</head>
<body>

<?php if ($flash): ?>
<div class="alert <?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['msg']); ?></div>
<?php endif; ?>

<div class="card">
<h2>Add New Student</h2>
<form method="POST" action="index.php" class="row">
<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email" required>
<select name="course" required>
<option value="">Select Course</option>
<?php foreach ($courses as $c): ?><option value="<?php echo $c; ?>"><?php echo $c; ?></option><?php endforeach; ?>
</select>
<select name="gender" required>
<option value="">Select Gender</option>
<?php foreach ($genders as $g): ?><option value="<?php echo $g; ?>"><?php echo $g; ?></option><?php endforeach; ?>
</select>
<button type="submit" name="add_student" class="btn btn-primary">+ Add Student</button>
</form>
</div>

<div class="card">
<h2>Student List</h2>
<form method="GET" action="index.php" class="row" style="margin-bottom:18px;">
<input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
<select name="course_filter">
<option value="">All Courses</option>
<?php foreach ($courses as $c): ?>
<option value="<?php echo $c; ?>" <?php if ($course_filter === $c) echo 'selected'; ?>><?php echo $c; ?></option>
<?php endforeach; ?>
</select>
<select name="status_filter">
<option value="">All Students</option>
<option value="active" <?php if ($status_filter === 'active') echo 'selected'; ?>>Active</option>
<option value="inactive" <?php if ($status_filter === 'inactive') echo 'selected'; ?>>Inactive</option>
</select>
<button type="submit" class="btn btn-primary">Search / Filter</button>
<a href="index.php" class="btn btn-grey">Reset</a>
<a href="<?php echo htmlspecialchars($export_url); ?>" class="btn btn-green">Export CSV</a>
</form>

<table>
<thead>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Gender</th><th>Status</th><th>Actions</th></tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)):
    $js_name = htmlspecialchars(json_encode($row['name']), ENT_QUOTES);
?>
<tr class="<?php echo $row['status'] === 'inactive' ? 'inactive-row' : ''; ?>">
<td>#<?php echo $row['id']; ?></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td><?php echo htmlspecialchars($row['email']); ?></td>
<td><?php echo htmlspecialchars($row['course']); ?></td>
<td><?php echo htmlspecialchars($row['gender']); ?></td>
<td><span class="badge <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
<td>
<a class="act edit" href="edit.php?id=<?php echo $row['id']; ?>">Edit</a>
<?php if ($row['status'] === 'active'): ?>
<button type="button" class="act deact" onclick="openModal('deactivate', <?php echo $row['id']; ?>, <?php echo $js_name; ?>)">Deactivate</button>
<?php else: ?>
<form method="POST" action="index.php" style="display:inline;">
<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
<button type="submit" name="activate" class="act activate">Activate</button>
</form>
<?php endif; ?>
<button type="button" class="act del" onclick="openModal('delete', <?php echo $row['id']; ?>, <?php echo $js_name; ?>)">Delete</button>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7" style="text-align:center;color:#888;">No records found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

<!-- Deactivate popup -->
<div class="modal-bg" id="deactivateModal">
<div class="modal">
<h3>Deactivate Student</h3>
<p id="deactivateMsg"></p>
<form method="POST" action="index.php">
<input type="hidden" name="id" id="deactivateId">
<div class="actions">
<button type="button" class="btn btn-grey" onclick="closeModals()">Cancel</button>
<button type="submit" name="deactivate" class="btn btn-orange">Yes, Deactivate</button>
</div>
</form>
</div>
</div>

<!-- Delete popup -->
<div class="modal-bg" id="deleteModal">
<div class="modal">
<h3>Delete Student</h3>
<p id="deleteMsg"></p>
<form method="POST" action="index.php">
<input type="hidden" name="id" id="deleteId">
<div class="actions">
<button type="button" class="btn btn-grey" onclick="closeModals()">Cancel</button>
<button type="submit" name="delete" class="btn btn-red">Yes, Delete</button>
</div>
</form>
</div>
</div>

<script>
function openModal(type, id, name) {
  document.getElementById(type + 'Id').value = id;
  document.getElementById(type + 'Msg').textContent = type === 'delete'
    ? "Permanently delete '" + name + "'? This cannot be undone."
    : "Deactivate '" + name + "'? You can activate them again later.";
  document.getElementById(type + 'Modal').classList.add('show');
}
function closeModals() {
  document.querySelectorAll('.modal-bg').forEach(m => m.classList.remove('show'));
}
document.querySelectorAll('.modal-bg').forEach(m =>
  m.addEventListener('click', e => { if (e.target === m) closeModals(); }));
</script>
</body>
</html>