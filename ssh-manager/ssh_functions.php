<?php
require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SSH2;

function sshConnect() {
    $host = 'IP_VPS_ANDA';              // Ganti IP VPS Anda
    $rootUser = 'root';
    $rootPassword = 'PASSWORD_ROOT_VPS'; // Ganti password root VPS Anda

    $ssh = new SSH2($host);
    if (!$ssh->login($rootUser, $rootPassword)) {
        throw new Exception('Gagal login root ke VPS!');
    }
    return $ssh;
}

function userExists($ssh, $username) {
    $output = $ssh->exec("id -u $username 2>&1");
    return strpos($output, 'no such user') === false;
}

function createUser($ssh, $username, $password) {
    if (userExists($ssh, $username)) {
        return "User $username sudah ada!";
    }
    $ssh->exec("useradd -m -s /bin/bash $username");
    $ssh->exec("echo '$username:$password' | chpasswd");
    return "User $username berhasil dibuat.";
}

function deleteUser($ssh, $username) {
    if (!userExists($ssh, $username)) {
        return "User $username tidak ditemukan!";
    }
    $ssh->exec("userdel -r $username");
    return "User $username berhasil dihapus.";
}

function listUsers($ssh) {
    $usersRaw = $ssh->exec("cut -d: -f1,6 /etc/passwd | grep '/home' | cut -d: -f1");
    $users = array_filter(explode("\n", trim($usersRaw)));
    return $users;
}

function execCommand($ssh, $command) {
    return $ssh->exec($command);
}
