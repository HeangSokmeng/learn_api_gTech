<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'customer_id', 'description', 'price', 'stock'];
    public function order()
    {
        return $this->belongsTo(Customer::class);
    }
}
