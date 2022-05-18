<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return Transaction::where("user_id", $user->id)->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        Transaction::Create([
            'status' => 'order',
            'user_id' => $user->id,
            'product_id' => $request->productId,
        ]);

        return response()->json(['message' => 'Transaksi dibuat']);
    }

    public function show($id)
    {
        
    }

    public function update(Request $request, $id)
    {
        
    }

    public function destroy($id)
    {
        
    }
}
