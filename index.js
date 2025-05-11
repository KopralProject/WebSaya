const TelegramBot = require('node-telegram-bot-api');
const { exec } = require('child_process');

// Ganti dengan token bot Telegram Anda
const token = '8058490630:AAFiyQKYBSFBsfb7N-pLJ-JiTUnuvA2BBag';
const bot = new TelegramBot(token, { polling: true });

// Ganti dengan Telegram ID admin yang boleh buat akun SSH
const ADMIN_ID = 6222865137;

bot.onText(/\/start/, (msg) => {
  bot.sendMessage(msg.chat.id, 'Selamat datang! Gunakan /create username password untuk buat akun SSH.');
});

bot.onText(/\/create (.+) (.+)/, (msg, match) => {
  const chatId = msg.chat.id;
  const userId = msg.from.id;

  if (userId !== ADMIN_ID) {
    bot.sendMessage(chatId, 'Maaf, Anda tidak punya izin menggunakan perintah ini.');
    return;
  }

  const username = match[1];
  const password = match[2];

  // Validasi sederhana username dan password
  if (!/^[a-z0-9]+$/.test(username)) {
    bot.sendMessage(chatId, 'Username hanya boleh huruf kecil dan angka.');
    return;
  }

  if (password.length < 6) {
    bot.sendMessage(chatId, 'Password minimal 6 karakter.');
    return;
  }

  // Perintah untuk membuat user dan set password
  const cmd = `
    sudo useradd -m ${username} -s /bin/bash && \
    echo "${username}:${password}" | sudo chpasswd && \
    sudo passwd -x 7 ${username}
  `;

  exec(cmd, (error, stdout, stderr) => {
    if (error) {
      bot.sendMessage(chatId, `Gagal membuat akun SSH: ${stderr || error.message}`);
      return;
    }
    bot.sendMessage(chatId, `Akun SSH berhasil dibuat.\nUsername: ${username}\nPassword: ${password}\nMasa aktif: 7 hari`);
  });
});
