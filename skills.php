<?php

require_once __DIR__ . '/includes/db.php';

$skills = $pdo->query('SELECT * FROM skills ORDER BY id ASC')->fetchAll();

function escapeSkill($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skills | Charles Daryll P. Tamayo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar container">
        <a class="logo" href="index.php">CD<span>.</span></a>
        <button class="menu-toggle">☰</button>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a class="active" href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="resume.php">Resume</a>
            <a href="contact.php">Contact</a>
            <button class="theme-toggle">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">MY EXPERTISE</p>
            <h1>Skills</h1>
            <p>Tools and technologies I am learning and using.</p>
        </div>
    </section>

    <section class="section container">
        <div class="skills-grid">
            <?php foreach ($skills as $skill): ?>
                <article class="skill-card">
                    <div class="skill-head">
                        <h3><?php echo escapeSkill($skill['name']); ?></h3>
                        <span><?php echo escapeSkill($skill['percentage']); ?>%</span>
                    </div>
                    <div class="progress">
                        <span style="--progress: <?php echo escapeSkill($skill['percentage']); ?>%"></span>
                    </div>
                    <p><?php echo escapeSkill($skill['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> Charles Daryll P. Tamayo. All rights reserved.</p>
    </div>
</footer>

<script src="js/script.js"></script>

</body>
</html>
