<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$messageType = 'success';
$editingProject = null;

function save_project_image($file) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name']) || $file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
        return false;
    }

    $mimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);
    if (!isset($mimeTypes[$mimeType]) || @getimagesize($file['tmp_name']) === false) {
        return false;
    }

    $uploadFolder = __DIR__ . '/../uploads';
    if (!is_dir($uploadFolder) && !mkdir($uploadFolder, 0755, true) && !is_dir($uploadFolder)) {
        return false;
    }

    try {
        $fileName = bin2hex(random_bytes(16)) . '.' . $mimeTypes[$mimeType];
    } catch (Throwable $exception) {
        return false;
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadFolder . '/' . $fileName)) {
        return false;
    }

    return 'uploads/' . $fileName;
}

function remove_project_image($imagePath) {
    if (strpos($imagePath, 'uploads/') !== 0 || basename($imagePath) !== substr($imagePath, 8)) {
        return;
    }

    $fullPath = __DIR__ . '/../' . $imagePath;
    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

if (isset($_GET['edit'])) {
    $editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($editId) {
        $statement = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
        $statement->execute([$editId]);
        $editingProject = $statement->fetch() ?: null;
        if (!$editingProject) {
            $message = 'Project was not found.';
            $messageType = 'error';
        }
    } else {
        $message = 'Choose a valid project to edit.';
        $messageType = 'error';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';
        $postedId = trim($_POST['id'] ?? '');
        $projectId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($action === 'delete') {
            if (!$projectId) {
                $message = 'Choose a valid project to delete.';
                $messageType = 'error';
            } else {
                $statement = $pdo->prepare('SELECT image FROM projects WHERE id = ?');
                $statement->execute([$projectId]);
                $project = $statement->fetch();
                if ($project) {
                    $delete = $pdo->prepare('DELETE FROM projects WHERE id = ?');
                    $delete->execute([$projectId]);
                    remove_project_image($project['image'] ?? '');
                    $message = 'Project deleted.';
                } else {
                    $message = 'Project was not found.';
                    $messageType = 'error';
                }
            }
        } elseif ($action === 'save') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $technology = trim($_POST['technology'] ?? '');
            $projectUrl = trim($_POST['project_url'] ?? '');
            $isEditing = $postedId !== '';

            $valid = (!$isEditing || $projectId)
                && $title !== '' && mb_strlen($title) <= 150
                && $category !== '' && mb_strlen($category) <= 100
                && $description !== ''
                && $technology !== '' && mb_strlen($technology) <= 255
                && mb_strlen($projectUrl) <= 255
                && ($projectUrl === '' || filter_var($projectUrl, FILTER_VALIDATE_URL));

            if (!$valid) {
                $message = 'Complete the required fields. Title (150), category (100), technologies (255), and project URL (255) must fit their limits; provide a valid URL when supplied.';
                $messageType = 'error';
                $editingProject = [
                    'id' => $projectId,
                    'title' => $title,
                    'category' => $category,
                    'description' => $description,
                    'technology' => $technology,
                    'project_url' => $projectUrl,
                    'image' => ''
                ];
                if ($projectId) {
                    $current = $pdo->prepare('SELECT image FROM projects WHERE id = ?');
                    $current->execute([$projectId]);
                    $editingProject['image'] = $current->fetchColumn() ?: '';
                }
            } else {
                $newImage = save_project_image($_FILES['image'] ?? null);
                if ($newImage === false) {
                    $message = 'Upload a valid image under 2 MB. JPG, PNG, GIF, and WebP are accepted.';
                    $messageType = 'error';
                    $editingProject = [
                        'id' => $projectId,
                        'title' => $title,
                        'category' => $category,
                        'description' => $description,
                        'technology' => $technology,
                        'project_url' => $projectUrl,
                        'image' => ''
                    ];
                    if ($projectId) {
                        $current = $pdo->prepare('SELECT image FROM projects WHERE id = ?');
                        $current->execute([$projectId]);
                        $editingProject['image'] = $current->fetchColumn() ?: '';
                    }
                } elseif ($isEditing) {
                    $check = $pdo->prepare('SELECT image FROM projects WHERE id = ?');
                    $check->execute([$projectId]);
                    $existingProject = $check->fetch();

                    if (!$existingProject) {
                        if ($newImage !== null) {
                            remove_project_image($newImage);
                        }
                        $message = 'Project was not found.';
                        $messageType = 'error';
                    } else {
                        $imagePath = $newImage ?? $existingProject['image'];
                        $update = $pdo->prepare('UPDATE projects SET title = ?, category = ?, description = ?, technology = ?, image = ?, project_url = ? WHERE id = ?');
                        $update->execute([$title, $category, $description, $technology, $imagePath, $projectUrl ?: null, $projectId]);
                        if ($newImage !== null) {
                            remove_project_image($existingProject['image'] ?? '');
                        }
                        $message = 'Project updated.';
                        $editingProject = null;
                    }
                } else {
                    $insert = $pdo->prepare('INSERT INTO projects (title, category, description, technology, image, project_url) VALUES (?, ?, ?, ?, ?, ?)');
                    $insert->execute([$title, $category, $description, $technology, $newImage, $projectUrl ?: null]);
                    $message = 'Project added.';
                    $editingProject = null;
                }
            }
        } else {
            $message = 'Unknown project action.';
            $messageType = 'error';
        }
    }
}

$projects = $pdo->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
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
        <a class="active" href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2><?php echo $editingProject ? 'Edit Project' : 'Add Project'; ?></h2>
        <?php if ($message !== ''): ?>
            <p class="<?php echo $messageType === 'error' ? 'error-message' : 'success-message'; ?>" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>

        <form class="admin-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?php echo escape($editingProject['id'] ?? ''); ?>">

            <label for="title">Project title</label>
            <input id="title" name="title" maxlength="150" value="<?php echo escape($editingProject['title'] ?? ''); ?>" required>

            <label for="category">Category</label>
            <input id="category" name="category" maxlength="100" value="<?php echo escape($editingProject['category'] ?? ''); ?>" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required><?php echo escape($editingProject['description'] ?? ''); ?></textarea>

            <label for="technology">Technologies, separated by commas</label>
            <input id="technology" name="technology" maxlength="255" value="<?php echo escape($editingProject['technology'] ?? ''); ?>" required>

            <label for="project_url">Project URL</label>
            <input id="project_url" type="url" name="project_url" maxlength="255" value="<?php echo escape($editingProject['project_url'] ?? ''); ?>">

            <label for="image">Project image (maximum 2 MB)</label>
            <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
            <?php if (!empty($editingProject['image'])): ?>
                <p>Current image: <?php echo escape($editingProject['image']); ?></p>
            <?php endif; ?>

            <button class="btn" type="submit">Save Project</button>
            <?php if ($editingProject): ?><a href="projects.php">Cancel edit</a><?php endif; ?>
        </form>

        <h3 class="top-space">Existing Projects</h3>
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Category</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo escape($project['title']); ?></td>
                        <td><?php echo escape($project['category']); ?></td>
                        <td>
                            <a href="projects.php?edit=<?php echo escape($project['id']); ?>">Edit</a>
                            <form method="post" style="display:inline">
                                <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo escape($project['id']); ?>">
                                <button type="submit" onclick="return confirm('Delete this project?')">Delete</button>
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
