<?php

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid form request. Please try again.';
    } elseif (!login_attempt_allowed()) {
        $error = 'Too many failed attempts. Please try again after 5 minutes.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $statement = $pdo->prepare('SELECT * FROM administrators WHERE email = ? LIMIT 1');
        $statement->execute([$email]);
        $admin = $statement->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['login_attempts'] = 0;
            unset($_SESSION['login_locked_until']);
            redirect('dashboard.php');
        }

        add_login_attempt();
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<main class="login-page">
    <form class="login-form" method="post">
        <h1>Administrator Login</h1>

        <?php if ($error !== ''): ?>
            <p class="error-message"><?php echo escape($error); ?></p>
        <?php endif; ?>

        <input type="hidden" name="csrf_token" value="<?php echo escape(csrf_token()); ?>">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="<?php echo escape($_POST['email'] ?? ''); ?>" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required>

        <button class="btn" type="submit">Login</button>
    </form>
</main>

</body>
</html>
