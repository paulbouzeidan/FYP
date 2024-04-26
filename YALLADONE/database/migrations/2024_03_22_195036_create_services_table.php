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
            $table->id('service_id');
            $table->timestamps();

            $table->string('category')->nullable();
            $table->integer('price')->nullable();
            $table->string('service_name')->nullable()->unique();
            $table->string('service_description')->nullable();
            $table->boolean('isEmergency')->default(false);
            $table->boolean('IsFav')->default(false);

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
