<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Log extends Model
{
    use HasFactory;
    protected $fillable = [
        'consumer_id',
        'service_id',
        'route_id',
        'request_method',
        'request_uri',
        'request_url',
        'request_size',
        'proxy_latency',
        'gateway_latency',
        'request_latency',
        'request_querystring',
        'request_headers',
        'response_status',
        'response_size',
        'response_headers',
        'client_ip',
        'started_at'
    ];

    protected $casts = [
        'request_querystring' => 'array',
        'request_headers' => 'array',
        'response_headers' => 'array',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
