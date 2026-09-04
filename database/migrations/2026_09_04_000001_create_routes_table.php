<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('start_point_lat', 10, 7);
            $table->decimal('start_point_lng', 10, 7);
            $table->decimal('end_point_lat', 10, 7);
            $table->decimal('end_point_lng', 10, 7);
            $table->decimal('change_point_lat', 10, 7);
            $table->decimal('change_point_lng', 10, 7);
            $table->json('stops_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
