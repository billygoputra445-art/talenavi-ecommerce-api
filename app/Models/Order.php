<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public static function checkout(User $user): self
    {
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            throw new InvalidArgumentException('Cart is empty. Please add products first.');
        }

        return DB::transaction(function () use ($user, $cartItems) {
            $totalPrice = 0;
            $itemsToCreate = [];

            // Validasi stok produk dan kumpulkan data pesanan dengan row locking
            foreach ($cartItems as $item) {
                // Lock row produk di database
                $product = Product::where('id', $item->product_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock < $item->quantity) {
                    throw new InvalidArgumentException("Insufficient stock for product '{$product->title}'. Available stock: {$product->stock}");
                }

                $totalPrice += $product->price * $item->quantity;

                $itemsToCreate[] = [
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ];
            }

            $order = self::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'paid',
            ]);

            foreach ($itemsToCreate as $data) {
                $order->items()->create([
                    'product_id' => $data['product']->id,
                    'quantity' => $data['quantity'],
                    'price' => $data['price'],
                ]);

                $data['product']->decrement('stock', $data['quantity']);
            }

            $user->cartItems()->delete();

            return $order->load('items.product');
        });
    }
}
