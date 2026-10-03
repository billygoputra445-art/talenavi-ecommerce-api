API Documentation — Backend Intern Talenavi Technical Test

Overview

API ini merupakan backend sederhana untuk kebutuhan e-commerce yang dibuat menggunakan Laravel 13, MySQL, dan Laravel Sanctum.

API menyediakan fitur autentikasi user, katalog produk, kategori, keranjang belanja, serta proses checkout dan riwayat pesanan.

Basic Configuration

* Base URL: {{base_url}}
* Default: http://127.0.0.1:8000/api
* Content-Type: application/json
* Accept: application/json
* Authentication: Bearer Token

Untuk endpoint yang membutuhkan login, token dikirim melalui header:

Authorization: Bearer {{token}}

Postman collection tersedia pada:

Talenavi_API.postman_collection.json

Collection tersebut bisa langsung di-import ke Postman untuk mencoba seluruh endpoint.

⸻

Response Format

Response berhasil menggunakan format berikut:

{
  "success": true,
  "message": "Human readable success message",
  "data": {}
}

Untuk request yang menggunakan pagination, informasi pagination dikembalikan melalui field meta atau mengikuti struktur pagination endpoint terkait.

⸻

Error Response

API menggunakan HTTP status code sesuai dengan jenis error yang terjadi.

Validation Error — 422

Dikembalikan ketika data yang dikirim tidak memenuhi aturan validasi.

{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}

Unauthenticated — 401

Dikembalikan ketika endpoint private dipanggil tanpa token yang valid.

{
  "success": false,
  "message": "Unauthenticated"
}

Resource Not Found — 404

Digunakan ketika resource yang diminta tidak ditemukan, misalnya produk, cart item, atau order.

{
  "success": false,
  "message": "Resource not found"
}

Insufficient Stock — 400

Saat checkout, stok produk dicek kembali di dalam transaction. Jika stok tidak mencukupi, checkout dibatalkan.

{
  "success": false,
  "message": "Insufficient stock for product: Wireless Mouse"
}

Empty Cart — 400

Checkout tidak dapat dilakukan jika user belum memiliki item di dalam cart.

{
  "success": false,
  "message": "Cart is empty"
}

⸻

1. Authentication

Endpoint pada bagian ini digunakan untuk register, login, melihat user yang sedang login, dan logout.

Register

Public

POST {{base_url}}/register

Request

Headers:

Content-Type: application/json
Accept: application/json

Body:

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123"
}

Response — 201 Created

{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2026-10-02T16:00:00.000000Z"
    },
    "access_token": "1|sanctum_token_string...",
    "token_type": "Bearer"
  }
}

Token yang diterima dapat digunakan untuk mengakses endpoint private.

⸻

Login

Public

POST {{base_url}}/login

Request

{
  "email": "test@example.com",
  "password": "password123"
}

Response — 200 OK

{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com",
      "created_at": "2026-10-02T15:00:00.000000Z"
    },
    "access_token": "2|sanctum_token_string...",
    "token_type": "Bearer"
  }
}

⸻

Get Current User

Private

GET {{base_url}}/me

Header:

Authorization: Bearer {{token}}
Accept: application/json

Response — 200 OK

{
  "success": true,
  "message": "Authenticated user profile",
  "data": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com"
  }
}

⸻

Logout

Private

POST {{base_url}}/logout

Header:

Authorization: Bearer {{token}}
Accept: application/json

Response — 200 OK

{
  "success": true,
  "message": "Logged out successfully"
}

⸻

2. Products & Categories

Endpoint pada bagian ini bersifat public dan dapat digunakan untuk menampilkan katalog produk.

Get Categories

Public

GET {{base_url}}/categories

Response — 200 OK

{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    },
    {
      "id": 2,
      "name": "Fashion",
      "slug": "fashion"
    }
  ]
}

⸻

Get Products

Public

GET {{base_url}}/products

Endpoint ini mendukung pencarian, filter kategori, sorting, dan pagination.

Query Parameters

Parameter	Keterangan
q / search	Mencari produk berdasarkan title atau description
category / category_id	Filter berdasarkan slug atau ID kategori
sort	price_asc, price_desc, atau newest
sort_by	Kolom yang digunakan untuk sorting, seperti price, created_at, title, atau stock
sort_dir	Arah sorting: asc atau desc
limit / per_page	Jumlah data per halaman. Default 10, maksimal 50
page	Nomor halaman

Contoh:

GET {{base_url}}/products?search=Mouse&sort_by=price&sort_dir=asc

Response — 200 OK

{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "title": "Wireless Mouse",
      "price": "75000.00",
      "stock": 20,
      "category": {
        "id": 1,
        "name": "Electronics",
        "slug": "electronics"
      }
    }
  ],
  "meta": {
    "page": 1,
    "limit": 10,
    "total": 30,
    "total_pages": 3
  }
}

⸻

Get Product Detail

Public

GET {{base_url}}/products/{{product_id}}

Response — 200 OK

{
  "success": true,
  "message": "Product detail retrieved successfully",
  "data": {
    "id": 1,
    "category_id": 1,
    "title": "Wireless Mouse",
    "description": "Wireless mouse for laptop and PC.",
    "price": "75000.00",
    "stock": 20,
    "category": {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics"
    }
  }
}

⸻

3. Cart

Semua endpoint cart membutuhkan user yang sudah login. Setiap user hanya dapat mengakses cart miliknya sendiri.

