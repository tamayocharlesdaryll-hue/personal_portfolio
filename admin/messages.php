<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $message = 'Choose a valid message.';
        } elseif ($action === 'toggle_read') {
            $statement = $pdo->prepare('UPDATE messages SET is_read = CASE WHEN is_read = 1 THEN 0 ELSE 1 END WHERE id = ?');
            $statement->execute([$id]);
            $message = $statement->rowCount() > 0 ? 'Message status updated.' : 'Message was not found.';
        } elseif ($action === 'delete') {
            $statement = $pdo->prepare('DELETE FROM messages WHERE id = ?');
            $statement->execute([$id]);
            $message = $statement->rowCount() > 0 ? 'Message deleted.' : 'Message was not found.';
        }
    }
}

$messages = $pdo->query('SELECT id, name, email, subject, message, is_read, created_at FROM messages ORDER BY created_at DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
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
        <a href="achievements.php">Achievements</a>
        <a class="active" href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2>Contact Messages</h2>
        <?php if ($message !== ''): ?>
            <p class="success-message" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>

        <?php if (!$messages): ?>
            <p>No messages have been received yet.</p>
        <?php else: ?>
            <div class="admin-message-list">
                <?php foreach ($messages as $item): ?>
                    <article class="resume-side">
                        <p><strong><?php echo escape($item['subject']); ?></strong>
                            <?php echo (int) $item['is_read'] === 1 ? '(Read)' : '(Unread)'; ?></p>
                        <p>From <?php echo escape($item['name']); ?> &lt;<a href="mailto:<?php echo escape($item['email']); ?>"><?php echo escape($item['email']); ?></a>&gt;</p>
                        <p><?php echo escape($item['created_at']); ?></p>
                        <p><?php echo nl2br(escape($item['message'])); ?></p>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                            <input type="hidden" name="action" value="toggle_read">
                            <input type="hidden" name="id" value="<?php echo escape($item['id']); ?>">
                            <button type="submit"><?php echo (int) $item['is_read'] === 1 ? 'Mark unread' : 'Mark read'; ?></button>
                        </form>
                        <form method="post" style="display:inline">
                            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo escape($item['id']); ?>">
                            <button type="submit" onclick="return confirm('Delete this message?')">Delete</button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
