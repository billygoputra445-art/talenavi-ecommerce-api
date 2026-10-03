Talenavi — E-Commerce API

RESTful API sederhana untuk kebutuhan e-commerce sebagai bagian dari Technical Test Backend Intern di Talenavi.

Project ini menggunakan Laravel, MySQL, dan Laravel Sanctum untuk autentikasi berbasis API token.

⸻

Tech Stack

* Framework: Laravel 13
* PHP: 8.3+
* Database: MySQL 8.x
* Authentication: Laravel Sanctum
* Validation: Laravel Form Request
* ORM: Laravel Eloquent
* Testing: Laravel Feature / Unit Test

⸻

Database

Project menggunakan MySQL karena kebutuhan utama aplikasi berkaitan dengan transaksi dan konsistensi data, terutama pada proses checkout.

Beberapa hal yang digunakan dalam proses checkout:

* Database transaction melalui DB::transaction()
* lockForUpdate() untuk mengunci row produk selama proses checkout
* Foreign key untuk menjaga integritas relasi antar tabel
* Unique constraint pada kombinasi user_id dan product_id di cart_items

Dengan pendekatan tersebut, proses pembuatan order, penyimpanan order item, pengurangan stok, dan penghapusan cart dilakukan dalam satu transaction. Jika terjadi error, perubahan akan di-rollback.

Database Structure

Project menggunakan 6 tabel utama:

users
 ├── cart_items
 └── orders
       └── order_items
categories
 └── products
       ├── cart_items
       └── order_items

Tables

Table	Description
users	Data akun pengguna
categories	Data kategori produk
products	Data produk, harga, dan stok
cart_items	Item yang sedang berada di keranjang user
orders	Data transaksi yang sudah dibuat
order_items	Detail produk yang terdapat pada sebuah order

Important Relationships

* Satu user dapat memiliki banyak cart_items.
* Satu user dapat memiliki banyak orders.
* Satu category dapat memiliki banyak products.
* Satu product dapat berada di banyak cart_items.
* Satu order memiliki banyak order_items.

Pada cart_items, terdapat unique constraint:

unique(user_id, product_id)

Sehingga satu user tidak akan memiliki dua cart item untuk produk yang sama.

order_items.price menyimpan harga produk ketika checkout dilakukan. Nilai ini digunakan sebagai price snapshot, sehingga perubahan harga produk setelah transaksi tidak mengubah harga pada riwayat order.

⸻

Installation

Requirements

Pastikan environment sudah memiliki:

* PHP >= 8.3
* Composer >= 2.9
* MySQL 8.x

1. Clone Repository

git clone <repository-url>
cd Talenavi

2. Install Dependencies

composer install

⸻

Database Setup

Pastikan MySQL sudah berjalan terlebih dahulu.

Buat database baru:

CREATE DATABASE db_talenavi;

Copy file environment:

cp .env.example .env

Kemudian sesuaikan konfigurasi database pada .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_talenavi
DB_USERNAME=root
DB_PASSWORD=

Generate application key:

php artisan key:generate

Jika menggunakan database remote, sesuaikan konfigurasi DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, dan DB_PASSWORD dengan credentials database yang digunakan.

⸻

Migration & Seeder

Untuk membuat seluruh tabel sekaligus menjalankan seeder:

php artisan migrate:fresh --seed

Seeder akan membuat data awal yang dapat digunakan untuk testing:

* 5 categories
* 30 products
* 1 user

Default User

Email    : test@example.com
Password : password123

⸻

Run the Application

Jalankan development server:

php artisan serve

API kemudian dapat diakses melalui:

http://127.0.0.1:8000/api

⸻

Testing

Untuk menjalankan seluruh test:

php artisan test

Test mencakup beberapa bagian utama seperti:

* Authentication
* Product dan category
* Cart
* Checkout
* Stock validation
* Order
* Authorization

⸻

Postman

Project menyediakan Postman Collection dan Environment untuk mempermudah testing API.

File yang tersedia:

Talenavi_API.postman_collection.json
Talenavi_Environment.postman_environment.json

Import

1. Buka Postman.
2. Pilih Import.
3. Import kedua file tersebut.
4. Pilih environment Talenavi Local Environment.
5. Jalankan request Login User.

Environment menggunakan beberapa variable:

Variable	Description
base_url	Base URL API
token	Sanctum Bearer Token
product_id	ID produk untuk testing
category_id	ID kategori
cart_item_id	ID cart item
order_id	ID order

Default base_url:

http://127.0.0.1:8000/api

Automatic Variables

Postman collection sudah memiliki script untuk membantu proses testing.

Setelah Login atau Register, access_token dari response akan otomatis disimpan ke:

{{token}}

ID yang diperlukan untuk request berikutnya juga dapat diperbarui otomatis, seperti:

{{cart_item_id}}
{{order_id}}

Dengan begitu, alur testing dari login sampai checkout dapat dijalankan tanpa harus memasukkan token dan ID secara manual.

⸻

API Documentation

Dokumentasi endpoint lengkap tersedia di:

API_DOCUMENTATION.md

Dokumentasi tersebut mencakup:

* Authentication
* Categories
* Products
* Cart
* Checkout
* Orders
* Request & response format
* Error response
* Contoh penggunaan API

⸻

API Flow

Alur utama aplikasi:

Register / Login
       ↓
Browse Categories & Products
       ↓
View Product
       ↓
Add Product to Cart
       ↓
View / Update Cart
       ↓
Checkout
       ↓
Create Order
       ↓
Decrease Product Stock
       ↓
Clear Cart
       ↓
View Order History

Pada saat checkout, proses yang berkaitan dengan order dan stock dilakukan dalam satu database transaction. Produk yang akan diproses juga menggunakan row lock untuk menghindari masalah race condition ketika stok yang sama diakses secara bersamaan.

⸻

Project Structure

Struktur utama project mengikuti struktur standar Laravel:

app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
│
database/
├── migrations/
├── seeders/
│
routes/
└── api.php

Business logic yang berkaitan langsung dengan model dan proses database ditempatkan secara terstruktur menggunakan Eloquent dan transaction Laravel, dengan tujuan menjaga implementasi tetap sederhana dan mudah diikuti.

⸻

Notes

Project ini dibuat sebagai implementasi technical test dan berfokus pada beberapa hal utama:

* RESTful API
* Authentication menggunakan Sanctum
* Relasi database menggunakan Eloquent
* Request validation
* Pagination, filtering, dan sorting produk
* Cart management
* Transactional checkout
* Stock consistency
* Price snapshot pada order item
* Automated API testing melalui Postman
