# API Documentation (v1)

Dokumentasi ini ditujukan untuk konsumsi aplikasi Android/Flutter.

## Base URL

```
https://<domain>/api/v1
```

## Health Check

`GET /`

**Response**
```json
{
  "status": "ok",
  "message": "API v1 is available."
}
```

## Daftar Modul API

Dokumentasi ini mencakup seluruh modul berikut:

- Authentication (`/auth/*`)
- Attendance Face Verification (`/attendance/*`)
- Attendance Logs (`/attendance-logs`)
- Attendances (`/attendances`)
- Cash Sessions (`/cash-sessions`)
- Categories (`/categories`)
- Customers (`/customers`)
- Employees (`/employees`)
- Finances (`/finances`)
- Payrolls (`/payrolls`)
- Permissions (`/permissions`)
- Products (`/products`)
- Purchases (`/purchases`)
- Purchase Items (`/purchase-items`)
- Roles (`/roles`)
- Services (`/services`)
- Service Items (`/service-items`)
- Service Logs (`/service-logs`)
- Settings (`/settings`)
- Stock Movements (`/stock-movements`)
- Suppliers (`/suppliers`)
- Transactions (`/transactions`)
- Transaction Items (`/transaction-items`)
- Users (`/users`)
- Warranties (`/warranties`)
- Warranty Claims (`/warranty-claims`)

## Authentication

Gunakan token Bearer dari endpoint login.

### Login

`POST /auth/login`

**Request**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response**
```json
{
  "token": "<token>",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "user@example.com"
  }
}
```

### Me

`GET /auth/me`

**Headers**
```
Authorization: Bearer <token>
```

**Response**
```json
{
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "user@example.com"
  }
}
```

### Logout

`POST /auth/logout`

**Headers**
```
Authorization: Bearer <token>
```

**Response**
```json
{
  "message": "Logout berhasil."
}
```

## Common Headers

```
Authorization: Bearer <token>
Content-Type: application/json
```

Untuk upload file (absensi wajah), gunakan `multipart/form-data`.

## Format Response

### List (pagination)
Endpoint list otomatis mengembalikan data ter-paginate Laravel (`data`, `links`, `meta`).

Query parameter:
- `per_page` (opsional)
- `search` (opsional, hanya untuk resource tertentu)

Contoh:
```
GET /customers?per_page=20&search=andi
```

### Detail

```
GET /customers/{id}
```

```json
{
  "data": {
    "id": 1,
    "name": "Andi"
  }
}
```

### Create/Update

```
POST /customers
PATCH /customers/{id}
```

```json
{
  "data": {
    "id": 1,
    "name": "Andi"
  }
}
```

### Delete

```
DELETE /customers/{id}
```

```json
{
  "message": "Deleted."
}
```

## Error

- `401 Unauthorized` jika token tidak valid.
- `404 Not Found` jika resource tidak terdaftar atau data tidak ditemukan.
- `422 Unprocessable Entity` jika validasi gagal.
- `502 Bad Gateway` jika integrasi eksternal gagal (mis. face verification).

## Attendance (Face Verification)

Endpoint khusus untuk absensi menggunakan verifikasi wajah.

### Check-in

`POST /attendance/check-in`

**Headers**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

**Form-data**
- `image` (file, required, max 5MB)

**Response sukses (201)**
```json
{
  "matched": true,
  "confidence": 0.98,
  "attendance_id": 123,
  "type": "checkin",
  "captured_at": "2026-03-10T08:15:30Z"
}
```

**Response gagal (422)**
```json
{
  "matched": false,
  "confidence": 0.32,
  "message": "Face not recognized."
}
```

**Response gagal (502)**
```json
{
  "matched": false,
  "message": "Face verification failed."
}
```

### Check-out

`POST /attendance/check-out`

Header dan payload sama dengan check-in, dengan response `type` menjadi `checkout`.

## CRUD Resources

Semua endpoint berikut memerlukan header:
```
Authorization: Bearer <token>
```

Setiap resource mendukung:
- `GET /resource` (list)
- `POST /resource` (create)
- `GET /resource/{id}` (detail)
- `PUT/PATCH /resource/{id}` (update)
- `DELETE /resource/{id}` (delete)

> Catatan validasi: seluruh field bersifat opsional (`sometimes`), tetapi secara bisnis Anda tetap perlu mengirim field penting ketika membuat data.

### Attendance Logs (`/attendance-logs`)

