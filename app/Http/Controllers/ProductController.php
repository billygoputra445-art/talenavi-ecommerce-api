<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //  Menampilkan katalog produk terpaginasi (dengan search, filter kategori, dan sorting).

    public function index(Request $request): JsonResponse
    {
        // 1. Ambil input limit dari query param 'limit' atau 'per_page' (default: 10)
        $limit = (int) $request->input('limit', $request->input('per_page', 10));

        // 2. Terapkan batasan: minimal 1 dan maksimal 50 item per halaman
        if ($limit <= 0) {
            $limit = 10;
        }
        if ($limit > 50) {
            $limit = 50;
        }

        $products = Product::with('category')
            ->filter($request->all())
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => $products->items(),
            'meta' => [
                'page' => $products->currentPage(),
                'limit' => $products->perPage(),
                'total' => $products->total(),
                'total_pages' => $products->lastPage(),
            ],
        ]);
    }


    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Product detail retrieved successfully',
            'data' => $product->load('category'),
        ]);
    }
}
