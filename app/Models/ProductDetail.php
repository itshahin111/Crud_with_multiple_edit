<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    protected $fillable = ['product_id', 'detail', 'image_path']; // Include 'detail'

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
