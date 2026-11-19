<?php

require_once __DIR__ . '/includes/db.php';

$projects = $pdo->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();

function escapeProject($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function projectFilter($category) {
    $category = strtolower($category);

    if (strpos($category, 'web') !== false) {
        return 'web';
    }

    if (strpos($category, 'anim') !== false) {
        return 'animate';
    }

    if (strpos($category, 'network') !== false) {
        return 'network';
    }

    return 'all';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects | Charles Daryll P. Tamayo</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar container">
        <a class="logo" href="index.php">CD<span>.</span></a>
        <button class="menu-toggle" aria-label="Open navigation">☰</button>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="skills.php">Skills</a>
            <a class="active" href="projects.php">Projects</a>
            <a href="resume.php">Resume</a>
            <a href="contact.php">Contact</a>
            <button class="theme-toggle" aria-label="Toggle dark mode">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">MY WORK</p>
            <h1>Projects</h1>
            <p>A selection of school and personal projects.</p>
        </div>
    </section>

    <section class="section container">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="web">Web Development</button>
            <button class="filter-btn" data-filter="animate">Animation</button>
            <button class="filter-btn" data-filter="network">Computer Networking</button>
        </div>

        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <article class="project-card" data-category="<?php echo escapeProject(projectFilter($project['category'])); ?>">
                    <?php if (!empty($project['image'])): ?>
                        <img src="<?php echo escapeProject($project['image']); ?>" alt="<?php echo escapeProject($project['title']); ?>">
                    <?php endif; ?>
                    <div class="project-body">
                        <span class="project-tag"><?php echo escapeProject($project['category']); ?></span>
                        <h3><?php echo escapeProject($project['title']); ?></h3>
                        <p><?php echo escapeProject($project['description']); ?></p>
                        <div class="tech-list">
                            <?php foreach (explode(',', $project['technology']) as $technology): ?>
                                <span><?php echo escapeProject(trim($technology)); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!empty($project['project_url'])): ?>
                            <a class="text-btn" href="<?php echo escapeProject($project['project_url']); ?>" target="_blank" rel="noopener noreferrer">View Project</a>
                        <?php endif; ?>
                    </div>
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

<button class="scroll-top" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>

</body>
</html>
