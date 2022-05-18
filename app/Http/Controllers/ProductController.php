<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductPicture;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $user = auth()->user();
        return Product::with('picture', 'cart:id,username')->where('shop_id', $user->shop->id)->get();
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:shops',
            'description' => 'required|min:5',
            'amount' => 'required|integer',
            'price' => 'required|integer',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        Product::Create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'price' => $request->price,
            'category' => $request->category,
            'shop_id' => $user->shop->id,
        ]);

        return response()->json(['message' => 'Produk berhasil dibuat']);
    }

    public function picture(Request $request, $id)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'picture' => 'mimes:jpeg,png,svg,jpg',
        ]);

        $imgName = null;

        if($request->file('picture')){
            $imgName = $request->file('picture')->getClientOriginalName() . '-' . time() . '.' . $request->file('picture')->extension();
            $request->file('picture')->move(public_path('picture'), $imgName);
        }

        ProductPicture::Create([
            'picture' => $imgName,
            'product_id' => $id,
        ]);

        return response()->json(['message' => 'Gambar berhasil diupload']);
    }

    public function show($id)
    {
        return Product::with('picture')->find($id);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $product = Product::find($id);
        if($request->name == $product->name){
            $nameRules = 'required';
        }
        else{
            $nameRules = 'required|unique:shops';
        }
        $validator = Validator::make($request->all(), [
            'name' => $nameRules,
            'description' => 'required|min:5',
            'amount' => 'required|integer',
            'price' => 'required|integer',
            'category' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'price' => $request->price,
            'category' => $request->category,
        ]);

        return response()->json(['message' => 'Produk berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Product::Find($id)->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    public function destroyImage($id)
    {
        ProductPicture::Find($id)->delete();
        return response()->json(['message' => 'Gambar berhasil dihapus']);
    }

    public function searchProduct(Request $request)
    {
        return Product::with('picture', 'shop:id,address')->where([
            ['name', 'like', '%'.$request->name.'%'], ])->get();
    }
}
