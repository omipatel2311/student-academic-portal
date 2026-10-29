<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/functions.php";
admin_only();
$q = trim((string)($_GET["search"] ?? ""));
$cat = trim((string)($_GET["category"] ?? ""));
$status = trim((string)($_GET["status"] ?? ""));
$sql = "SELECT * FROM events WHERE 1=1";
$types = "";
$args = [];
if ($q !== "") {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR venue LIKE ?)";
    $like = "%" . $q . "%";
    $types .= "sss";
    array_push($args, $like, $like, $like);
}
if ($cat !== "") {
    $sql .= " AND category = ?";
    $types .= "s";
    $args[] = $cat;
}
if (in_array($status, ["Open", "Closed"], true)) {
    $sql .= " AND status = ?";
    $types .= "s";
    $args[] = $status;
}
$sql .= " ORDER BY event_date DESC, event_id DESC";
$s = $conn->prepare($sql);
if ($types !== "") {
    $s->bind_param($types, ...$args);
}
$s->execute();
$events = $s->get_result()->fetch_all(MYSQLI_ASSOC);
$s->close();
$cats = [];
$r = $conn->query("SELECT DISTINCT category FROM events ORDER BY category");
while ($row = $r->fetch_assoc()) {
    $cats[] = $row["category"];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Event Directory</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include __DIR__ . "/top.php"; ?>
<main class="container">
    <section class="card">
        <?php flashes(); ?>
        <div class="heading">
            <div>
                <h2>Event Directory</h2>
                <p class="muted">Manage events with validated poster uploads.</p>
            </div>
            <a class="btn" href="add_event.php">＋ Add Event</a>
        </div>
        <form class="filters" method="get">
            <input
                name="search"
                placeholder="Search title, description, venue..."
                value="<?= e($q) ?>"
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= e($c) ?>"
                        <?= $cat === $c ? "selected" : "" ?>>
                        <?= e($c) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <option value="">All Statuses</option>
                <option value="Open" <?= $status === "Open" ? "selected" : "" ?>>
                    Open
                </option>
                <option value="Closed" <?= $status === "Closed" ? "selected" : "" ?>>
                    Closed
                </option>
            </select>
            <button type="submit" class="btn">Filter</button>
            <a class="btn secondary" href="index.php">Reset</a>
        </form>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Event</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$events): ?>
                    <tr>
                        <td colspan="8" class="empty">No events found.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($events as $ev): ?>
                    <tr>
                        <td>#<?= (int)$ev["event_id"] ?></td>
                        <td>
                            <?php if (!empty($ev["poster"])): ?>
                                <img
                                    class="thumb"
                                    src="uploads/<?= rawurlencode($ev["poster"]) ?>"
                                    alt="Event Poster"
                            <?php else: ?>
                                <span class="muted small">No poster</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <b><?= e($ev["title"]) ?></b>
                            <div class="muted small">
                                <?= e(mb_strimwidth((string)($ev["description"] ?? ""), 0, 65, "…")) ?>
                            </div>
                        </td>
                        <td><?= e($ev["event_date"]) ?></td>
                        <td><?= e($ev["venue"]) ?></td>
                        <td><?= e($ev["category"]) ?></td>
                        <td>
                            <span class="pill <?= strtolower(e($ev["status"])) ?>">
                                <?= e($ev["status"]) ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a
                                    class="btn smallbtn"
                                    href="edit_event.php?id=<?= (int)$ev["event_id"] ?>"
                                >Edit</a>
                                <form
                                    method="post"
                                    action="delete_event.php"
                                    onsubmit="return confirm('Delete this event?')"
                                >
                                    <input
                                        type="hidden"
                                        name="csrf"
                                        value="<?= e(csrf()) ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$ev["event_id"] ?>"
                                    >
                                    <button type="submit" class="btn danger smallbtn">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="muted small">
            Showing <?= count($events) ?> event(s).
            Prepared statements are used for database queries.
        </p>
    </section>
</main>
<?php include __DIR__ . "/bottom.php"; ?>
</body>
</html>
