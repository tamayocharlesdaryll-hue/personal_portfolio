<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_login();

$totalProjects = $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn();
$totalSkills = $pdo->query('SELECT COUNT(*) FROM skills')->fetchColumn();
$totalMessages = $pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn();
$totalUnread = $pdo->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
$totalEducation = $pdo->query('SELECT COUNT(*) FROM education')->fetchColumn();
$totalExperiences = $pdo->query('SELECT COUNT(*) FROM experiences')->fetchColumn();
$totalAchievements = $pdo->query('SELECT COUNT(*) FROM achievements')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <a class="active" href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="skills.php">Skills</a>
        <a href="projects.php">Projects</a>
        <a href="education.php">Education</a>
        <a href="experience.php">Experience</a>
        <a href="achievements.php">Achievements</a>
        <a href="messages.php">Messages</a>
        <a href="settings.php">Settings</a>
    </nav>

    <main class="admin-main">
        <h2>Dashboard</h2>

        <div class="admin-stats">
            <article><strong><?php echo escape($totalProjects); ?></strong><span>Projects</span></article>
            <article><strong><?php echo escape($totalSkills); ?></strong><span>Skills</span></article>
            <article><strong><?php echo escape($totalMessages); ?></strong><span>Messages</span></article>
            <article><strong><?php echo escape($totalUnread); ?></strong><span>Unread</span></article>
            <article><strong><?php echo escape($totalEducation); ?></strong><span>Education</span></article>
            <article><strong><?php echo escape($totalExperiences); ?></strong><span>Experience</span></article>
            <article><strong><?php echo escape($totalAchievements); ?></strong><span>Achievements</span></article>
        </div>

        <p><a class="btn" href="../index.php" target="_blank">View Website</a></p>
    </main>
</div>

</body>
</html>
