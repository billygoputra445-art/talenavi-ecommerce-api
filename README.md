# Talenavi — E-Commerce RESTful API

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Sanctum](https://img.shields.io/badge/Authentication-Laravel%20Sanctum-red?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![Testing](https://img.shields.io/badge/Testing-PHPUnit-blue?style=for-the-badge&logo=phpunit&logoColor=white)](https://phpunit.de)

RESTful API sederhana untuk kebutuhan e-commerce sebagai bagian dari Technical Test Backend Intern di Talenavi.

Project ini menggunakan Laravel 13, MySQL, dan Laravel Sanctum untuk autentikasi berbasis API token.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Key Features](#key-features)
- [Database Architecture & Integrity](#database-architecture--integrity)
  - [Database Structure](#database-structure)
  - [Database Tables](#database-tables)
  - [Important Relationships & Constraints](#important-relationships--constraints)
- [API Flow](#api-flow)
- [Installation & Setup](#installation--setup)
  - [Requirements](#requirements)
  - [1. Clone Repository](#1-clone-repository)
  - [2. Install Dependencies](#2-install-dependencies)
  - [3. Environment & Database Setup](#3-environment--database-setup)
  - [4. Migration & Seeder](#4-migration--seeder)
  - [5. Run Development Server](#5-run-development-server)
- [Testing](#testing)
- [Postman Collection](#postman-collection)
- [API Documentation](#api-documentation)
- [Project Structure](#project-structure)
- [Notes & Technical Focus](#notes--technical-focus)

---

## Tech Stack

* Framework: Laravel 13
* PHP: 8.3+
* Database: MySQL 8.x
* Authentication: Laravel Sanctum
* Validation: Laravel Form Request
* ORM: Laravel Eloquent
* Testing: Laravel Feature / Unit Test

---

## Key Features

- **Authentication System**: Register, Login, Logout, dan User Profile via Laravel Sanctum.
- **Category & Product Management**: Browse kategori, daftar produk dengan pagination, filtering (kategori/stok/search), dan sorting.
- **Cart Management**: Tambah, update, lihat, dan hapus item keranjang dengan validasi kuantitas serta unique constraint.
- **Transactional Checkout**:
  - **Database Transactions** (`DB::transaction`) menjamin atomisitas seluruh alur checkout.
  - **Pessimistic Row Locking** (`lockForUpdate()`) mencegah race condition saat manipulasi stok produk.
  - **Price Snapshot**: Menyimpan snapshot harga produk pada saat transaksi terjadi.
  - **Stock Consistency**: Pengurangan stok otomatis dan pembersihan keranjang belanja setelah checkout.
- **Order History**: Riwayat pesanan dan detail item pesanan yang telah diproses.

---

## Database Architecture & Integrity

Project ini menggunakan MySQL karena kebutuhan utama aplikasi berkaitan dengan transaksi dan konsistensi data, terutama pada proses checkout.

Beberapa hal yang digunakan dalam proses checkout:
- **Database transaction** melalui `DB::transaction()` untuk memastikan seluruh operasi pembuatan order, order item, pengurangan stok, dan pembersihan cart berhasil sepenuhnya atau di-rollback jika terjadi error.
- **Pessimistic Row Locking** melalui `lockForUpdate()` untuk mengunci row produk selama proses checkout agar terhindar dari race condition.
- **Foreign key** untuk menjaga integritas relasi antar tabel (`users`, `categories`, `products`, `cart_items`, `orders`, `order_items`).
- **Unique constraint** pada kombinasi `user_id` dan `product_id` di tabel `cart_items`.

### Database Structure

Project menggunakan 6 tabel utama:

```text
users
 ├── cart_items
 └── orders
       └── order_items

categories
 └── products
       ├── cart_items
       └── order_items
```

### Database Tables

| Table | Description |
| :--- | :--- |
| `users` | Data akun dan kredensial pengguna |
| `categories` | Data kategori produk |
| `products` | Data produk, harga, dan stok |
| `cart_items` | Item keranjang belanja pengguna |
| `orders` | Data transaksi/pesanan pengguna |
| `order_items` | Detail produk pada sebuah order (beserta price snapshot) |

### Important Relationships & Constraints

- Satu user dapat memiliki banyak `cart_items`.
- Satu user dapat memiliki banyak `orders`.
- Satu category dapat memiliki banyak `products`.
- Satu product dapat berada di banyak `cart_items` dan `order_items`.
- Satu order memiliki banyak `order_items`.

Pada tabel `cart_items`, terdapat unique constraint:
```sql
unique(user_id, product_id)
```
Sehingga satu user tidak akan memiliki dua cart item untuk produk yang sama.

`order_items.price` menyimpan harga produk ketika checkout dilakukan. Nilai ini digunakan sebagai price snapshot, sehingga perubahan harga produk di masa mendatang tidak mengubah harga pada riwayat order.

---

## API Flow

Alur utama aplikasi:

```text
Register / Login
       │
       ▼
Browse Categories & Products
       │
       ▼
View Product Detail
       │
       ▼
Add Product to Cart
       │
       ▼
View / Update Cart
       │
       ▼
Checkout  ───►  DB::transaction() & lockForUpdate()
       │        ├── Create Order
       │        ├── Save Order Items (Price Snapshot)
       │        ├── Deduct Product Stock
       │        └── Clear Cart Items
       ▼
View Order History
```

Pada saat checkout, proses yang berkaitan dengan order dan stock dilakukan dalam satu database transaction. Produk yang akan diproses juga menggunakan row lock untuk menghindari masalah race condition ketika stok yang sama diakses secara bersamaan.

---

## Installation & Setup

### Requirements

Pastikan environment sudah memiliki:
- PHP >= 8.3
- Composer >= 2.9
- MySQL 8.x

### 1. Clone Repository

```bash
git clone <repository-url>
cd Talenavi
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment & Database Setup

Pastikan MySQL sudah berjalan terlebih dahulu.

Buat database baru:

```sql
CREATE DATABASE db_talenavi;
```

Copy file environment:

```bash
cp .env.example .env
```

Kemudian sesuaikan konfigurasi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_talenavi
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:

```bash
php artisan key:generate
```

Jika menggunakan database remote, sesuaikan konfigurasi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dengan credentials yang digunakan.

### 4. Migration & Seeder

Untuk membuat seluruh tabel sekaligus menjalankan seeder:

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat data awal yang dapat digunakan untuk testing:
- 5 categories
- 30 products
- 1 default user

#### Default User Credentials

* Email: `test@example.com`
* Password: `password123`

### 5. Run Development Server

Jalankan development server:

```bash
php artisan serve
```

API kemudian dapat diakses melalui:  
`http://127.0.0.1:8000/api`

---

## Testing

Untuk menjalankan seluruh test suite:

```bash
php artisan test
```

Test mencakup beberapa bagian utama seperti:
- Authentication
- Product dan category
- Cart
- Checkout & Stock validation
- Order & Authorization

---

## Postman Collection

Project menyediakan Postman Collection dan Environment untuk mempermudah testing API.

File yang tersedia:
- `Talenavi_API.postman_collection.json`
- `Talenavi_Environment.postman_environment.json`

### Import & Penggunaan

1. Buka Postman.
2. Pilih **Import**.
3. Import kedua file tersebut.
4. Pilih environment **Talenavi Local Environment**.
5. Jalankan request **Login User**.

Environment menggunakan beberapa variable:

| Variable | Description |
| :--- | :--- |
| `base_url` | Base URL API |
| `token` | Sanctum Bearer Token |
| `product_id` | ID produk untuk testing |
| `category_id` | ID kategori |
| `cart_item_id` | ID cart item |
| `order_id` | ID order |

Default `base_url`: `http://127.0.0.1:8000/api`

### Automatic Variables

Postman collection sudah memiliki script untuk membantu proses testing.

Setelah Login atau Register, `access_token` dari response akan otomatis disimpan ke `{{token}}`.

ID yang diperlukan untuk request berikutnya juga dapat diperbarui otomatis:
- `{{cart_item_id}}`
- `{{order_id}}`

Dengan begitu, alur testing dari login sampai checkout dapat dijalankan tanpa harus memasukkan token dan ID secara manual.

---

## API Documentation

Dokumentasi endpoint lengkap tersedia di:

[API_DOCUMENTATION.md](file:///Users/billigo/development/intern/Talenavi/API_DOCUMENTATION.md)

Dokumentasi tersebut mencakup:
- Authentication
- Categories
- Products
- Cart
- Checkout
- Orders
- Request & response format
- Error response
- Contoh penggunaan API

---

## Project Structure

Struktur utama project mengikuti struktur standar Laravel:

```text
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
└── Models/

database/
├── migrations/
└── seeders/

routes/
└── api.php
```

Business logic yang berkaitan langsung dengan model dan proses database ditempatkan secara terstruktur menggunakan Eloquent dan transaction Laravel, dengan tujuan menjaga implementasi tetap sederhana dan mudah diikuti.

---

## Notes & Technical Focus

Project ini dibuat sebagai implementasi technical test dan berfokus pada beberapa hal utama:

- RESTful API
- Authentication menggunakan Sanctum
- Relasi database menggunakan Eloquent
- Request validation
- Pagination, filtering, dan sorting produk
- Cart management
- Transactional checkout
- Stock consistency
- Price snapshot pada order item
- Automated API testing melalui Postman
