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

        if ($action === 'add') {
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $percentage = filter_input(INPUT_POST, 'percentage', FILTER_VALIDATE_INT);
            $description = trim($_POST['description'] ?? '');

            if ($name === '' || $category === '' || $percentage === false || $percentage < 0 || $percentage > 100) {
                $message = 'Please enter a valid skill name, category, and percentage.';
            } else {
                $statement = $pdo->prepare('INSERT INTO skills (name, category, percentage, description) VALUES (?, ?, ?, ?)');
                $statement->execute([$name, $category, $percentage, $description]);
                $message = 'Skill added.';
            }
        }

        if ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if ($id) {
                $statement = $pdo->prepare('DELETE FROM skills WHERE id = ?');
                $statement->execute([$id]);
                $message = 'Skill deleted.';
            }
        }

        if ($action === 'update') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $percentage = filter_input(INPUT_POST, 'percentage', FILTER_VALIDATE_INT);
            $description = trim($_POST['description'] ?? '');

            if (!$id || $name === '' || $category === '' || $percentage === false || $percentage < 0 || $percentage > 100) {
                $message = 'Please enter valid skill details.';
            } else {
                $statement = $pdo->prepare('UPDATE skills SET name = ?, category = ?, percentage = ?, description = ? WHERE id = ?');
                $statement->execute([$name, $category, $percentage, $description, $id]);
                $message = 'Skill updated.';
            }
        }
    }
}

$skills = $pdo->query('SELECT * FROM skills ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Skills</title>
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
        <a class="active" href="skills.php">Skills</a>
        <a href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>

    <main class="admin-main">
        <h2>Skills</h2>

        <?php if ($message !== ''): ?>
            <p class="success-message"><?php echo escape($message); ?></p>
        <?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="add">

            <label for="name">Skill name</label>
            <input id="name" name="name" maxlength="100" required>

            <label for="category">Category</label>
            <input id="category" name="category" maxlength="100" required>

            <label for="percentage">Percentage</label>
            <input id="percentage" type="number" name="percentage" min="0" max="100" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>

            <button class="btn" type="submit">Add Skill</button>
        </form>

        <h3 class="top-space">Existing Skills</h3>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Percentage</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($skills as $skill): ?>
                    <tr>
                        <td><?php echo escape($skill['name']); ?></td>
                        <td><?php echo escape($skill['category']); ?></td>
                        <td><?php echo escape($skill['percentage']); ?>%</td>
                        <td>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo escape($skill['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this skill?')">Delete</button>
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
