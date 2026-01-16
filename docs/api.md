# API Documentation (v1)

Dokumentasi ini ditujukan untuk konsumsi aplikasi Android/Flutter.

## Base URL

```
https://<domain>/api/v1
```

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

### Logout

`POST /auth/logout`

**Headers**
```
Authorization: Bearer <token>
```

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
- `image` (file, required)

### Check-out
`POST /attendance/check-out`

**Headers** sama dengan check-in.

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

### Daftar Resource
- `/attendance-logs`
- `/attendances`
- `/cash-sessions`
- `/categories`
- `/customers`
- `/employees`
- `/finances`
- `/payrolls`
- `/permissions`
- `/products`
- `/purchases`
- `/purchase-items`
- `/roles`
- `/services`
- `/service-items`
- `/service-logs`
- `/settings`
- `/stock-movements`
- `/suppliers`
- `/transactions`
- `/transaction-items`
- `/users`
- `/warranties`
- `/warranty-claims`

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

## Error

- `401 Unauthorized` jika token tidak valid.
- `404 Not Found` jika resource tidak terdaftar atau data tidak ditemukan.
- `422 Unprocessable Entity` jika validasi gagal.
