<?php
require_once "db.php";
requireAdmin();

$status = $_GET["status"] ?? "All";

if (!in_array($status, ["All", "Open", "Closed"], true)) {
    $status = "All";
}

if ($status === "All") {
    $stmt = $conn->prepare("SELECT * FROM events ORDER BY id DESC");
} else {
    $stmt = $conn->prepare(
        "SELECT * FROM events WHERE status = ? ORDER BY id DESC"
    );
    $stmt->bind_param("s", $status);
}

$stmt->execute();
$result = $stmt->get_result();
$message = $_GET["message"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Management</title>
    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<div class="container">
    <header class="topbar">
        <div>
            <h1>Event Management</h1>
            <p>Admin dashboard</p>
        </div>
        <a class="btn danger" href="logout.php">Logout</a>
    </header>

    <?php if ($message !== ""): ?>
        <p class="message success"><?= e($message) ?></p>
    <?php endif; ?>

    <div class="toolbar">
        <h2>All Events</h2>
        <a class="btn" href="add.php">+ Add New Event</a>
    </div>

    <form method="GET" class="filter-form">
        <label for="status">Filter by status:</label>
        <select name="status" id="status">
            <option value="All" <?= $status === "All" ? "selected" : "" ?>>All</option>
            <option value="Open" <?= $status === "Open" ? "selected" : "" ?>>Open</option>
            <option value="Closed" <?= $status === "Closed" ? "selected" : "" ?>>Closed</option>
        </select>
        <button type="submit">Apply Filter</button>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Poster</th>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($event = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($event["id"]) ?></td>
                        <td>
                            <?php if ($event["poster"]): ?>
                                <img class="poster-thumb"
                                     src="uploads/<?= rawurlencode($event["poster"]) ?>"
                                     alt="Event poster">
                            <?php else: ?>
                                No poster
                            <?php endif; ?>
                        </td>
                        <td><?= e($event["title"]) ?></td>
                        <td><?= e($event["description"]) ?></td>
                        <td><?= e($event["event_date"]) ?></td>
                        <td>
                            <span class="status <?= strtolower(e($event["status"])) ?>">
                                <?= e($event["status"]) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a class="btn small"
                               href="edit.php?id=<?= (int)$event["id"] ?>">Edit</a>

                            <form method="POST" action="delete.php"
                                  onsubmit="return confirm('Delete this event?');">
                                <input type="hidden" name="id"
                                       value="<?= (int)$event["id"] ?>">
                                <button class="btn danger small" type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No events found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>