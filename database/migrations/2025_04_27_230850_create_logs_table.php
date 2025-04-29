<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('consumer_id')->constrained('consumers');
            $table->foreignUuid('service_id')->constrained('services');
            $table->foreignUuid('route_id')->constrained('routes');
            
            $table->string('request_method');
            $table->string('request_uri');
            $table->string('request_url');
            $table->integer('request_size');
            $table->json('request_querystring')->nullable();
            $table->json('request_headers');
            
            $table->integer('response_status');
            $table->integer('response_size');
            $table->json('response_headers');
            
            $table->integer('proxy_latency');
            $table->integer('gateway_latency');
            $table->integer('request_latency');
            
            $table->string('client_ip');
            $table->timestamp('started_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
