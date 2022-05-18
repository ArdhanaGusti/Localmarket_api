<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required',
            'profile' => 'mimes:jpeg,png,svg'
        ]);
        $imgName = null;

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        if($request->file('profile')){
            $imgName = $request->file('profile')->getClientOriginalName() . '-' . time() . '.' . $request->file('profile')->extension();
            $request->file('profile')->move(public_path('profile'), $imgName);
        }

        User::Create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile' => $imgName,
        ]);

        return response()->json(['message' => 'Berhasil registrasi']);
    }

    public function change(Request $request)
    {
        $user = auth()->user();
        if($request->username == $user->username){
            $usernameRules = 'required';
        }
        else{
            $usernameRules = 'required|unique:users';
        }
        $validator = Validator::make($request->all(), [
            'username' => $usernameRules,
            'profile' => 'mimes:jpeg,png,svg'
        ]);
        $imgName = $user->profile;

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag());
        }

        if($request->file('profile')){
            File::delete(public_path("\profile\\").$imgName);
            $imgName = $request->file('profile')->getClientOriginalName() . '-' . time() . '.' . $request->file('profile')->extension();
            $request->file('profile')->move(public_path('profile'), $imgName);
        }

        $user->update([
            'username' => $request->username,
            'profile' => $imgName,
        ]);

        return response()->json(['message' => 'Berhasil update']);
    }

    public function me()
    {
        $user = User::with('cart', 'transaction.product')->where('id', auth()->user()->id)->get();
        return response()->json($user);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh(true, true));
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}