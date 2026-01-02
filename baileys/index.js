import http from 'node:http';
import makeWASocket, {
  DisconnectReason,
  fetchLatestBaileysVersion,
  useMultiFileAuthState,
} from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import pino from 'pino';
import qrcode from 'qrcode-terminal';

const AUTH_DIR = 'baileys_auth';
const PORT = Number(process.env.WA_PORT ?? 3001);

let socketRef = null;
let isConnected = false;

const getTextMessage = (message) =>
  message?.conversation ?? message?.extendedTextMessage?.text ?? '';

const normalizeJid = (phone) => {
  const normalized = String(phone ?? '')
    .trim()
    .replace(/[^0-9@.]/g, '');

  if (!normalized) {
    return null;
  }

  if (normalized.endsWith('@s.whatsapp.net')) {
    return normalized;
  }

  const digitsOnly = normalized.replace(/[^0-9]/g, '');
  return digitsOnly ? `${digitsOnly}@s.whatsapp.net` : null;
};

const sendMessage = async (phone, message) => {
  if (!socketRef || !isConnected) {
    throw new Error('Baileys belum terhubung.');
  }

  const jid = normalizeJid(phone);
  if (!jid) {
    throw new Error('Nomor tujuan tidak valid.');
  }

  await socketRef.sendMessage(jid, { text: message });
};

const startBaileys = async () => {
  const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
  const { version } = await fetchLatestBaileysVersion();

  const socket = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: process.env.WA_LOG_LEVEL ?? 'info' }),
    printQRInTerminal: false,
  });

  socketRef = socket;

  socket.ev.on('connection.update', async (update) => {
    const { connection, lastDisconnect, qr } = update;

    if (qr) {
      qrcode.generate(qr, { small: true });
      console.log('Scan QR di atas dengan WhatsApp untuk login.');
    }

    if (connection === 'close') {
      isConnected = false;
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
      isConnected = true;

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

const server = http.createServer(async (req, res) => {
  res.setHeader('Content-Type', 'application/json');

  if (req.method === 'GET' && req.url === '/status') {
    res.writeHead(200);
    res.end(JSON.stringify({ status: isConnected ? 'connected' : 'disconnected' }));
    return;
  }

  if (req.method === 'POST' && req.url === '/send') {
    let body = '';
    req.on('data', (chunk) => {
      body += chunk;
    });

    req.on('end', async () => {
      try {
        const payload = body ? JSON.parse(body) : {};
        const { phone, message } = payload;

        if (!phone || !message) {
          res.writeHead(400);
          res.end(JSON.stringify({ error: 'phone dan message wajib diisi.' }));
          return;
        }

        await sendMessage(phone, message);
        res.writeHead(200);
        res.end(JSON.stringify({ status: 'sent' }));
      } catch (error) {
        const status = error instanceof SyntaxError ? 400 : 500;
        res.writeHead(status);
        res.end(JSON.stringify({ error: error.message }));
      }
    });
    return;
  }

  res.writeHead(404);
  res.end(JSON.stringify({ error: 'Not Found' }));
});

server.listen(PORT, () => {
  console.log(`Baileys gateway berjalan di http://localhost:${PORT}`);
});

startBaileys().catch((error) => {
  console.error('Gagal menjalankan Baileys:', error);
  process.exit(1);
});
