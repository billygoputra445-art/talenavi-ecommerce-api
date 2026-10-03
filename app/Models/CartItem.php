<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    public static function addOrUpdate(int $userId, int $productId, int $quantity): self
    {
        $product = Product::findOrFail($productId);

        $existing = self::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        $totalQty = $existing ? ($existing->quantity + $quantity) : $quantity;

        // Validasi stok ketersediaan
        if ($totalQty > $product->stock) {
            throw new InvalidArgumentException("Insufficient stock for product '{$product->title}'. Available stock: {$product->stock}");
        }

        if ($existing) {
            $existing->update(['quantity' => $totalQty]);

            return $existing;
        }

        return self::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }


    public function updateQuantity(int $quantity): self
    {
        if ($quantity > $this->product->stock) {
            throw new InvalidArgumentException("Insufficient stock for product '{$this->product->title}'. Available stock: {$this->product->stock}");
        }

        $this->update(['quantity' => $quantity]);

        return $this;
    }
}
