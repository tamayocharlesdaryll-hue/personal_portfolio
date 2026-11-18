<?php

require_once __DIR__ . '/includes/db.php';

$profile = $pdo->query('SELECT * FROM personal_info WHERE id = 1')->fetch();
$settings = $pdo->query('SELECT * FROM website_settings WHERE id = 1')->fetch();
$projects = $pdo->query('SELECT * FROM projects ORDER BY id ASC')->fetchAll();

if (!$profile) {
    $profile = [
        'full_name' => 'Charles Daryll P. Tamayo',
        'title' => 'Student',
        'introduction' => 'I am still improving my skills and discovering what I enjoy the most.',
        'biography' => 'This portfolio highlights my education, creative work, and experience.',
        'email' => 'tamayocharlesdaryll@gmail.com',
        'facebook' => 'https://www.facebook.com/charles.tamayo.522',
        'instagram' => 'https://www.instagram.com/charles.jpeg',
        'profile_image' => 'images/profile.png'
    ];
}

if (!$settings) {
    $settings = [
        'website_title' => 'Charles Daryll P. Tamayo | Portfolio',
        'logo_text' => 'CD',
        'primary_color' => '#5b5ce2'
    ];
}

function publicEscape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$primaryColor = preg_match('/^#[0-9a-fA-F]{6}$/', $settings['primary_color'])
    ? $settings['primary_color']
    : '#5b5ce2';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Personal portfolio website">
    <title><?php echo publicEscape($settings['website_title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>:root { --primary: <?php echo publicEscape($primaryColor); ?>; --primary-dark: color-mix(in srgb, <?php echo publicEscape($primaryColor); ?> 78%, #000); }</style>
</head>
<body>

<header class="site-header">
    <nav class="navbar container">
        <a class="logo" href="index.php">
            <?php echo publicEscape($settings['logo_text']); ?><span>.</span>
        </a>

        <button class="menu-toggle" aria-label="Open navigation">☰</button>

        <div class="nav-links">
            <a class="active" href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="skills.php">Skills</a>
            <a href="projects.php">Projects</a>
            <a href="resume.php">Resume</a>
            <a href="contact.php">Contact</a>
            <button class="theme-toggle" aria-label="Toggle dark mode">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="hero container">
        <div class="hero-content">
            <p class="eyebrow">WELCOME TO MY PORTFOLIO</p>
            <h1>Hi, I'm <span><?php echo publicEscape($profile['full_name']); ?></span>.</h1>
            <h2>I am a <span><?php echo publicEscape($profile['title']); ?></span></h2>
            <p class="hero-text"><?php echo publicEscape($profile['introduction']); ?></p>
            <div class="hero-buttons">
                <a class="btn" href="projects.php">View My Projects</a>
                <a class="btn btn-outline" href="contact.php">Contact Me</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="<?php echo publicEscape($profile['profile_image'] ?: 'images/profile.png'); ?>" alt="Profile photo">
        </div>
    </section>

    <section class="section soft-bg">
        <div class="container center">
            <p class="eyebrow">WHAT I DO</p>
            <h2 class="section-title">Learning new things</h2>
            <p class="section-intro"><?php echo publicEscape($profile['biography']); ?></p>
            <div class="projects-grid">
                <?php foreach (array_slice($projects, 0, 3) as $project): ?>
                    <article class="project-card">
                        <?php if (!empty($project['image'])): ?>
                            <img src="<?php echo publicEscape($project['image']); ?>" alt="<?php echo publicEscape($project['title']); ?>">
                        <?php endif; ?>
                        <div class="project-body">
                            <span class="project-tag"><?php echo publicEscape($project['category']); ?></span>
                            <h3><?php echo publicEscape($project['title']); ?></h3>
                            <p><?php echo publicEscape($project['description']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> <?php echo publicEscape($profile['full_name']); ?>. All rights reserved.</p>
    </div>
</footer>

<button class="scroll-top" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>

</body>
</html>
