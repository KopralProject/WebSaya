<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Ganti username & password login web sesuai keinginan Anda
    $validUser = 'admin';
    $validPass = 'admin123';

    if ($user === $validUser && $pass === $validPass) {
        $_SESSION['logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $message = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login SSH Manager</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; }
        form { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        label, input { display: block; width: 100%; margin-bottom: 10px; }
        input[type=text], input[type=password] { padding: 8px; }
        button { padding: 10px; width: 100%; }
        .message { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Login SSH Manager</h2>
    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required autofocus />
        <label>Password:</label>
        <input type="password" name="password" required />
        <button type="submit">Login</button>
    </form>
</body>
</html>
