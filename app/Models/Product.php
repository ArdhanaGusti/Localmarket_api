<?php

namespace App\Models;

use App\Models\Shop;
use App\Models\User;
use App\Models\Transaction;
use App\Models\ProductPicture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function shop(){
        return $this->belongsTo(Shop::class);
    }
    public function picture(){
        return $this->hasMany(ProductPicture::class);
    }
    public function cart(){
        return $this->belongsToMany(User::class, 'carts');
    }
    public function transaction(){
        return $this->hasMany(Transaction::class);
    }
}
