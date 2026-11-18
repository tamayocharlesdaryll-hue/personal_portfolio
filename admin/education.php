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
                $statement = $pdo->prepare('DELETE FROM education WHERE id = ?');
                $statement->execute([$id]);
                $message = $statement->rowCount() > 0 ? 'Education entry deleted.' : 'Education entry was not found.';
                $messageType = $statement->rowCount() > 0 ? 'success' : 'error';
            } else {
                $message = 'Choose a valid education entry to delete.';
                $messageType = 'error';
            }
        } elseif ($action === 'save') {
            $institution = trim($_POST['institution'] ?? '');
            $degree = trim($_POST['degree'] ?? '');
            $startYear = trim($_POST['start_year'] ?? '');
            $endYear = trim($_POST['end_year'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isEditing = !empty($_POST['id']);

            if (($isEditing && !$id) || $institution === '' || $degree === '' || $startYear === '' || mb_strlen($institution) > 200 || mb_strlen($degree) > 200 || mb_strlen($startYear) > 20 || mb_strlen($endYear) > 20) {
                $message = 'Enter a valid institution, degree, and start year (maximum 20 characters).';
                $messageType = 'error';
                $editing = [
                    'id' => $id,
                    'institution' => $institution,
                    'degree' => $degree,
                    'start_year' => $startYear,
                    'end_year' => $endYear,
                    'description' => $description
                ];
            } elseif ($isEditing) {
                $statement = $pdo->prepare('UPDATE education SET institution = ?, degree = ?, start_year = ?, end_year = ?, description = ? WHERE id = ?');
                $statement->execute([$institution, $degree, $startYear, $endYear, $description, $id]);
                $exists = $pdo->prepare('SELECT id FROM education WHERE id = ?');
                $exists->execute([$id]);
                if ($exists->fetch()) {
                    $message = 'Education entry updated.';
                } else {
                    $message = 'Education entry was not found.';
                    $messageType = 'error';
                }
            } else {
                $statement = $pdo->prepare('INSERT INTO education (institution, degree, start_year, end_year, description) VALUES (?, ?, ?, ?, ?)');
                $statement->execute([$institution, $degree, $startYear, $endYear, $description]);
                $message = 'Education entry added.';
            }
        }
    }
}

if ($editing === null && isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $statement = $pdo->prepare('SELECT * FROM education WHERE id = ?');
        $statement->execute([$editId]);
        $editing = $statement->fetch() ?: null;
        if (!$editing) {
            $message = 'Education entry was not found.';
            $messageType = 'error';
        }
    }
}

$education = $pdo->query('SELECT * FROM education ORDER BY start_year DESC, id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Education</title>
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
        <a class="active" href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2><?php echo $editing ? 'Edit Education' : 'Add Education'; ?></h2>
        <?php if ($message !== ''): ?>
            <p class="<?php echo $messageType === 'error' ? 'error-message' : 'success-message'; ?>" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo escape($editing['id'] ?? ''); ?>">
            <label for="institution">Institution</label>
            <input id="institution" name="institution" maxlength="200" value="<?php echo escape($editing['institution'] ?? ''); ?>" required>
            <label for="degree">Degree or program</label>
            <input id="degree" name="degree" maxlength="200" value="<?php echo escape($editing['degree'] ?? ''); ?>" required>
            <label for="start_year">Start year</label>
            <input id="start_year" name="start_year" maxlength="20" value="<?php echo escape($editing['start_year'] ?? ''); ?>" required>
            <label for="end_year">End year (leave blank if current)</label>
            <input id="end_year" name="end_year" maxlength="20" value="<?php echo escape($editing['end_year'] ?? ''); ?>">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4"><?php echo escape($editing['description'] ?? ''); ?></textarea>
            <button class="btn" type="submit"><?php echo $editing ? 'Update Education' : 'Add Education'; ?></button>
            <?php if ($editing): ?><a href="education.php">Cancel edit</a><?php endif; ?>
        </form>
        <h3 class="top-space">Education Entries</h3>
        <table class="admin-table">
            <thead><tr><th>Degree</th><th>Institution</th><th>Years</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($education as $entry): ?>
                    <tr>
                        <td><?php echo escape($entry['degree']); ?></td>
                        <td><?php echo escape($entry['institution']); ?></td>
                        <td><?php echo escape($entry['start_year']); ?> – <?php echo escape($entry['end_year'] ?: 'Present'); ?></td>
                        <td>
                            <a href="education.php?edit=<?php echo escape($entry['id']); ?>">Edit</a>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo escape($entry['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this education entry?')">Delete</button>
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
