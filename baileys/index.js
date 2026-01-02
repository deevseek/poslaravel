import makeWASocket, {
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import pino from 'pino';
import qrcode from 'qrcode-terminal';

const AUTH_DIR = 'baileys_auth';

const getTextMessage = (message) =>
  message?.conversation ?? message?.extendedTextMessage?.text ?? '';

const startBaileys = async () => {
  const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
  const { version } = await fetchLatestBaileysVersion();

  const socket = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: process.env.WA_LOG_LEVEL ?? 'info' }),
    printQRInTerminal: false,
  });

  socket.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      qrcode.generate(qr, { small: true });
      console.log('Scan QR di atas dengan WhatsApp untuk login.');
    }

    if (connection === 'close') {
      const reason = new Boom(lastDisconnect?.error)?.output?.statusCode;
      if (reason !== DisconnectReason.loggedOut) {
        console.log('Koneksi terputus, mencoba reconnect...');
        await startBaileys();
      } else {
        console.log('Logout terdeteksi. Hapus folder auth dan scan ulang QR.');
      }
    }

    if (connection === 'open') {
      console.log('Baileys terhubung.');

      const target = process.env.WA_TARGET;
      const message = process.env.WA_MESSAGE;
      if (target && message) {
        await socket.sendMessage(target, { text: message });
        console.log(`Pesan terkirim ke ${target}.`);
      }
    }
  });

  socket.ev.on('creds.update', saveCreds);

  socket.ev.on('messages.upsert', async ({ messages, type }) => {
    if (type !== 'notify') {
      return;
    }

    const message = messages[0];
    if (!message?.message || message.key.fromMe) {
      return;
    }

    const text = getTextMessage(message.message);
    if (!text) {
      return;
    }

    if (text.toLowerCase() === 'ping') {
      await socket.sendMessage(message.key.remoteJid, { text: 'pong' });
    }
  });
};

startBaileys().catch((error) => {
  console.error('Gagal menjalankan Baileys:', error);
  process.exit(1);
});
