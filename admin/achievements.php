<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$messageType = 'success';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($action === 'delete') {
            if ($id) {
                $statement = $pdo->prepare('DELETE FROM achievements WHERE id = ?');
                $statement->execute([$id]);
                $message = $statement->rowCount() > 0 ? 'Achievement deleted.' : 'Achievement was not found.';
                $messageType = $statement->rowCount() > 0 ? 'success' : 'error';
            } else {
                $message = 'Choose a valid achievement to delete.';
                $messageType = 'error';
            }
        } elseif ($action === 'save') {
            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $achievementDate = trim($_POST['achievement_date'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isEditing = !empty($_POST['id']);

            if (($isEditing && !$id) || $title === '' || mb_strlen($title) > 200 || mb_strlen($issuer) > 200 || mb_strlen($achievementDate) > 50) {
                $message = 'Enter an achievement title (maximum 200 characters). Issuer is limited to 200 characters and date to 50 characters.';
                $messageType = 'error';
                $editing = [
                    'id' => $id,
                    'title' => $title,
                    'issuer' => $issuer,
                    'achievement_date' => $achievementDate,
                    'description' => $description
                ];
            } elseif ($isEditing) {
                $statement = $pdo->prepare('UPDATE achievements SET title = ?, issuer = ?, achievement_date = ?, description = ? WHERE id = ?');
                $statement->execute([$title, $issuer, $achievementDate, $description, $id]);
                $exists = $pdo->prepare('SELECT id FROM achievements WHERE id = ?');
                $exists->execute([$id]);
                if ($exists->fetch()) {
                    $message = 'Achievement updated.';
                } else {
                    $message = 'Achievement was not found.';
                    $messageType = 'error';
                }
            } else {
                $statement = $pdo->prepare('INSERT INTO achievements (title, issuer, achievement_date, description) VALUES (?, ?, ?, ?)');
                $statement->execute([$title, $issuer, $achievementDate, $description]);
                $message = 'Achievement added.';
            }
        }
    }
}

if ($editing === null && isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $statement = $pdo->prepare('SELECT * FROM achievements WHERE id = ?');
        $statement->execute([$editId]);
        $editing = $statement->fetch() ?: null;
        if (!$editing) {
            $message = 'Achievement was not found.';
            $messageType = 'error';
        }
    }
}

$achievements = $pdo->query('SELECT * FROM achievements ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="admin-header">
    <h1>Portfolio Admin</h1>
    <a class="btn" href="logout.php">Logout</a>
</header>
<div class="admin-layout">
    <nav class="admin-nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="skills.php">Skills</a>
        <a href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a class="active" href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2><?php echo $editing ? 'Edit Achievement' : 'Add Achievement'; ?></h2>
        <?php if ($message !== ''): ?>
            <p class="<?php echo $messageType === 'error' ? 'error-message' : 'success-message'; ?>" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo escape($editing['id'] ?? ''); ?>">
            <label for="title">Achievement or certification</label>
            <input id="title" name="title" maxlength="200" value="<?php echo escape($editing['title'] ?? ''); ?>" required>
            <label for="issuer">Issuer</label>
            <input id="issuer" name="issuer" maxlength="200" value="<?php echo escape($editing['issuer'] ?? ''); ?>">
            <label for="achievement_date">Date</label>
            <input id="achievement_date" name="achievement_date" maxlength="50" value="<?php echo escape($editing['achievement_date'] ?? ''); ?>">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo escape($editing['description'] ?? ''); ?></textarea>
            <button class="btn" type="submit"><?php echo $editing ? 'Update Achievement' : 'Add Achievement'; ?></button>
            <?php if ($editing): ?><a href="achievements.php">Cancel edit</a><?php endif; ?>
        </form>
        <h3 class="top-space">Achievements</h3>
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Issuer</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($achievements as $entry): ?>
                    <tr>
                        <td><?php echo escape($entry['title']); ?></td>
                        <td><?php echo escape($entry['issuer']); ?></td>
                        <td><?php echo escape($entry['achievement_date']); ?></td>
                        <td>
                            <a href="achievements.php?edit=<?php echo escape($entry['id']); ?>">Edit</a>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo escape($entry['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this achievement?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
