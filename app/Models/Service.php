<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'id',
        'service_id',
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
}
