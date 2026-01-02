# Baileys WhatsApp (Node.js)

Folder ini berisi contoh sederhana penggunaan Baileys untuk koneksi WhatsApp.

## Menjalankan

1. Install dependency:
   ```bash
   npm install
   ```
2. Jalankan dari root project:
   ```bash
   node baileys/index.js
   ```
   Atau masuk ke folder `baileys` lalu jalankan:
   ```bash
   cd baileys
   node index.js
   ```
3. Scan QR yang muncul di terminal.
4. Aplikasi Laravel mengirim pesan ke endpoint gateway `POST /send`.

## Env opsional

- `WA_TARGET`: JID tujuan untuk kirim pesan otomatis, contoh `6281234567890@s.whatsapp.net`
- `WA_MESSAGE`: Isi pesan otomatis yang dikirim setelah koneksi terbuka.
- `WA_LOG_LEVEL`: Level log (`info`, `debug`, dll).
- `WA_PORT`: Port HTTP gateway (default `3001`).

Contoh:
```bash
WA_TARGET=6281234567890@s.whatsapp.net WA_MESSAGE="Halo" node baileys/index.js
```

## API gateway

### POST /send

Payload JSON:
```json
{
  "phone": "6281234567890",
  "message": "Halo dari POS"
}
```

Response sukses:
```json
{ "status": "sent" }
```

### GET /status

Response:
```json
{ "status": "connected" }
```
