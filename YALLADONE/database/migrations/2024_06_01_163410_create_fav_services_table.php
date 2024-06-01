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
        Schema::create('fav_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('Users_id')->on('users')->onDelete('cascade');
            $table->foreignId('service_idF')->references('service_id')->on('users')->onDelete('cascade');
            $table->boolean('IsFav')->default(false);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fav_services');
    }
};
