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
        Schema::create('routes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('route_id');
            $table->json('hosts')->nullable();
            $table->json('methods');
            $table->json('paths');
            $table->boolean('preserve_host');
            $table->json('protocols');
            $table->integer('regex_priority');
            $table->boolean('strip_path');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
