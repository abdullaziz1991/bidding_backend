<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidding extends Model
{
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'product_id',
        'productOwnerId',
        'bidding_details',
        'biddingDays',
        'biddingEndDate'
    ];
}
