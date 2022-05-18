<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductPicture extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public $table = 'productpictures';

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