Get Cart

Private

GET {{base_url}}/cart

Header:

Authorization: Bearer {{token}}
Accept: application/json

Response — 200 OK

{
  "success": true,
  "message": "Cart items retrieved successfully",
  "data": {
    "items": [
      {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "quantity": 2,
        "product": {
          "id": 1,
          "title": "Wireless Mouse",
          "price": "75000.00",
          "stock": 20
        }
      }
    ],
    "total_price": 150000
  }
}

⸻

Add Product to Cart

Private

POST {{base_url}}/cart

Request

{
  "product_id": 1,
  "quantity": 2
}

Response — 201 Created

{
  "success": true,
  "message": "Product added to cart successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "quantity": 2
  }
}

⸻

Update Cart Item

Private

PATCH {{base_url}}/cart/{{cart_item_id}}

Request

{
  "quantity": 5
}

Response — 200 OK

{
  "success": true,
  "message": "Cart item updated successfully",
  "data": {
    "id": 1,
    "quantity": 5
  }
}

⸻

Remove Cart Item

Private

DELETE {{base_url}}/cart/{{cart_item_id}}

Response — 200 OK

{
  "success": true,
  "message": "Cart item deleted successfully"
}

⸻

4. Orders & Checkout

Bagian ini menangani proses checkout dan akses ke order yang sudah dibuat.

Checkout

Private

POST {{base_url}}/orders/checkout

Checkout dijalankan dalam satu database transaction.

Secara umum prosesnya:

1. Mengambil item dari cart user.
2. Mengecek apakah cart kosong.
3. Mengambil produk yang diperlukan menggunakan lockForUpdate().
4. Mengecek stok masing-masing produk.
5. Membuat order.
6. Membuat order_items.
7. Menyimpan harga produk saat transaksi sebagai price snapshot.
8. Mengurangi stok produk.
9. Menghapus item dari cart.
10. Jika salah satu proses gagal, seluruh transaction di-rollback.

Penggunaan row lock membantu mencegah dua checkout memproses stok produk yang sama secara bersamaan.

Response — 201 Created

{
  "success": true,
  "message": "Checkout completed successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "total_price": "150000.00",
    "status": "paid",
    "items": [
      {
        "id": 1,
        "order_id": 1,
        "product_id": 1,
        "quantity": 2,
        "price": "75000.00",
        "product": {
          "id": 1,
          "title": "Wireless Mouse"
        }
      }
    ]
  }
}

⸻

Get Order History

Private

GET {{base_url}}/orders

Endpoint ini menampilkan order milik user yang sedang login.

Response — 200 OK

{
  "success": true,
  "message": "Order history retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "total_price": "150000.00",
        "status": "paid"
      }
    ],
    "per_page": 10,
    "total": 1
  }
}

⸻

Get Order Detail

Private

GET {{base_url}}/orders/{{order_id}}

Response — 200 OK

{
  "success": true,
  "message": "Order details retrieved successfully",
  "data": {
    "id": 1,
    "user_id": 1,
    "total_price": "150000.00",
    "status": "paid",
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "price": "75000.00"
      }
    ]
  }
}

⸻

End-to-End Example

Berikut alur sederhana untuk mencoba API dari awal sampai checkout menggunakan Postman.

1. Register

POST {{base_url}}/register

Body:

{
  "name": "Budi",
  "email": "budi@example.com",
  "password": "password123"
}

Setelah berhasil, API mengembalikan access token.

⸻

2. Login

POST {{base_url}}/login

Body:

{
  "email": "budi@example.com",
  "password": "password123"
}

Token dari response dapat disimpan ke variable Postman:

{{token}}

Dengan begitu, endpoint private bisa menggunakan header:

Authorization: Bearer {{token}}

⸻

3. Lihat Kategori dan Produk

Ambil daftar kategori:

GET {{base_url}}/categories

Kemudian cari produk:

GET {{base_url}}/products?search=Mouse&sort_by=price&sort_dir=asc

Dari hasil tersebut, pilih product_id yang ingin dibeli.

⸻

4. Cek Detail Produk

GET {{base_url}}/products/1

Pastikan produk masih memiliki stok sebelum dimasukkan ke cart.

⸻

5. Tambahkan Produk ke Cart

POST {{base_url}}/cart

Body:

{
  "product_id": 1,
  "quantity": 2
}

Simpan id cart item jika ingin melakukan update atau delete.

⸻

6. Cek Cart

GET {{base_url}}/cart

Response akan menampilkan item yang ada di cart dan total harga sementara.

⸻

7. Checkout

POST {{base_url}}/orders/checkout

Jika checkout berhasil:

* order dibuat;
* harga produk disimpan sebagai snapshot;
* stok produk berkurang;
* cart dikosongkan;
* ID order dikembalikan melalui response.

⸻

8. Cek Order

Lihat seluruh order user:

GET {{base_url}}/orders

Kemudian gunakan ID order untuk melihat detail:

GET {{base_url}}/orders/{{order_id}}

Detail order berisi item yang dibeli, quantity, dan harga pada saat checkout.

⸻

Authentication Notes

Endpoint private menggunakan Laravel Sanctum Personal Access Token.

Token dikirim melalui HTTP Bearer Authentication:

Authorization: Bearer {{token}}

Untuk testing di Postman, token dapat disimpan sebagai environment variable sehingga tidak perlu copy-paste token secara manual pada setiap request.
