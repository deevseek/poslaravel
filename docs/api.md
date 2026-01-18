# API Documentation (v1)

Dokumentasi ini ditujukan untuk konsumsi aplikasi Android/Flutter.

## Base URL

```
https://<domain>/api/v1
```

## API Index / Health Check

Gunakan endpoint index untuk memastikan API v1 dapat dijangkau.

`GET /` (full path: `/api/v1`)

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
- Dashboard (`/dashboard/*`)
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

## Ringkasan Endpoint per Modul

| Modul | Base Path | Ringkasan Endpoint |
| --- | --- | --- |
| Authentication | `/auth/*` | `POST /auth/login`, `GET /auth/me`, `POST /auth/logout` |
| Dashboard | `/dashboard/*` | `GET /dashboard/summary` |
| Attendance Face Verification | `/attendance/*` | `POST /attendance/check-in`, `POST /attendance/check-out` |
| Attendance Logs | `/attendance-logs` | CRUD standar (list, detail, create, update, delete) |
| Attendances | `/attendances` | CRUD standar |
| Cash Sessions | `/cash-sessions` | CRUD standar |
| Categories | `/categories` | CRUD standar |
| Customers | `/customers` | CRUD standar |
| Employees | `/employees` | CRUD standar |
| Finances | `/finances` | CRUD standar |
| Payrolls | `/payrolls` | CRUD standar |
| Permissions | `/permissions` | CRUD standar |
| Products | `/products` | CRUD standar |
| Purchases | `/purchases` | CRUD standar |
| Purchase Items | `/purchase-items` | CRUD standar |
| Roles | `/roles` | CRUD standar |
| Services | `/services` | CRUD standar |
| Service Items | `/service-items` | CRUD standar |
| Service Logs | `/service-logs` | CRUD standar |
| Settings | `/settings` | CRUD standar |
| Stock Movements | `/stock-movements` | CRUD standar |
| Suppliers | `/suppliers` | CRUD standar |
| Transactions | `/transactions` | CRUD standar |
| Transaction Items | `/transaction-items` | CRUD standar |
| Users | `/users` | CRUD standar |
| Warranties | `/warranties` | CRUD standar |
| Warranty Claims | `/warranty-claims` | CRUD standar |

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

## Dashboard

Endpoint ringkasan untuk kebutuhan dashboard aplikasi.

### Summary

`GET /dashboard/summary`

**Headers**
```
Authorization: Bearer <token>
```

**Query Parameter (Opsional)**
- `days` (integer, default 7): jumlah hari yang ditarik untuk grafik pemasukan/pengeluaran (minimum 1).

**Response**
```json
{
  "data": {
    "today_sales": 1250000,
    "monthly_sales": 45200000,
    "transactions_today": 12,
    "customers_count": 340,
    "products_count": 780,
    "active_services_count": 8,
    "outstanding_purchases": 5000000,
    "recent_transactions": [
      {
        "id": 120,
        "invoice_number": "INV-2026-001",
        "total": 250000,
        "customer": {
          "id": 15,
          "name": "Andi"
        },
        "created_at": "2026-03-10T08:15:30Z"
      }
    ],
    "recent_services": [
      {
        "id": 45,
        "device": "iPhone 13",
        "status": "in_progress",
        "customer": {
          "id": 18,
          "name": "Budi"
        },
        "created_at": "2026-03-10T09:00:00Z"
      }
    ],
    "finance_chart": {
      "labels": ["04 Mar", "05 Mar", "06 Mar", "07 Mar", "08 Mar", "09 Mar", "10 Mar"],
      "income": [100000, 250000, 0, 300000, 200000, 150000, 400000],
      "expense": [50000, 100000, 0, 80000, 60000, 75000, 90000]
    }
  }
}
```

## Common Headers

```
Authorization: Bearer <token>
Content-Type: application/json
```

## Products

Modul ini digunakan untuk mengelola data produk.

### Data Object

Representasi produk pada response.

```json
{
  "id": 12,
  "category_id": 3,
  "name": "Keyboard Mechanical",
  "sku": "KBD-2503-0001",
  "cost_price": "350000.00",
  "avg_cost": "345000.00",
  "price": "525000.00",
  "pricing_mode": "percentage",
  "margin_percentage": "50.00",
  "stock": 25,
  "warranty_days": 180,
  "description": "Switch blue",
  "created_at": "2025-03-10T08:00:00.000000Z",
  "updated_at": "2025-03-10T08:00:00.000000Z",
  "category": {
    "id": 3,
    "name": "Aksesoris"
  }
}
```

### List Products

`GET /products`

