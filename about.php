<?php

require_once __DIR__ . '/includes/db.php';

$profile = $pdo->query('SELECT * FROM personal_info WHERE id = 1')->fetch();

if (!$profile) {
    $profile = [
        'full_name' => 'Charles Daryll P. Tamayo',
        'biography' => 'I am a student who is still learning and exploring different areas of technology and creativity. Through my studies, I am learning how to use technology to create projects, solve simple problems, and express my ideas.',
        'profile_image' => 'images/about.png'
    ];
}

function escapeAbout($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="About <?php echo escapeAbout($profile['full_name']); ?>">
    <title>About | <?php echo escapeAbout($profile['full_name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar container">
        <a class="logo" href="index.php">CD<span>.</span></a>
        <button class="menu-toggle" aria-label="Open navigation">☰</button>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a class="active" href="about.php">About</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="resume.php">Resume</a>
            <a href="contact.php">Contact</a>
            <button class="theme-toggle" aria-label="Toggle dark mode">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">GET TO KNOW ME</p>
            <h1>About Me</h1>
            <p>A short introduction to my background, goals, and interests.</p>
        </div>
    </section>

    <section class="section container two-column">
        <div>
            <img class="about-image" src="images/about.png" alt="About <?php echo escapeAbout($profile['full_name']); ?>">
        </div>

        <div>
            <p class="eyebrow">MY STORY</p>
            <h2 class="section-title">A student learning technology and creativity.</h2>
            <p><?php echo nl2br(escapeAbout($profile['biography'])); ?></p>

            <div class="info-list">
                <div>
                    <strong>Education</strong>
                    <span>Bachelor of Science in Information Technology</span>
                </div>
                <div>
                    <strong>Location</strong>
                    <span>Poblacion Sta. Catalina Negros</span>
                </div>
                <div>
                    <strong>Goal</strong>
                    <span>To keep learning, improving, and gaining experience.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section soft-bg">
        <div class="container">
            <p class="eyebrow">MY INTERESTS</p>
            <h2 class="section-title">Things I enjoy</h2>
            <div class="interest-grid">
                <div class="interest-card">💡 Learning technology</div>
                <div class="interest-card">🎨 Drawing</div>
                <div class="interest-card">📷 Photography</div>
                <div class="interest-card">🎬 Animation</div>
                <div class="interest-card">🌐 Exploring</div>
                <div class="interest-card">🎮 Playing online games</div>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> <?php echo escapeAbout($profile['full_name']); ?>. All rights reserved.</p>
    </div>
</footer>

<button class="scroll-top" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>

</body>
</html>
