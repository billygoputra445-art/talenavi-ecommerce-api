<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderController extends Controller
{

    //Memproses checkout dari keranjang belanja user.
    //Mengembalikan HTTP 201 Created jika sukses, atau 400 Bad Request jika stok kurang/keranjang kosong.

    public function checkout(Request $request): JsonResponse
    {
        try {
            $order = Order::checkout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Checkout completed successfully',
                'data' => $order,
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


     // Menampilkan riwayat transaksi pesanan milik user yang sedang login.

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Order history retrieved successfully',
            'data' => $orders,
        ]);
    }


    // Menampilkan detail pesanan berdasarkan ID.
    // Mencegah akses ke order milik user lain (HTTP 403 Forbidden).

    public function show(Request $request, Order $order): JsonResponse
    {
        // Otorisasi kepemilikan data order
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not own this order',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order details retrieved successfully',
            'data' => $order->load('items.product'),
        ]);
    }
}
