<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'host',
        'path',
        'port',
        'protocol',
        'connect_timeout',
        'read_timeout',
        'write_timeout',
        'retries',
        'created_at',
        'updated_at'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
