<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(){
        $user = auth()->user();
        return Shop::with('user', 'product')->where('user_id', $user->id)->get();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:shops',
            'description' => 'required|min:5',
            'address' => 'required',
            'avatar' => 'mimes:jpeg,png,svg,jpg',
            'cover' => 'mimes:jpeg,png,svg,jpg',
        ]);
        $avatarName = null;
        $coverName = null;
        $user = auth()->user();

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        if($request->file('avatar')){
            $avatarName = $request->file('avatar')->getClientOriginalName() . '-' . time() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move(public_path('avatar'), $avatarName);
        }

        if($request->file('cover')){
            $coverName = $request->file('cover')->getClientOriginalName() . '-' . time() . '.' . $request->file('cover')->extension();
            $request->file('cover')->move(public_path('cover'), $coverName);
        }

        Shop::Create([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'user_id' => $user->id,
            'avatar' => $avatarName,
            'cover' => $coverName,
        ]);

        return response()->json(['message' => 'Toko berhasil dibuat']);
    }

    public function update(Request $request, $id)
    {
        $shop = Shop::find($id);
        if($request->name == $shop->name){
            $nameRules = 'required';
        }
        else{
            $nameRules = 'required|unique:shops';
        }

        $validator = Validator::make($request->all(), [
            'name' => $nameRules,
            'description' => 'required|min:5',
            'address' => 'required',
            'avatar' => 'mimes:jpeg,png,svg,jpg',
            'cover' => 'mimes:jpeg,png,svg,jpg',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        $avatarName = $shop->avatar;
        $coverName = $shop->cover;
        $user = auth()->user();

        if($request->file('avatar')){
            File::delete(public_path("\avatar\\").$avatarName);
            $avatarName = $request->file('avatar')->getClientOriginalName() . '-' . time() . '.' . $request->file('avatar')->extension();
            $request->file('avatar')->move(public_path('avatar'), $avatarName);
        }

        if($request->file('cover')){
            File::delete(public_path("\cover\\").$coverName);
            $coverName = $request->file('cover')->getClientOriginalName() . '-' . time() . '.' . $request->file('cover')->extension();
            $request->file('cover')->move(public_path('cover'), $coverName);
        }

        $shop->update([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'avatar' => $avatarName,
            'cover' => $coverName,
        ]);

        return response()->json(['message' => 'Toko berhasil diubah']);
    }
}