**Query Parameters (Opsional)**
- `search` (string): pencarian berdasarkan `name` atau `sku`.
- `category_id` (integer): filter kategori.
- `per_page` (integer, default 15): jumlah data per halaman.

**Response**
```json
{
  "data": [
    {
      "id": 12,
      "category_id": 3,
      "name": "Keyboard Mechanical",
      "sku": "KBD-2503-0001",
      "cost_price": "350000.00",
      "avg_cost": "345000.00",
      "price": "525000.00",
      "pricing_mode": "percentage",
      "margin_percentage": "50.00",
      "stock": 25,
      "warranty_days": 180,
      "description": "Switch blue",
      "created_at": "2025-03-10T08:00:00.000000Z",
      "updated_at": "2025-03-10T08:00:00.000000Z",
      "category": {
        "id": 3,
        "name": "Aksesoris"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 56,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://<domain>/api/v1/products?page=1",
    "last": "https://<domain>/api/v1/products?page=4",
    "prev": null,
    "next": "https://<domain>/api/v1/products?page=2"
  }
}
```

### Detail Product

`GET /products/{id}`

**Response**
```json
{
  "data": {
    "id": 12,
    "category_id": 3,
    "name": "Keyboard Mechanical",
    "sku": "KBD-2503-0001",
    "cost_price": "350000.00",
    "avg_cost": "345000.00",
    "price": "525000.00",
    "pricing_mode": "percentage",
    "margin_percentage": "50.00",
    "stock": 25,
    "warranty_days": 180,
    "description": "Switch blue",
    "created_at": "2025-03-10T08:00:00.000000Z",
    "updated_at": "2025-03-10T08:00:00.000000Z",
    "category": {
      "id": 3,
      "name": "Aksesoris"
    }
  }
}
```

### Create Product

`POST /products`

**Request Body**
```json
{
  "category_id": 3,
  "name": "Keyboard Mechanical",
  "sku": "KBD-2503-0001",
  "cost_price": 350000,
  "price": 525000,
  "pricing_mode": "manual",
  "margin_percentage": null,
  "stock": 25,
  "warranty_days": 180,
  "description": "Switch blue"
}
```

**Validasi & Aturan**
- `category_id` wajib dan harus ada di tabel categories.
- `name` wajib, maksimal 255 karakter.
- `sku` opsional, unik. Jika tidak diisi, sistem akan membuatkan otomatis berdasarkan kategori.
- `pricing_mode` wajib: `manual` atau `percentage`.
- Jika `pricing_mode` = `manual`, maka `price` wajib.
- Jika `pricing_mode` = `percentage`, maka `cost_price` dan `margin_percentage` wajib, `cost_price` > 0, dan `price` akan dihitung otomatis.
- `stock` wajib dan minimum 0.

**Response (201)**
```json
{
  "data": {
    "id": 12,
    "category_id": 3,
    "name": "Keyboard Mechanical",
    "sku": "KBD-2503-0001",
    "cost_price": "350000.00",
    "avg_cost": "0.00",
    "price": "525000.00",
    "pricing_mode": "manual",
    "margin_percentage": null,
    "stock": 25,
    "warranty_days": 180,
    "description": "Switch blue",
    "created_at": "2025-03-10T08:00:00.000000Z",
    "updated_at": "2025-03-10T08:00:00.000000Z"
  }
}
```

### Update Product

`PUT /products/{id}` atau `PATCH /products/{id}`

**Request Body (Contoh)**
```json
{
  "pricing_mode": "percentage",
  "cost_price": 400000,
  "margin_percentage": 30,
  "stock": 30
}
```

**Validasi & Aturan**
- Semua field bersifat opsional, namun akan divalidasi jika dikirim.
- Jika `pricing_mode` berubah menjadi `manual`, `margin_percentage` akan diset `null`.
- Jika `pricing_mode` = `percentage` dan `margin_percentage` tersedia, `price` dihitung ulang otomatis dari `cost_price` dan `margin_percentage`.
- `sku` harus unik terhadap produk lain.

**Response**
```json
{
  "data": {
    "id": 12,
    "category_id": 3,
    "name": "Keyboard Mechanical",
    "sku": "KBD-2503-0001",
    "cost_price": "400000.00",
    "avg_cost": "345000.00",
    "price": "520000.00",
    "pricing_mode": "percentage",
    "margin_percentage": "30.00",
    "stock": 30,
    "warranty_days": 180,
    "description": "Switch blue",
    "created_at": "2025-03-10T08:00:00.000000Z",
    "updated_at": "2025-03-10T09:00:00.000000Z",
    "category": {
      "id": 3,
      "name": "Aksesoris"
    }
  }
}
```

