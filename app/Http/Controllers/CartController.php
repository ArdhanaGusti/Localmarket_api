<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = auth()->user();
        return Cart::where('user_id', $user->id)->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        Cart::Create([
            'user_id' => $user->id,
            'product_id' => $request->productId,
        ]);

        return response()->json(['message' => 'Masuk ke keranjang']);
    }

    public function destroy($id)
    {
        Cart::Find($id)->delete();
        return response()->json(['message' => 'Dihapus dari keranjang']);
    }
}
