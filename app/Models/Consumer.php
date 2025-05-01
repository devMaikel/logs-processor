<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;
    protected $fillable = [
        'id'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
