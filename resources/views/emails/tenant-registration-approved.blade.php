<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Tenant Disetujui</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);">
        <tr>
            <td style="padding: 24px 32px; background-color: #0f172a; color: #ffffff;">
                <h1 style="margin: 0; font-size: 20px;">Tenant Anda Sudah Aktif 🎉</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 32px; color: #0f172a;">
                <p style="margin: 0 0 16px;">Halo {{ $registration->admin_name ?? $registration->name }},</p>
                <p style="margin: 0 0 16px;">Terima kasih telah mendaftar. Kami sudah memverifikasi pembayaran dan tenant Anda sekarang aktif.</p>

                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; border-radius: 6px; margin: 16px 0;">
                    <tr>
                        <td style="padding: 16px;">
                            <p style="margin: 0 0 8px; font-weight: 600;">Detail Tenant</p>
                            <p style="margin: 0;">Nama usaha: {{ $registration->name }}</p>
                            <p style="margin: 0;">Subdomain: {{ $registration->subdomain }}</p>
                            <p style="margin: 0;">Paket: {{ $registration->plan?->name }}</p>
                        </td>
                    </tr>
                </table>

                <p style="margin: 0 0 16px;">Silakan login menggunakan email yang Anda daftarkan.</p>
                <p style="margin: 0;">Jika ada pertanyaan, silakan balas email ini.</p>
            </td>
        </tr>
    </table>
</body>
</html>
