<?php

require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

$errors = [];
$sent = false;
$values = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $values[$field] = trim($_POST[$field] ?? '');
    }

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Your form session expired. Please reload the page and try again.';
    } else {
        if (mb_strlen($values['name']) < 2 || mb_strlen($values['name']) > 150) {
            $errors[] = 'Enter a name between 2 and 150 characters.';
        }

        if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 150) {
            $errors[] = 'Enter a valid email address (maximum 150 characters).';
        }

        if (mb_strlen($values['subject']) < 3 || mb_strlen($values['subject']) > 200) {
            $errors[] = 'Enter a subject between 3 and 200 characters.';
        }

        if (mb_strlen($values['message']) < 10 || mb_strlen($values['message']) > 10000) {
            $errors[] = 'Your message must be between 10 and 10,000 characters.';
        }

        if (!$errors) {
            $statement = $pdo->prepare(
                'INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([
                $values['name'],
                $values['email'],
                $values['subject'],
                $values['message']
            ]);
            $sent = true;
            $values = ['name' => '', 'email' => '', 'subject' => '', 'message' => ''];
        }
    }
}

$profile = $pdo->query('SELECT full_name, email, facebook, instagram FROM personal_info WHERE id = 1')->fetch();
if (!$profile) {
    $profile = [
        'full_name' => 'Charles Daryll P. Tamayo',
        'email' => 'tamayocharlesdaryll@gmail.com',
        'facebook' => 'https://www.facebook.com/charles.tamayo.522',
        'instagram' => 'https://www.instagram.com/charles.jpeg'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | <?php echo escape($profile['full_name']); ?></title>
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
            <a href="resume.php">Resume</a>
            <a class="active" href="contact.php">Contact</a>
            <button class="theme-toggle" aria-label="Toggle dark mode">☾</button>
        </div>
    </nav>
</header>

<main>
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">GET IN TOUCH</p>
            <h1>Contact Me</h1>
            <p>Have a question? Send a message.</p>
        </div>
    </section>

    <section class="section container contact-layout">
        <div class="contact-info">
            <p class="eyebrow">CONTACT DETAILS</p>
            <h2 class="section-title">Let's talk.</h2>

            <?php if (!empty($profile['email'])): ?>
                <div class="contact-item">
                    <strong>✉ Email</strong>
                    <a href="mailto:<?php echo escape($profile['email']); ?>"><?php echo escape($profile['email']); ?></a>
                </div>
            <?php endif; ?>

            <?php foreach (['facebook' => '📱 Facebook', 'instagram' => '📷 Instagram'] as $field => $label): ?>
                <?php if (!empty($profile[$field]) && filter_var($profile[$field], FILTER_VALIDATE_URL)): ?>
                    <div class="contact-item">
                        <strong><?php echo escape($label); ?></strong>
                        <a href="<?php echo escape($profile[$field]); ?>" target="_blank" rel="noopener noreferrer"><?php echo escape($profile[$field]); ?></a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <form class="contact-form" id="contact-form-server" method="post" action="contact.php">
            <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">

            <?php if ($sent): ?>
                <p class="form-success" role="status">Thanks! Your message has been sent.</p>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="error-message" role="alert">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo escape($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <label for="name">Name</label>
            <input id="name" name="name" type="text" maxlength="150" autocomplete="name" value="<?php echo escape($values['name']); ?>" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" maxlength="150" autocomplete="email" value="<?php echo escape($values['email']); ?>" required>

            <label for="subject">Subject</label>
            <input id="subject" name="subject" type="text" maxlength="200" value="<?php echo escape($values['subject']); ?>" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="6" maxlength="10000" required><?php echo escape($values['message']); ?></textarea>

            <input class="btn" type="submit" value="Send Message">
        </form>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> <?php echo escape($profile['full_name']); ?>. All rights reserved.</p>
    </div>
</footer>

<button class="scroll-top" aria-label="Scroll to top">↑</button>
<script src="js/script.js"></script>

</body>
</html>
