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
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_id');
            $table->string('name');
            $table->string('host');
            $table->string('path')->nullable();
            $table->integer('port');
            $table->string('protocol');
            $table->integer('connect_timeout');
            $table->integer('read_timeout');
            $table->integer('write_timeout');
            $table->integer('retries');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