### Delete Product

`DELETE /products/{id}`

**Response**
```json
{
  "message": "Deleted."
}
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

## Customers

Modul customer menggunakan resource `/customers` **di bawah prefix `/api/v1`**. Jika memanggil `/customers` tanpa prefix `/api/v1`, server akan mengembalikan `404 Not Found`.

**Base URL**
```
/api/v1/customers
```

**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```

### Data Fields

| Field | Type | Notes |
| --- | --- | --- |
| name | string | **required** saat create, nama pelanggan |
| email | string | optional, harus unik |
| phone | string | optional |
| address | string | optional |

### Customer Index (List)

Endpoint index untuk modul customer.

`GET /api/v1/customers`

**Query Parameters**
- `per_page` (opsional, default 15)
- `search` (opsional, mencari di `name`, `email`, `phone`)

**Contoh**
```
GET /api/v1/customers?per_page=20&search=andi
```

**Response sukses (200)**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Andi",
      "email": "andi@example.com",
      "phone": "08123456789",
      "address": "Jakarta",
      "created_at": "2026-03-10T09:00:00Z",
      "updated_at": "2026-03-10T09:00:00Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/v1/customers?page=1",
    "last": "http://localhost/api/v1/customers?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost/api/v1/customers",
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### Detail Customer

`GET /api/v1/customers/{id}`

**Response sukses (200)**
```json
{
  "data": {
    "id": 1,
    "name": "Andi",
    "email": "andi@example.com",
    "phone": "08123456789",
    "address": "Jakarta",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:00:00Z"
  }
}
```

### Create Customer

`POST /api/v1/customers`

**Request Body**
```json
{
  "name": "Andi",
  "email": "andi@example.com",
  "phone": "08123456789",
  "address": "Jakarta"
}
```

**Response sukses (201)**
```json
{
  "data": {
    "id": 1,
    "name": "Andi",
    "email": "andi@example.com",
    "phone": "08123456789",
    "address": "Jakarta",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:00:00Z"
  }
}
```

### Update Customer

`PATCH /api/v1/customers/{id}`

**Request Body**
```json
{
  "phone": "08111111111",
  "address": "Bandung"
}
```

**Response sukses (200)**
```json
{
  "data": {
    "id": 1,
    "name": "Andi",
    "email": "andi@example.com",
    "phone": "08111111111",
    "address": "Bandung",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:10:00Z"
  }
}
```

### Delete Customer

`DELETE /api/v1/customers/{id}`

**Response sukses (200)**
```json
{
  "message": "Deleted."
}
```

## Categories

Modul kategori menggunakan resource `/categories` **di bawah prefix `/api/v1`**. Jika memanggil `/categories` tanpa prefix `/api/v1`, server akan mengembalikan `404 Not Found`.

**Base URL**
```
/api/v1/categories
```

**Headers**
```
Authorization: Bearer <token>
Content-Type: application/json
```

### Ringkasan Endpoint

| Method | Endpoint | Deskripsi |
| --- | --- | --- |
| GET | `/api/v1/categories` | Ambil daftar kategori (pagination + search). |
| GET | `/api/v1/categories/{id}` | Ambil detail kategori. |
| POST | `/api/v1/categories` | Buat kategori baru. |
| PATCH | `/api/v1/categories/{id}` | Perbarui kategori (partial update). |
| DELETE | `/api/v1/categories/{id}` | Hapus kategori. |

### Data Fields

| Field | Type | Notes |
| --- | --- | --- |
| name | string | **required** saat create, unik, nama kategori |
| description | string | optional, deskripsi kategori |

### Validasi

**Create**
- `name`: required, string, max 255 karakter
- `description`: nullable, string

**Update**
- `name`: optional, string, max 255 karakter
- `description`: optional, nullable, string

### Category Index (List)

Endpoint index untuk modul kategori.

`GET /api/v1/categories`

**Query Parameters**
- `per_page` (opsional, default 15)
- `search` (opsional, mencari di `name`)

**Contoh**
```
GET /api/v1/categories?per_page=20&search=aksesoris
```

**Response sukses (200)**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Aksesoris",
      "description": "Aksesoris tambahan",
      "created_at": "2026-03-10T09:00:00Z",
      "updated_at": "2026-03-10T09:00:00Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/v1/categories?page=1",
    "last": "http://localhost/api/v1/categories?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://localhost/api/v1/categories",
    "per_page": 20,
    "to": 1,
    "total": 1
  }
}
```

### Detail Category

`GET /api/v1/categories/{id}`

**Response sukses (200)**
```json
{
  "data": {
    "id": 1,
    "name": "Aksesoris",
    "description": "Aksesoris tambahan",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:00:00Z"
  }
}
```

### Create Category

`POST /api/v1/categories`

**Request Body**
```json
{
  "name": "Aksesoris",
  "description": "Aksesoris tambahan"
}
```

**Response sukses (201)**
```json
{
  "data": {
    "id": 1,
    "name": "Aksesoris",
    "description": "Aksesoris tambahan",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:00:00Z"
  }
}
```

**Response gagal (422)**
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```