Searchable: `user_id`, `type`, `device_info`.

| Field | Type | Notes |
| --- | --- | --- |
| user_id | integer | ID user yang melakukan absensi |
| type | string | `checkin` / `checkout` |
| confidence | float | skor verifikasi wajah |
| captured_at | datetime | waktu capture |
| ip_address | string | IP address |
| device_info | string | user agent / device |
| created_at | datetime | waktu log dibuat |

### Attendances (`/attendances`)

Searchable: `employee_id`, `attendance_date`, `status`, `method`.

| Field | Type | Notes |
| --- | --- | --- |
| employee_id | integer | relasi ke employee |
| attendance_date | date | tanggal absensi |
| check_in_time | time/string | jam masuk |
| check_out_time | time/string | jam keluar |
| method | string | metode absensi |
| status | string | status absensi |
| note | string | catatan |

### Cash Sessions (`/cash-sessions`)

Searchable: `status`, `opened_by`, `closed_by`.

| Field | Type | Notes |
| --- | --- | --- |
| opening_balance | decimal | saldo awal |
| closing_balance | decimal | saldo akhir |
| note | string | catatan |
| opened_at | datetime | waktu dibuka |
| closed_at | datetime | waktu ditutup |

### Categories (`/categories`)

Searchable: `name`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama kategori |
| description | string | deskripsi |

### Customers (`/customers`)

Searchable: `name`, `email`, `phone`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama pelanggan |
| email | string | email pelanggan |
| phone | string | nomor telepon |
| address | string | alamat |

### Employees (`/employees`)

Searchable: `name`, `email`, `phone`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama karyawan |
| position | string | jabatan |
| email | string | email |
| phone | string | nomor telepon |
| address | string | alamat |
| join_date | date | tanggal bergabung |
| base_salary | decimal | gaji pokok |
| is_active | boolean | status aktif |
| face_recognition_signature | string | signature face recognition |
| face_recognition_registered_at | datetime | waktu registrasi wajah |
| face_recognition_scan_path | string | path file scan |

### Finances (`/finances`)

Searchable: `type`, `description`.

| Field | Type | Notes |
| --- | --- | --- |
| cash_session_id | integer | relasi ke sesi kas |
| type | string | tipe transaksi |
| category | string | kategori kas |
| nominal | decimal | jumlah |
| note | string | catatan |
| recorded_at | date | tanggal pencatatan |
| reference_id | integer | referensi data |
| reference_type | string | tipe referensi |
| source | string | sumber |
| created_by | integer | pembuat |

### Payrolls (`/payrolls`)

Searchable: `employee_id`, `status`.

| Field | Type | Notes |
| --- | --- | --- |
| employee_id | integer | relasi ke employee |
| period_start | date | awal periode |
| period_end | date | akhir periode |
| pay_date | date | tanggal gaji |
| base_salary | decimal | gaji pokok |
| allowance | decimal | tunjangan |
| deduction | decimal | potongan |
| total | decimal | total gaji |
| note | string | catatan |
| created_by | integer | pembuat |

### Permissions (`/permissions`)

Searchable: `name`, `slug`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama permission |
| slug | string | identifier |
| description | string | deskripsi |

### Products (`/products`)

Searchable: `name`, `sku`.

| Field | Type | Notes |
| --- | --- | --- |
| category_id | integer | relasi ke kategori |
| name | string | nama produk |
| sku | string | SKU |
| cost_price | decimal | harga pokok |
| avg_cost | decimal | rata-rata biaya |
| price | decimal | harga jual |
| pricing_mode | string | `manual` / `percentage` |
| margin_percentage | decimal | margin (%) |
| stock | integer | stok |
| warranty_days | integer | masa garansi (hari) |
| description | string | deskripsi |

### Purchases (`/purchases`)

Searchable: `invoice_number`, `supplier_id`.

| Field | Type | Notes |
| --- | --- | --- |
| supplier_id | integer | relasi ke supplier |
| invoice_number | string | nomor invoice |
| purchase_date | date | tanggal pembelian |
| payment_status | string | status pembayaran |
| total_amount | decimal | total |
| notes | string | catatan |

### Purchase Items (`/purchase-items`)

Searchable: `purchase_id`, `product_id`.

| Field | Type | Notes |
| --- | --- | --- |
| purchase_id | integer | relasi ke purchase |
| product_id | integer | relasi ke produk |
| quantity | integer | jumlah |
| price | decimal | harga satuan |
| subtotal | decimal | subtotal |

