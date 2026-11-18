<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$messageType = 'success';

$profile = $pdo->query('SELECT * FROM personal_info WHERE id = 1')->fetch();
if (!$profile) {
    $profile = [
        'full_name' => '',
        'title' => '',
        'introduction' => '',
        'biography' => '',
        'email' => '',
        'facebook' => '',
        'instagram' => '',
        'profile_image' => 'images/profile.png'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile = array_merge($profile, [
        'full_name' => trim($_POST['full_name'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'introduction' => trim($_POST['introduction'] ?? ''),
        'biography' => trim($_POST['biography'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'facebook' => trim($_POST['facebook'] ?? ''),
        'instagram' => trim($_POST['instagram'] ?? '')
    ]);

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
        $messageType = 'error';
    } else {
        $valid = $profile['full_name'] !== ''
            && mb_strlen($profile['full_name']) <= 150
            && $profile['title'] !== ''
            && mb_strlen($profile['title']) <= 150
            && $profile['introduction'] !== ''
            && $profile['biography'] !== ''
            && filter_var($profile['email'], FILTER_VALIDATE_EMAIL)
            && mb_strlen($profile['email']) <= 150;

        foreach (['facebook', 'instagram'] as $socialField) {
            if ($profile[$socialField] !== '' && (!filter_var($profile[$socialField], FILTER_VALIDATE_URL) || mb_strlen($profile[$socialField]) > 255)) {
                $valid = false;
            }
        }

        if (!$valid) {
            $message = 'Complete the required fields. Name and title must be at most 150 characters, email must be valid, and social links must be valid URLs up to 255 characters.';
            $messageType = 'error';
        } else {
            $statement = $pdo->prepare(
                'INSERT INTO personal_info (id, full_name, title, introduction, biography, email, facebook, instagram, profile_image) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), title = VALUES(title), introduction = VALUES(introduction), biography = VALUES(biography), email = VALUES(email), facebook = VALUES(facebook), instagram = VALUES(instagram)'
            );
            $statement->execute([
                $profile['full_name'],
                $profile['title'],
                $profile['introduction'],
                $profile['biography'],
                $profile['email'],
                $profile['facebook'],
                $profile['instagram'],
                $profile['profile_image'] ?: 'images/profile.png'
            ]);
            $message = 'Profile information saved.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
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
        <a class="active" href="profile.php">Profile</a>
        <a href="skills.php">Skills</a>
        <a href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2>Personal Information</h2>
        <?php if ($message !== ''): ?>
            <p class="<?php echo $messageType === 'error' ? 'error-message' : 'success-message'; ?>" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">

            <label for="full_name">Full name</label>
            <input id="full_name" name="full_name" maxlength="150" value="<?php echo escape($profile['full_name']); ?>" required>

            <label for="title">Title</label>
            <input id="title" name="title" maxlength="150" value="<?php echo escape($profile['title']); ?>" required>

            <label for="introduction">Introduction</label>
            <textarea id="introduction" name="introduction" rows="3" required><?php echo escape($profile['introduction']); ?></textarea>

            <label for="biography">Biography</label>
            <textarea id="biography" name="biography" rows="6" required><?php echo escape($profile['biography']); ?></textarea>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" maxlength="150" value="<?php echo escape($profile['email']); ?>" required>

            <label for="facebook">Facebook URL</label>
            <input id="facebook" type="url" name="facebook" maxlength="255" value="<?php echo escape($profile['facebook']); ?>">

            <label for="instagram">Instagram URL</label>
            <input id="instagram" type="url" name="instagram" maxlength="255" value="<?php echo escape($profile['instagram']); ?>">

            <button class="btn" type="submit">Save Profile</button>
        </form>
    </main>
</div>
</body>
</html>
