<?php
require_once "db.php";
requireAdmin();

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: index.php?message=" . urlencode("Invalid event ID."));
    exit;
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: index.php?message=" . urlencode("Event not found."));
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $event_date = $_POST["event_date"] ?? "";
    $status = $_POST["status"] ?? "";
    $newPoster = $event["poster"];

    if ($title === "" || $description === "" || $event_date === "") {
        $error = "Please fill in all required fields.";
    } elseif (mb_strlen($title) > 150) {
        $error = "Title cannot exceed 150 characters.";
    } elseif (!in_array($status, ["Open", "Closed"], true)) {
        $error = "Invalid status.";
    } elseif (
        !DateTime::createFromFormat("!Y-m-d", $event_date) ||
        DateTime::createFromFormat("!Y-m-d", $event_date)->format("Y-m-d") !== $event_date
    ) {
        $error = "Invalid event date.";
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
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed[$mime]) ||
                    getimagesize($upload["tmp_name"]) === false) {
                    $error = "Only valid JPG, PNG, or WEBP images are allowed.";
                } else {
                    $folder = __DIR__ . "/uploads/";

                    if (!is_dir($folder) &&
                        !mkdir($folder, 0755, true)) {
                        $error = "Cannot create upload folder.";
                    } else {
                        $newPoster = bin2hex(random_bytes(16))
                                   . "." . $allowed[$mime];

                        if (!move_uploaded_file(
                            $upload["tmp_name"],
                            $folder . $newPoster
                        )) {
                            $error = "Could not save the new poster.";
                            $newPoster = $event["poster"];
                        }
                    }
                }
            }
        }
    }

    if ($error === "") {
        $stmt = $conn->prepare(
            "UPDATE events
             SET title = ?, description = ?, event_date = ?,
                 status = ?, poster = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            "sssssi", $title, $description, $event_date,
            $status, $newPoster, $id
        );

        if ($stmt->execute()) {
            if ($newPoster !== $event["poster"] && $event["poster"]) {
                $oldFile = __DIR__ . "/uploads/" . basename($event["poster"]);
                if (is_file($oldFile)) {
                    unlink($oldFile);
                }
            }

            header("Location: index.php?message=" .
                   urlencode("Event updated successfully."));
            exit;
        }

        if ($newPoster !== $event["poster"] && $newPoster) {
            $newFile = __DIR__ . "/uploads/" . basename($newPoster);
            if (is_file($newFile)) {
                unlink($newFile);
            }
        }

        $error = "Could not update the event.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<div class="form-container">
    <h1>Edit Event</h1>

    <?php if ($error): ?>
        <p class="message error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" maxlength="150" required
               value="<?= e($_POST["title"] ?? $event["title"]) ?>">

        <label>Description</label>
        <textarea name="description" rows="4" required><?= e($_POST["description"] ?? $event["description"]) ?></textarea>

        <label>Event Date</label>
        <input type="date" name="event_date" required
               value="<?= e($_POST["event_date"] ?? $event["event_date"]) ?>">

        <label>Status</label>
        <select name="status">
            <option value="Open"
                <?= ($_POST["status"] ?? $event["status"]) === "Open" ? "selected" : "" ?>>
                Open
            </option>
            <option value="Closed"
                <?= ($_POST["status"] ?? $event["status"]) === "Closed" ? "selected" : "" ?>>
                Closed
            </option>
        </select>

        <label>Current Poster</label>
        <?php if ($event["poster"]): ?>
            <img class="current-poster"
                 src="uploads/<?= rawurlencode($event["poster"]) ?>"
                 alt="Current poster">
        <?php else: ?>
            <p>No poster uploaded.</p>
        <?php endif; ?>

        <label>Replace Poster (optional)</label>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp">
        <p class="hint">JPG, PNG or WEBP; maximum 2 MB.</p>

        <button type="submit">Update Event</button>
        <a class="btn secondary" href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
