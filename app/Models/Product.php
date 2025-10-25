<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'productId';
    protected $fillable = [
        'sellerId',
        'sellerNumber',
        'productCategory',
        'productTitle',
        'productDetails',
        'productImage',
        'productImageNumber',
        'productPrice',
        'productDateTime',
        'productStatus',
        'productStatusRatio',
        'productAmount',
        'productCurrency',
        'productCountry',
        'productCity'
    ];
    // public function seller()
    // {
    //     return $this->belongsTo(User::class, 'sellerId');
    // }
    // في Product.php
    public function seller()
    {
        return $this->belongsTo(User::class, 'sellerId', 'id');
    }
}
