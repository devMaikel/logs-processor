<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;
    protected $casts = [
        'hosts' => 'array',
        'methods' => 'array',
        'paths' => 'array',
        'protocols' => 'array',
    ];

    protected $fillable = [
        'id',
        'hosts',
        'methods',
        'paths',
        'preserve_host',
        'protocols',
        'regex_priority',
        'strip_path',
        'created_at',
        'updated_at'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
