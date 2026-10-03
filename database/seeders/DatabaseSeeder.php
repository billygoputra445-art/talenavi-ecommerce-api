<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'test@example.com',
            ],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $categories = [
            'Electronics',
            'Fashion',
            'Food & Beverage',
            'Books',
            'Sports',
        ];

        foreach ($categories as $categoryName) {
            Category::updateOrCreate(
                [
                    'slug' => Str::slug($categoryName),
                ],
                [
                    'name' => $categoryName,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = [
            // Electronics
            [
                'category' => 'Electronics',
                'title' => 'Wireless Mouse',
                'description' => 'Wireless mouse untuk kebutuhan komputer dan laptop.',
                'price' => 75000,
                'stock' => 20,
                'image_url' => null,
            ],
            [
                'category' => 'Electronics',
                'title' => 'Mechanical Keyboard',
                'description' => 'Keyboard mechanical untuk produktivitas dan gaming.',
                'price' => 350000,
                'stock' => 15,
                'image_url' => null,
            ],
            [
                'category' => 'Electronics',
                'title' => 'USB-C Cable',
                'description' => 'Kabel USB-C untuk charging dan transfer data.',
                'price' => 45000,
                'stock' => 30,
                'image_url' => null,
            ],
            [
                'category' => 'Electronics',
                'title' => 'Power Bank 10000mAh',
                'description' => 'Power bank berkapasitas 10000mAh.',
                'price' => 150000,
                'stock' => 18,
                'image_url' => null,
            ],
            [
                'category' => 'Electronics',
                'title' => 'Bluetooth Speaker',
                'description' => 'Speaker Bluetooth portable dengan suara jernih.',
                'price' => 250000,
                'stock' => 12,
                'image_url' => null,
            ],
            [
                'category' => 'Electronics',
                'title' => 'Webcam HD',
                'description' => 'Webcam HD untuk meeting dan video conference.',
                'price' => 200000,
                'stock' => 10,
                'image_url' => null,
            ],

            // Fashion
            [
                'category' => 'Fashion',
                'title' => 'Basic T-Shirt',
                'description' => 'Kaos basic dengan bahan nyaman untuk aktivitas sehari-hari.',
                'price' => 85000,
                'stock' => 25,
                'image_url' => null,
            ],
            [
                'category' => 'Fashion',
                'title' => 'Hoodie',
                'description' => 'Hoodie casual dengan bahan yang nyaman.',
                'price' => 175000,
                'stock' => 15,
                'image_url' => null,
            ],
            [
                'category' => 'Fashion',
                'title' => 'Jeans',
                'description' => 'Celana jeans casual untuk penggunaan sehari-hari.',
                'price' => 250000,
                'stock' => 12,
                'image_url' => null,
            ],
            [
                'category' => 'Fashion',
                'title' => 'Sneakers',
                'description' => 'Sneakers casual untuk berbagai aktivitas.',
                'price' => 450000,
                'stock' => 10,
                'image_url' => null,
            ],
            [
                'category' => 'Fashion',
                'title' => 'Baseball Cap',
                'description' => 'Topi baseball dengan desain casual.',
                'price' => 75000,
                'stock' => 20,
                'image_url' => null,
            ],
            [
                'category' => 'Fashion',
                'title' => 'Backpack',
                'description' => 'Tas ransel untuk sekolah, kuliah, dan aktivitas sehari-hari.',
                'price' => 200000,
                'stock' => 15,
                'image_url' => null,
            ],

            // Food & Beverage
            [
                'category' => 'Food & Beverage',
                'title' => 'Instant Coffee',
                'description' => 'Kopi instan praktis untuk menemani aktivitas sehari-hari.',
                'price' => 35000,
                'stock' => 50,
                'image_url' => null,
            ],
            [
                'category' => 'Food & Beverage',
                'title' => 'Green Tea',
                'description' => 'Teh hijau dengan rasa yang ringan dan menyegarkan.',
                'price' => 30000,
                'stock' => 40,
                'image_url' => null,
            ],
            [
                'category' => 'Food & Beverage',
                'title' => 'Chocolate Bar',
                'description' => 'Cokelat bar sebagai camilan sehari-hari.',
                'price' => 20000,
                'stock' => 60,
                'image_url' => null,
            ],
            [
                'category' => 'Food & Beverage',
                'title' => 'Potato Chips',
                'description' => 'Keripik kentang renyah dengan rasa gurih.',
                'price' => 18000,
                'stock' => 50,
                'image_url' => null,
            ],
            [
                'category' => 'Food & Beverage',
                'title' => 'Mineral Water',
                'description' => 'Air mineral dalam kemasan.',
                'price' => 5000,
                'stock' => 100,
                'image_url' => null,
            ],
            [
                'category' => 'Food & Beverage',
                'title' => 'Orange Juice',
                'description' => 'Minuman jus jeruk yang menyegarkan.',
                'price' => 15000,
                'stock' => 50,
                'image_url' => null,
            ],

            // Books
            [
                'category' => 'Books',
                'title' => 'Laravel Fundamentals',
                'description' => 'Buku dasar-dasar pengembangan aplikasi menggunakan Laravel.',
                'price' => 120000,
                'stock' => 10,
                'image_url' => null,
            ],
            [
                'category' => 'Books',
                'title' => 'PHP Programming Guide',
                'description' => 'Panduan pemrograman PHP untuk pemula hingga tingkat menengah.',
                'price' => 150000,
                'stock' => 8,
                'image_url' => null,
            ],
            [
                'category' => 'Books',
                'title' => 'Database Design',
                'description' => 'Panduan merancang database relasional yang baik.',
                'price' => 135000,
                'stock' => 10,
                'image_url' => null,
            ],
            [
                'category' => 'Books',
                'title' => 'Clean Code',
                'description' => 'Buku tentang praktik menulis kode yang bersih dan mudah dipelihara.',
                'price' => 180000,
                'stock' => 7,
                'image_url' => null,
            ],
            [
                'category' => 'Books',
                'title' => 'Web Development Basics',
                'description' => 'Dasar-dasar pengembangan aplikasi web.',
                'price' => 110000,
                'stock' => 12,
                'image_url' => null,
            ],
            [
                'category' => 'Books',
                'title' => 'Software Engineering',
                'description' => 'Pengantar konsep dan praktik software engineering.',
                'price' => 160000,
                'stock' => 8,
                'image_url' => null,
            ],

            // Sports
            [
                'category' => 'Sports',
                'title' => 'Football',
                'description' => 'Bola sepak untuk latihan dan permainan.',
                'price' => 150000,
                'stock' => 15,
                'image_url' => null,
            ],
            [
                'category' => 'Sports',
                'title' => 'Basketball',
                'description' => 'Bola basket untuk latihan dan permainan.',
                'price' => 175000,
                'stock' => 12,
                'image_url' => null,
            ],
            [
                'category' => 'Sports',
                'title' => 'Tennis Racket',
                'description' => 'Raket tenis untuk latihan dan permainan.',
                'price' => 350000,
                'stock' => 8,
                'image_url' => null,
            ],
            [
                'category' => 'Sports',
                'title' => 'Yoga Mat',
                'description' => 'Matras yoga untuk latihan di rumah maupun studio.',
                'price' => 100000,
                'stock' => 20,
                'image_url' => null,
            ],
            [
                'category' => 'Sports',
                'title' => 'Skipping Rope',
                'description' => 'Tali skipping untuk latihan kardio.',
                'price' => 50000,
                'stock' => 25,
                'image_url' => null,
            ],
            [
                'category' => 'Sports',
                'title' => 'Sports Bottle',
                'description' => 'Botol minum untuk aktivitas olahraga.',
                'price' => 75000,
                'stock' => 30,
                'image_url' => null,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where(
                'slug',
                Str::slug($productData['category'])
            )->firstOrFail();

            Product::updateOrCreate(
                [
                    'title' => $productData['title'],
                    'category_id' => $category->id,
                ],
                [
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image_url' => $productData['image_url'],
                ]
            );
        }
    }
}
