<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watllet extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->select(['id', 'name' ,'email']);
    }
}
