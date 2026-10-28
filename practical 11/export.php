<?php
include 'db.php';
include 'helpers.php';

// Uses the same search/filters as index.php (passed in the URL)
list($where, $types, $params) = build_where($_GET);
$stmt   = run_query($conn, "SELECT id, name, email, course, gender, status FROM students $where ORDER BY id DESC", $types, $params);
$result = mysqli_stmt_get_result($stmt);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="students_' . date('Y-m-d') . '.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads it correctly
fputcsv($out, ['ID', 'Name', 'Email', 'Course', 'Gender', 'Status']);
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($out, $row);
}
fclose($out);
exit();