# Baileys WhatsApp (Node.js)

Folder ini berisi contoh sederhana penggunaan Baileys untuk koneksi WhatsApp.

## Menjalankan

1. Install dependency:
   ```bash
   npm install
   ```
2. Jalankan:
   ```bash
   node baileys/index.js
   ```
3. Scan QR yang muncul di terminal.

## Env opsional

- `WA_TARGET`: JID tujuan untuk kirim pesan otomatis, contoh `6281234567890@s.whatsapp.net`
- `WA_MESSAGE`: Isi pesan otomatis yang dikirim setelah koneksi terbuka.
- `WA_LOG_LEVEL`: Level log (`info`, `debug`, dll).

Contoh:
```bash
WA_TARGET=6281234567890@s.whatsapp.net WA_MESSAGE="Halo" node baileys/index.js
```
