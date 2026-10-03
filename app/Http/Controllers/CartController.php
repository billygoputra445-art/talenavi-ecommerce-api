<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with('product.category')
            ->get();

        $totalPrice = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return response()->json([
            'success' => true,
            'message' => 'Cart items retrieved successfully',
            'data' => [
                'items' => $cartItems,
                'total_price' => (float) $totalPrice,
            ],
        ]);
    }

    public function store(AddCartItemRequest $request): JsonResponse
    {
        try {
            $cartItem = CartItem::addOrUpdate(
                $request->user()->id,
                $request->product_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'data' => $cartItem->load('product.category'),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not own this cart item',
            ], 403);
        }

        try {
            $cartItem->updateQuantity($request->quantity);

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated successfully',
                'data' => $cartItem->load('product.category'),
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You do not own this cart item',
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart item deleted successfully',
        ]);
    }
}
