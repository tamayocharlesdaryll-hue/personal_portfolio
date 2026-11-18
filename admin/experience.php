<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($action === 'delete') {
            if ($id) {
                $statement = $pdo->prepare('DELETE FROM experiences WHERE id = ?');
                $statement->execute([$id]);
                $message = $statement->rowCount() > 0 ? 'Experience entry deleted.' : 'Experience entry was not found.';
            } else {
                $message = 'Choose a valid experience entry to delete.';
            }
        } elseif ($action === 'save') {
            $title = trim($_POST['title'] ?? '');
            $organization = trim($_POST['organization'] ?? '');
            $startYear = trim($_POST['start_year'] ?? '');
            $endYear = trim($_POST['end_year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (($id === false && !empty($_POST['id'])) || $title === '' || $organization === '' || $startYear === '' || mb_strlen($title) > 200 || mb_strlen($organization) > 200 || mb_strlen($startYear) > 20 || mb_strlen($endYear) > 20) {
                $message = 'Enter a valid title, organization, and start year (maximum 20 characters).';
                $editing = [
                    'id' => $id,
                    'title' => $title,
                    'organization' => $organization,
                    'start_year' => $startYear,
                    'end_year' => $endYear,
                    'description' => $description
                ];
            } elseif (!empty($id)) {
                $statement = $pdo->prepare('UPDATE experiences SET title = ?, organization = ?, start_year = ?, end_year = ?, description = ? WHERE id = ?');
                $statement->execute([$title, $organization, $startYear, $endYear, $description, $id]);
                $message = $statement->rowCount() > 0 ? 'Experience entry updated.' : 'Experience entry was not found or no values changed.';
            } else {
                $statement = $pdo->prepare('INSERT INTO experiences (title, organization, start_year, end_year, description) VALUES (?, ?, ?, ?, ?)');
                $statement->execute([$title, $organization, $startYear, $endYear, $description]);
                $message = 'Experience entry added.';
            }
        }
    }
}

if ($editing === null && isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $statement = $pdo->prepare('SELECT * FROM experiences WHERE id = ?');
        $statement->execute([$editId]);
        $editing = $statement->fetch() ?: null;
    }
}

$experiences = $pdo->query('SELECT * FROM experiences ORDER BY start_year DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Experience</title>
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
        <a class="active" href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2><?php echo $editing ? 'Edit Experience' : 'Add Experience'; ?></h2>
        <?php if ($message !== ''): ?><p class="success-message"><?php echo escape($message); ?></p><?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo escape($editing['id'] ?? ''); ?>">
            <label for="title">Role or activity</label>
            <input id="title" name="title" maxlength="200" value="<?php echo escape($editing['title'] ?? ''); ?>" required>
            <label for="organization">Organization</label>
            <input id="organization" name="organization" maxlength="200" value="<?php echo escape($editing['organization'] ?? ''); ?>" required>
            <label for="start_year">Start year</label>
            <input id="start_year" name="start_year" maxlength="20" value="<?php echo escape($editing['start_year'] ?? ''); ?>" required>
            <label for="end_year">End year (leave blank if current)</label>
            <input id="end_year" name="end_year" maxlength="20" value="<?php echo escape($editing['end_year'] ?? ''); ?>">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo escape($editing['description'] ?? ''); ?></textarea>
            <button class="btn" type="submit"><?php echo $editing ? 'Update Experience' : 'Add Experience'; ?></button>
            <?php if ($editing): ?><a href="experience.php">Cancel edit</a><?php endif; ?>
        </form>
        <h3 class="top-space">Experience Entries</h3>
        <table class="admin-table">
            <thead><tr><th>Role</th><th>Organization</th><th>Years</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($experiences as $entry): ?>
                    <tr>
                        <td><?php echo escape($entry['title']); ?></td>
                        <td><?php echo escape($entry['organization']); ?></td>
                        <td><?php echo escape($entry['start_year']); ?> – <?php echo escape($entry['end_year'] ?: 'Present'); ?></td>
                        <td>
                            <a href="experience.php?edit=<?php echo escape($entry['id']); ?>">Edit</a>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo escape($entry['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this experience entry?')">Delete</button>
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
