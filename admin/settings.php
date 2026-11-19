<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$message = '';
$messageType = 'success';
$settings = $pdo->query('SELECT * FROM website_settings WHERE id = 1')->fetch();

if (!$settings) {
    $settings = [
        'website_title' => 'Charles Daryll P. Tamayo | Portfolio',
        'logo_text' => 'CD',
        'primary_color' => '#5b5ce2'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = 'Invalid form request. Please try again.';
        $messageType = 'error';
    } else {
        $websiteTitle = trim($_POST['website_title'] ?? '');
        $logoText = trim($_POST['logo_text'] ?? '');
        $primaryColor = strtolower(trim($_POST['primary_color'] ?? ''));

        if ($websiteTitle === '' || mb_strlen($websiteTitle) > 200 || $logoText === '' || mb_strlen($logoText) > 50 || !preg_match('/^#[0-9a-f]{6}$/', $primaryColor)) {
            $message = 'Enter a website title (up to 200 characters), logo text (up to 50 characters), and a valid six-digit hex color.';
            $messageType = 'error';
            $settings = [
                'website_title' => $websiteTitle,
                'logo_text' => $logoText,
                'primary_color' => $primaryColor
            ];
        } else {
            $statement = $pdo->prepare(
                'INSERT INTO website_settings (id, website_title, logo_text, primary_color) VALUES (1, ?, ?, ?) '
                . 'ON DUPLICATE KEY UPDATE website_title = VALUES(website_title), logo_text = VALUES(logo_text), primary_color = VALUES(primary_color)'
            );
            $statement->execute([$websiteTitle, $logoText, $primaryColor]);

            $settings = [
                'website_title' => $websiteTitle,
                'logo_text' => $logoText,
                'primary_color' => $primaryColor
            ];
            $message = 'Website settings saved.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings</title>
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
        <a href="messages.php">Messages</a>
        <a class="active" href="settings.php">Settings</a>
    </nav>
    <main class="admin-main">
        <h2>Website Settings</h2>
        <?php if ($message !== ''): ?>
            <p class="<?php echo $messageType === 'error' ? 'error-message' : 'success-message'; ?>" role="status"><?php echo escape($message); ?></p>
        <?php endif; ?>
        <form class="admin-form" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">

            <label for="website_title">Website title</label>
            <input id="website_title" name="website_title" maxlength="200" value="<?php echo escape($settings['website_title']); ?>" required>

            <label for="logo_text">Logo text</label>
            <input id="logo_text" name="logo_text" maxlength="50" value="<?php echo escape($settings['logo_text']); ?>" required>

            <label for="primary_color">Primary color</label>
            <input id="primary_color" name="primary_color" type="color" value="<?php echo escape(preg_match('/^#[0-9a-f]{6}$/', $settings['primary_color']) ? $settings['primary_color'] : '#5b5ce2'); ?>" required>

            <button class="btn" type="submit">Save Settings</button>
        </form>
    </main>
</div>
</body>
</html>
