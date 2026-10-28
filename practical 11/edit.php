<?php
session_start();
include 'db.php';
include 'helpers.php';

$courses = ['Computer Science', 'Web Development', 'Data Science'];
$genders = ['Male', 'Female'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch existing record
$stmt = run_query($conn, "SELECT * FROM students WHERE id = ?", 'i', [$id]);
$student = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$student) {
    flash('error', 'Student record not found.');
    header("Location: index.php");
    exit();
}

// Handle Form Update (UPDATE)
$error = '';
if (isset($_POST['update_student'])) {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $course = $_POST['course'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $status = $_POST['status'] ?? 'active';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || !in_array($course, $courses, true) || !in_array($gender, $genders, true)
        || !in_array($status, ['active', 'inactive'], true)) {
        $error = 'Please fill all fields with valid values.';
    } else {
        try {
            run_query($conn,
                "UPDATE students SET name = ?, email = ?, course = ?, gender = ?, status = ? WHERE id = ?",
                'sssssi', [$name, $email, $course, $gender, $status, $id]);
            flash('success', "Student '$name' updated successfully.");
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $error = 'Update failed (email may already exist).';
        }
    }
    // keep typed values on error
    $student = array_merge($student, compact('name', 'email', 'course', 'gender', 'status'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Student</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.alert { padding: 10px 14px; border-radius: 4px; margin-bottom: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
</head>
<body>
<h2>Edit Student Details</h2>

<?php if ($error): ?>
<div class="alert"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" action="edit.php?id=<?php echo $id; ?>">
<div>
<label>Name:</label><br>
<input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
</div><br>
<div>
<label>Email:</label><br>
<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
</div><br>
<div>
<label>Course:</label><br>
<select name="course" required>
<?php foreach ($courses as $c): ?>
<option value="<?php echo $c; ?>" <?php if ($student['course'] === $c) echo 'selected'; ?>><?php echo $c; ?></option>
<?php endforeach; ?>
</select>
</div><br>
<div>
<label>Gender:</label><br>
<select name="gender" required>
<?php foreach ($genders as $g): ?>
<option value="<?php echo $g; ?>" <?php if ($student['gender'] === $g) echo 'selected'; ?>><?php echo $g; ?></option>
<?php endforeach; ?>
</select>
</div><br>
<div>
<label>Status:</label><br>
<select name="status" required>
<option value="active" <?php if ($student['status'] === 'active') echo 'selected'; ?>>Active</option>
<option value="inactive" <?php if ($student['status'] === 'inactive') echo 'selected'; ?>>Inactive</option>
</select>
</div><br>
<button type="submit" name="update_student">Update</button>
<a href="index.php">Cancel</a>
</form>
</body>
</html>