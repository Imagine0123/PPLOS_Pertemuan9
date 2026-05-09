# Backend Service Microservices Laravel

## Deskripsi Proyek

Project ini merupakan implementasi backend service berbasis microservices menggunakan Laravel yang memenuhi requirement tugas mata kuliah, meliputi:

- Database dan relasi antar entitas
- CRUD API
- Authentication menggunakan JWT
- Role-based authorization
- Message broker menggunakan RabbitMQ
- Asynchronous communication
- API Gateway
- Logging middleware

Project terdiri dari beberapa service yang saling terhubung dan berjalan secara terpisah.

---

# Arsitektur Sistem

```text
Client
   |
   v
API Gateway (Port 8000)
   |
   |-------------------------|
   |                         |
   v                         v
Auth Service           Order Service
(Port 8001)            (Port 8002)
                               |
                               v
                         RabbitMQ Broker
                               |
                               v
                        Consumer Worker
```

---

# Teknologi yang Digunakan

| Komponen | Teknologi |
|---|---|
| Framework | Laravel |
| Database | MySQL |
| Authentication | JWT |
| Message Broker | RabbitMQ |
| API Communication | REST API |
| Queue Consumer | Laravel Console Command |

---

# Struktur Project

```text
backend/
├── gateway/
├── auth-service/
└── order-service/
```

---

# Cara Menjalankan Project

## 1. Clone Repository

```bash
git clone <repository-url>
cd backend
```

---

# 2. Install Dependency

## Gateway

```bash
cd gateway
composer install
```

## Auth Service

```bash
cd ../auth-service
composer install
```

## Order Service

```bash
cd ../order-service
composer install
```

---

# 3. Konfigurasi Environment

Copy file `.env.example` menjadi `.env`

```bash
cp .env.example .env
```

Lalu sesuaikan konfigurasi database dan RabbitMQ.

---

# 4. Generate Application Key

Jalankan pada setiap service:

```bash
php artisan key:generate
```

---

# 5. Migrasi Database

## Auth Service

```bash
cd auth-service
php artisan migrate
```

## Order Service

```bash
cd ../order-service
php artisan migrate
```

---

# 6. Menjalankan Service

## Gateway

```bash
cd gateway
php artisan serve --port=8000
```

## Auth Service

```bash
cd auth-service
php artisan serve --port=8001
```

## Order Service

```bash
cd order-service
php artisan serve --port=8002
```

---

# 7. Menjalankan Consumer RabbitMQ

Masuk ke folder `order-service`

```bash
php artisan broker:consume-orders
```

---

# Konfigurasi Port

| Service | Port |
|---|---|
| Gateway | 8000 |
| Auth Service | 8001 |
| Order Service | 8002 |

---

# Database Schema

## users

| Field | Type |
|---|---|
| id | bigint |
| name | string |
| email | string |
| password | string |
| role | string |

---

## products

| Field | Type |
|---|---|
| id | bigint |
| name | string |
| stock | integer |
| price | decimal |

---

## orders

| Field | Type |
|---|---|
| id | bigint |
| user_id | bigint |
| total_price | decimal |
| status | string |

---

## order_items

| Field | Type |
|---|---|
| id | bigint |
| order_id | bigint |
| product_id | bigint |
| quantity | integer |
| price | decimal |

---

# Authentication

Authentication menggunakan JWT.

Setelah login berhasil, user akan mendapatkan token:

```json
{
  "access_token": "TOKEN_JWT"
}
```

Token digunakan pada endpoint protected:

```http
Authorization: Bearer TOKEN_JWT
```

---

# Role Authorization

Terdapat 2 role:

| Role | Akses |
|---|---|
| admin | CRUD product |
| customer | membuat order |

---

# Daftar Endpoint

# AUTH SERVICE

## Register

### Request

```http
POST /api/auth/register
```

### Body

```json
{
  "name": "Admin",
  "email": "admin@test.com",
  "password": "password",
  "role": "admin"
}
```

### Response

```json
{
  "message": "User registered successfully"
}
```

---

## Login

### Request

```http
POST /api/auth/login
```

### Body

```json
{
  "email": "admin@test.com",
  "password": "password"
}
```

### Response

```json
{
  "access_token": "JWT_TOKEN"
}
```

---

# PRODUCT SERVICE

## Get Products

```http
GET /api/products
```

---

## Create Product

```http
POST /api/products
```

### Header

```http
Authorization: Bearer TOKEN
```

### Body

```json
{
  "name": "Keyboard",
  "stock": 10,
  "price": 250000
}
```

---

## Update Product

```http
PUT /api/products/{id}
```

---

## Delete Product

```http
DELETE /api/products/{id}
```

---

# ORDER SERVICE

## Create Order

```http
POST /api/orders
```

### Header

```http
Authorization: Bearer TOKEN
```

### Body

```json
{
  "user_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ]
}
```

### Response

```json
{
  "message": "Order created"
}
```

---

# RabbitMQ Asynchronous Communication

Ketika order berhasil dibuat:

1. Order Service menyimpan data ke database
2. Service mengirim event ke RabbitMQ
3. Consumer menerima event secara asynchronous

Contoh event:

```json
{
  "event": "order.created",
  "order_id": 1
}
```

Contoh output consumer:

```text
Received order.created event
```

---

# Logging Middleware

API Gateway menggunakan middleware logging untuk mencatat request yang masuk.

Data yang dicatat:

- HTTP Method
- URL
- IP Address

---

# Deployment Server LeAds

Project dijalankan pada server LeAds menggunakan SSH:

```bash
ssh -p 8989 mahasiswa@103.147.92.134
```

---

# Author

| Nama | NIM |
|---|---|
| Rafdi Nur Zhafir Rahman | 2310511069 |
