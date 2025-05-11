<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

require 'ssh_functions.php';

$message = '';
$output = '';
try {
    $ssh = sshConnect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            if (!$username || !$password) {
                $message = "Username dan password harus diisi!";
            } else {
                $message = createUser($ssh, $username, $password);
            }
        } elseif ($action === 'delete') {
            $username = trim($_POST['username'] ?? '');
            if (!$username) {
                $message = "Username harus diisi!";
            } else {
                $message = deleteUser($ssh, $username);
            }
        } elseif ($action === 'exec') {
            $command = trim($_POST['command'] ?? '');
            if (!$command) {
                $message = "Perintah harus diisi!";
            } else {
                $output = execCommand($ssh, $command);
            }
        }
    }

    $users = listUsers($ssh);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard SSH Manager</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 20px auto; }
        h1, h2 { margin-top: 30px; }
        form { border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 30px; }
        label { display: block; margin-top: 10px; }
        input[type=text], input[type=password], textarea, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 15px; }
        .message { background: #eef; border: 1px solid #99c; padding: 10px; margin-bottom: 20px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; white-space: pre-wrap; }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: #007BFF; }
        nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a>
    </nav>

    <h1>Dashboard SSH Manager</h1>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2>Buat User SSH Baru</h2>
    <form method="POST">
        <input type="hidden" name="action" value="create" />
        <label>Username baru:</label>
        <input type="text" name="username" required />
        <label>Password:</label>
        <input type="password" name="password" required />
        <button type="submit">Buat User</button>
    </form>

    <h2>Hapus User SSH</h2>
    <form method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
        <input type="hidden" name="action" value="delete" />
        <label>Pilih user yang akan dihapus:</label>
        <select name="username" required>
            <option value="">-- Pilih User --</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user) ?>"><?= htmlspecialchars($user) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Hapus User</button>
    </form>

    <h2>Daftar User SSH di VPS</h2>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><?= htmlspecialchars($user) ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Jalankan Perintah Shell</h2>
    <form method="POST">
        <input type="hidden" name="action" value="exec" />
        <label>Perintah:</label>
        <textarea name="command" rows="3" required></textarea>
        <button type="submit">Jalankan</button>
    </form>

    <?php if ($output): ?>
        <h3>Output Perintah</h3>
        <pre><?= htmlspecialchars($output) ?></pre>
    <?php endif; ?>
</body>
</html>
