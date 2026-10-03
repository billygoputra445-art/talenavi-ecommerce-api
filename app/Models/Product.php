<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'price',
        'stock',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        //  Filter Pencarian (q / search)
        $search = $filters['q'] ?? $filters['search'] ?? null;
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        //  Filter Kategori (category_id / category slug)
        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (! empty($filters['category'])) {
            $cat = $filters['category'];
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('slug', $cat)->orWhere('id', $cat);
            });
        }

        // Sorting (sort=price_asc|price_desc|newest atau sort_by dan sort_dir)
        if (! empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $sortBy = in_array($filters['sort_by'] ?? null, ['price', 'created_at', 'title', 'stock'])
                ? $filters['sort_by']
                : 'created_at';

            $sortDir = strtolower($filters['sort_dir'] ?? '') === 'asc' ? 'asc' : 'desc';

            $query->orderBy($sortBy, $sortDir);
        }

        return $query;
    }
}
