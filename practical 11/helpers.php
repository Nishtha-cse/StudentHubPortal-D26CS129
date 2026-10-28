<?php
// Shared by index.php and export.php so the CSV always matches the filtered table.

function build_where(array $src): array {
    $cond = [];
    $types = '';
    $params = [];

    $search = trim($src['search'] ?? '');
    $course = $src['course_filter'] ?? '';
    $status = $src['status_filter'] ?? '';

    if ($search !== '') {
        $cond[] = "(name LIKE ? OR email LIKE ?)";
        $like = "%$search%";
        $types .= 'ss';
        $params[] = $like;
        $params[] = $like;
    }
    if ($course !== '') {
        $cond[] = "course = ?";
        $types .= 's';
        $params[] = $course;
    }
    if (in_array($status, ['active', 'inactive'], true)) {
        $cond[] = "status = ?";
        $types .= 's';
        $params[] = $status;
    }

    $where = $cond ? ' WHERE ' . implode(' AND ', $cond) : '';
    return [$where, $types, $params];
}

function run_query($conn, string $sql, string $types = '', array $params = []) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    return $stmt;
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}