<?php

require_once __DIR__ . '/includes/db.php';

$education = $pdo->query('SELECT * FROM education ORDER BY start_year DESC, id DESC')->fetchAll();
$experiences = $pdo->query('SELECT * FROM experiences ORDER BY start_year DESC, id DESC')->fetchAll();
$achievements = $pdo->query('SELECT * FROM achievements ORDER BY id DESC')->fetchAll();

$profile = $pdo->query('SELECT full_name FROM personal_info WHERE id = 1')->fetch();
$fullName = $profile['full_name'] ?? 'Charles Daryll P. Tamayo';

function escapeResume($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function resumeDateRange($start, $end) {
    $start = trim((string) $start);
    $end = trim((string) $end);

    if ($end === '') {
        $end = 'Present';
    }

    return trim($start . ($start !== '' ? ' – ' : '') . $end);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume | <?php echo escapeResume($fullName); ?></title>
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
            <a href="projects.php">Projects</a>
            <a class="active" href="resume.php">Resume</a>
            <a href="contact.php">Contact</a>
            <button class="theme-toggle" aria-label="Toggle dark mode">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">MY BACKGROUND</p>
            <h1>Resume &amp; Experience</h1>
            <p>Education, activities, and experience.</p>
        </div>
    </section>

    <section class="section container resume-layout">
        <div>
            <h2 class="section-title">Education</h2>
            <div class="timeline">
                <?php if ($education): ?>
                    <?php foreach ($education as $item): ?>
                        <article class="timeline-item">
                            <span><?php echo escapeResume(resumeDateRange($item['start_year'], $item['end_year'])); ?></span>
                            <h3><?php echo escapeResume($item['degree']); ?></h3>
                            <p><?php echo escapeResume($item['institution']); ?></p>
                            <?php if (!empty($item['description'])): ?>
                                <p><?php echo nl2br(escapeResume($item['description'])); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Education details will be added soon.</p>
                <?php endif; ?>
            </div>

            <h2 class="section-title top-space">Activities &amp; Experience</h2>
            <div class="timeline">
                <?php if ($experiences): ?>
                    <?php foreach ($experiences as $item): ?>
                        <article class="timeline-item">
                            <span><?php echo escapeResume(resumeDateRange($item['start_year'], $item['end_year'])); ?></span>
                            <h3><?php echo escapeResume($item['title']); ?></h3>
                            <p><?php echo escapeResume($item['organization']); ?></p>
                            <?php if (!empty($item['description'])): ?>
                                <p><?php echo nl2br(escapeResume($item['description'])); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Experience details will be added soon.</p>
                <?php endif; ?>
            </div>
        </div>

        <aside class="resume-side">
            <h2>Training &amp; Achievements</h2>
            <?php if ($achievements): ?>
                <ul class="check-list">
                    <?php foreach ($achievements as $item): ?>
                        <li>
                            <strong><?php echo escapeResume($item['title']); ?></strong>
                            <?php if (!empty($item['issuer'])): ?>
                                <br><?php echo escapeResume($item['issuer']); ?>
                            <?php endif; ?>
                            <?php if (!empty($item['achievement_date'])): ?>
                                <br><?php echo escapeResume($item['achievement_date']); ?>
                            <?php endif; ?>
                            <?php if (!empty($item['description'])): ?>
                                <br><?php echo escapeResume($item['description']); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Training and achievements will be added soon.</p>
            <?php endif; ?>
            <a class="btn" href="contact.php">Let's Connect</a>
        </aside>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> <?php echo escapeResume($fullName); ?>. All rights reserved.</p>
    </div>
</footer>

<button class="scroll-top" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>

</body>
</html>
