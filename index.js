const TelegramBot = require('node-telegram-bot-api');
const { Client } = require('ssh2');

const token = '8058490630:AAFiyQKYBSFBsfb7N-pLJ-JiTUnuvA2BBag';
const bot = new TelegramBot(token, { polling: true });

const sshConfig = {
  host: '13.212.234.66',
  port: 22,
  username: 'root',
  password: 'kopral13245'
};

bot.onText(/\/start/, (msg) => {
  bot.sendMessage(msg.chat.id, 'Halo! Kirim perintah untuk dijalankan di VPS.');
});

bot.on('message', (msg) => {
  const chatId = msg.chat.id;
  const command = msg.text;

  if (command.startsWith('/')) return; // Abaikan perintah bot

  const conn = new Client();
  conn.on('ready', () => {
    conn.exec(command, (err, stream) => {
      if (err) {
        bot.sendMessage(chatId, `Error: ${err.message}`);
        conn.end();
        return;
      }
      let output = '';
      stream.on('close', () => {
        bot.sendMessage(chatId, `Output:\n${output || '(tidak ada output)'}`);
        conn.end();
      }).on('data', (data) => {
        output += data.toString();
      }).stderr.on('data', (data) => {
        output += data.toString();
      });
    });
  }).on('error', (err) => {
    bot.sendMessage(chatId, `Koneksi SSH gagal: ${err.message}`);
  }).connect(sshConfig);
});
