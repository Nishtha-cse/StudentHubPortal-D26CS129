<?php
require_once "db.php";
requireAdmin();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $event_date = $_POST["event_date"] ?? "";
    $status = $_POST["status"] ?? "Open";
    $poster = null;

    if ($title === "" || $description === "" || $event_date === "") {
        $error = "Please fill in all required fields.";
    } elseif (!in_array($status, ["Open", "Closed"], true)) {
        $error = "Invalid event status.";
    } elseif (
        !DateTime::createFromFormat("!Y-m-d", $event_date) ||
        DateTime::createFromFormat("!Y-m-d", $event_date)->format("Y-m-d") !== $event_date
    ) {
        $error = "Please enter a valid event date.";
    } else {
        $upload = $_FILES["poster"] ?? null;

        if ($upload && $upload["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($upload["error"] !== UPLOAD_ERR_OK) {
                $error = "Poster upload failed.";
            } elseif ($upload["size"] > 2 * 1024 * 1024) {
                $error = "Poster must be 2 MB or smaller.";
            } elseif (!is_uploaded_file($upload["tmp_name"])) {
                $error = "Invalid uploaded file.";
            } else {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($upload["tmp_name"]);

                $allowed = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed[$mime]) ||
                    getimagesize($upload["tmp_name"]) === false) {
                    $error = "Only valid JPG, PNG, or WEBP images are allowed.";
                } else {
                    $folder = __DIR__ . "/uploads/";

                    if (!is_dir($folder) &&
                        !mkdir($folder, 0755, true)) {
                        $error = "Unable to create the upload folder.";
                    } else {
                        $poster = bin2hex(random_bytes(16))
                                . "." . $allowed[$mime];

                        if (!move_uploaded_file(
                            $upload["tmp_name"],
                            $folder . $poster
                        )) {
                            $error = "Could not save the poster.";
                            $poster = null;
                        }
                    }
                }
            }
        }
    }

    if ($error === "") {
        $stmt = $conn->prepare(
            "INSERT INTO events (title, description, event_date, status, poster)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssss", $title, $description, $event_date, $status, $poster
        );

        if ($stmt->execute()) {
            header("Location: index.php?message=" .
                   urlencode("Event added successfully."));
            exit;
        }

        if ($poster && is_file(__DIR__ . "/uploads/" . $poster)) {
            unlink(__DIR__ . "/uploads/" . $poster);
        }
        $error = "Could not add the event.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Event</title>
    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<div class="form-container">
    <h1>Add New Event</h1>

    <?php if ($error): ?>
        <p class="message error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" maxlength="150" required
               value="<?= e($_POST["title"] ?? "") ?>">

        <label>Description</label>
        <textarea name="description" rows="4" required><?= e($_POST["description"] ?? "") ?></textarea>

        <label>Event Date</label>
        <input type="date" name="event_date" required
               value="<?= e($_POST["event_date"] ?? "") ?>">

        <label>Status</label>
        <select name="status">
            <option value="Open">Open</option>
            <option value="Closed"
                <?= ($_POST["status"] ?? "") === "Closed" ? "selected" : "" ?>>
                Closed
            </option>
        </select>

        <label>Event Poster (optional)</label>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
        <p class="hint">Allowed: JPG, PNG, WEBP. Maximum size: 2 MB.</p>

        <button type="submit">Add Event</button>
        <a class="btn secondary" href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>