### Roles (`/roles`)

Searchable: `name`, `slug`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama role |
| slug | string | identifier |
| description | string | deskripsi |

### Services (`/services`)

Searchable: `customer_name`, `status`, `service_type`.

| Field | Type | Notes |
| --- | --- | --- |
| customer_id | integer | relasi ke customer |
| transaction_id | integer | relasi ke transaction |
| device | string | nama perangkat |
| serial_number | string | serial number |
| accessories | string | kelengkapan |
| complaint | string | keluhan |
| diagnosis | string | diagnosa |
| notes | string | catatan |
| service_fee | decimal | biaya servis |
| warranty_days | integer | garansi servis |
| status | string | status servis |

### Service Items (`/service-items`)

Searchable: `service_id`, `product_id`.

| Field | Type | Notes |
| --- | --- | --- |
| service_id | integer | relasi ke service |
| product_id | integer | relasi ke produk |
| quantity | integer | jumlah |
| price | decimal | harga satuan |
| total | decimal | total |

### Service Logs (`/service-logs`)

Searchable: `service_id`, `status`.

| Field | Type | Notes |
| --- | --- | --- |
| service_id | integer | relasi ke service |
| user_id | integer | user pencatat |
| message | string | pesan log |

### Settings (`/settings`)

Searchable: `key`, `group`.

| Field | Type | Notes |
| --- | --- | --- |
| key | string | kunci setting |
| value | string | nilai |

### Stock Movements (`/stock-movements`)

Searchable: `reference`, `type`.

| Field | Type | Notes |
| --- | --- | --- |
| product_id | integer | relasi ke produk |
| type | string | tipe pergerakan |
| source | string | sumber pergerakan |
| reference | string | referensi |
| quantity | integer | jumlah |
| note | string | catatan |

### Suppliers (`/suppliers`)

Searchable: `name`, `email`, `phone`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama supplier |
| contact_person | string | kontak person |
| email | string | email |
| phone | string | nomor telepon |
| address | string | alamat |

### Transactions (`/transactions`)

Searchable: `invoice_number`, `customer_id`, `status`.

| Field | Type | Notes |
| --- | --- | --- |
| invoice_number | string | nomor invoice |
| customer_id | integer | relasi ke customer |
| subtotal | decimal | subtotal |
| discount | decimal | diskon |
| total | decimal | total |
| payment_method | string | metode pembayaran |
| paid_amount | decimal | jumlah bayar |
| change_amount | decimal | kembalian |

### Transaction Items (`/transaction-items`)

Searchable: `transaction_id`, `product_id`.

| Field | Type | Notes |
| --- | --- | --- |
| transaction_id | integer | relasi ke transaction |
| product_id | integer | relasi ke produk |
| quantity | integer | jumlah |
| price | decimal | harga satuan |
| discount | decimal | diskon |
| hpp | decimal | harga pokok penjualan |
| subtotal_hpp | decimal | subtotal HPP |
| total | decimal | total |

### Users (`/users`)

Searchable: `name`, `email`.

| Field | Type | Notes |
| --- | --- | --- |
| name | string | nama user |
| email | string | email |
| password | string | password (hash on store) |

### Warranties (`/warranties`)

Searchable: `customer_name`, `status`.

| Field | Type | Notes |
| --- | --- | --- |
| type | string | tipe garansi |
| reference_id | integer | referensi data |
| customer_id | integer | relasi ke customer |
| start_date | date | mulai garansi |
| end_date | date | akhir garansi |
| description | string | deskripsi |
| status | string | status |

### Warranty Claims (`/warranty-claims`)

Searchable: `warranty_id`, `status`.

| Field | Type | Notes |
| --- | --- | --- |
| warranty_id | integer | relasi ke warranty |
| claim_date | date | tanggal klaim |
| technician_notes | string | catatan teknisi |
| status | string | status klaim |
| resolution | string | resolusi |

## Penamaan Route (Internal)

Untuk kebutuhan internal Laravel (route name), semua resource API menggunakan prefix `api.`.
Contoh: endpoint `GET /settings` memakai nama route `api.settings.index`.

## Contoh Create

```
POST /customers
```

```json
{
  "name": "Andi",
  "email": "andi@example.com",
  "phone": "08123456789",
  "address": "Jakarta"
}
```

## Contoh Update

```
PATCH /customers/1
```

```json
{
  "phone": "08111111111"
}
```
