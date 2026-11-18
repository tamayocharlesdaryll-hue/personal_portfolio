<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$editingSkill = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            if ($id) {
                $statement = $pdo->prepare('DELETE FROM skills WHERE id = ?');
                $statement->execute([$id]);
                $message = $statement->rowCount() > 0 ? 'Skill deleted.' : 'Skill was not found.';
            }
        } elseif ($action === 'add' || $action === 'update') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $name = trim($_POST['name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $percentage = filter_input(INPUT_POST, 'percentage', FILTER_VALIDATE_INT);
            $description = trim($_POST['description'] ?? '');

            if (($action === 'update' && !$id) || $name === '' || $category === '' || $percentage === false || $percentage === null || $percentage < 0 || $percentage > 100) {
                $message = 'Please enter a valid skill name, category, and percentage.';
                $editingSkill = [
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'percentage' => $_POST['percentage'] ?? '',
                    'description' => $description
                ];
            } elseif ($action === 'add') {
                $statement = $pdo->prepare('INSERT INTO skills (name, category, percentage, description) VALUES (?, ?, ?, ?)');
                $statement->execute([$name, $category, $percentage, $description]);
                $message = 'Skill added.';
            } else {
                $statement = $pdo->prepare('UPDATE skills SET name = ?, category = ?, percentage = ?, description = ? WHERE id = ?');
                $statement->execute([$name, $category, $percentage, $description, $id]);
                $message = $statement->rowCount() > 0 ? 'Skill updated.' : 'Skill was not found or no values changed.';
            }
        }
    }
}

if ($editingSkill === null && isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

    if ($editId) {
        $statement = $pdo->prepare('SELECT * FROM skills WHERE id = ?');
        $statement->execute([$editId]);
        $editingSkill = $statement->fetch() ?: null;
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
        <h2><?php echo $editingSkill ? 'Edit Skill' : 'Add Skill'; ?></h2>

        <?php if ($message !== ''): ?>
            <p class="success-message"><?php echo escape($message); ?></p>
        <?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="<?php echo $editingSkill ? 'update' : 'add'; ?>">
            <input type="hidden" name="id" value="<?php echo escape($editingSkill['id'] ?? ''); ?>">

            <label for="name">Skill name</label>
            <input id="name" name="name" maxlength="100" value="<?php echo escape($editingSkill['name'] ?? ''); ?>" required>

            <label for="category">Category</label>
            <input id="category" name="category" maxlength="100" value="<?php echo escape($editingSkill['category'] ?? ''); ?>" required>

            <label for="percentage">Percentage</label>
            <input id="percentage" type="number" name="percentage" min="0" max="100" value="<?php echo escape($editingSkill['percentage'] ?? ''); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo escape($editingSkill['description'] ?? ''); ?></textarea>

            <button class="btn" type="submit"><?php echo $editingSkill ? 'Update Skill' : 'Add Skill'; ?></button>
            <?php if ($editingSkill): ?>
                <a href="skills.php">Cancel edit</a>
            <?php endif; ?>
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
                            <a href="skills.php?edit=<?php echo escape($skill['id']); ?>">Edit</a>
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