### Update Category

`PATCH /api/v1/categories/{id}`

**Request Body**
```json
{
  "description": "Update deskripsi kategori"
}
```

**Response sukses (200)**
```json
{
  "data": {
    "id": 1,
    "name": "Aksesoris",
    "description": "Update deskripsi kategori",
    "created_at": "2026-03-10T09:00:00Z",
    "updated_at": "2026-03-10T09:10:00Z"
  }
}
```

**Response gagal (404)**
```json
{
  "message": "No query results for model [App\\\\Models\\\\Category] {id}"
}
```

### Delete Category

`DELETE /api/v1/categories/{id}`

**Response sukses (200)**
```json
{
  "message": "Deleted."
}
```

**Response gagal (404)**
```json
{
  "message": "No query results for model [App\\\\Models\\\\Category] {id}"
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

#### Daftar Supplier

`GET /suppliers`

**Query Parameter**
- `search` (string, opsional): pencarian di `name`, `contact_person`, `email`, `phone`.
- `per_page` (integer, opsional, default 15): jumlah data per halaman.

**Response**
```json
{
  "data": [
    {
      "id": 12,
      "name": "PT Sumber Jaya",
      "contact_person": "Rina",
      "email": "rina@sumberjaya.co.id",
      "phone": "08123456789",
      "address": "Jl. Merdeka No. 10",
      "created_at": "2026-03-10T08:10:00Z",
      "updated_at": "2026-03-10T08:10:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 15,
    "total": 52,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "https://<domain>/api/v1/suppliers?page=1",
    "last": "https://<domain>/api/v1/suppliers?page=4",
    "prev": null,
    "next": "https://<domain>/api/v1/suppliers?page=2"
  }
}
```

#### Detail Supplier

`GET /suppliers/{id}`

**Response**
```json
{
  "data": {
    "id": 12,
    "name": "PT Sumber Jaya",
    "contact_person": "Rina",
    "email": "rina@sumberjaya.co.id",
    "phone": "08123456789",
    "address": "Jl. Merdeka No. 10",
    "created_at": "2026-03-10T08:10:00Z",
    "updated_at": "2026-03-10T08:10:00Z"
  }
}
```

#### Tambah Supplier

`POST /suppliers`

**Body**
```json
{
  "name": "PT Sumber Jaya",
  "contact_person": "Rina",
  "email": "rina@sumberjaya.co.id",
  "phone": "08123456789",
  "address": "Jl. Merdeka No. 10"
}
```

**Validasi**
- `name` (required, string, max 255)
- `contact_person` (nullable, string, max 255)
- `email` (nullable, email, max 255, unique)
- `phone` (nullable, string, max 50)
- `address` (nullable, string)

**Response (201)**
```json
{
  "data": {
    "id": 12,
    "name": "PT Sumber Jaya",
    "contact_person": "Rina",
    "email": "rina@sumberjaya.co.id",
    "phone": "08123456789",
    "address": "Jl. Merdeka No. 10",
    "created_at": "2026-03-10T08:10:00Z",
    "updated_at": "2026-03-10T08:10:00Z"
  }
}
```

#### Ubah Supplier

`PATCH /suppliers/{id}`

**Body**
```json
{
  "name": "PT Sumber Jaya Abadi",
  "phone": "08123456780"
}
```

**Validasi**
- `name` (sometimes, string, max 255)
- `contact_person` (sometimes, nullable, string, max 255)
- `email` (sometimes, nullable, email, max 255, unique kecuali id saat ini)
- `phone` (sometimes, nullable, string, max 50)
- `address` (sometimes, nullable, string)

**Response**
```json
{
  "data": {
    "id": 12,
    "name": "PT Sumber Jaya Abadi",
    "contact_person": "Rina",
    "email": "rina@sumberjaya.co.id",
    "phone": "08123456780",
    "address": "Jl. Merdeka No. 10",
    "created_at": "2026-03-10T08:10:00Z",
    "updated_at": "2026-03-12T02:00:00Z"
  }
}
```

#### Hapus Supplier

`DELETE /suppliers/{id}`

**Response**
```json
{
  "message": "Deleted."
}
```

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
