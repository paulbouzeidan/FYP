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
        Schema::create('services_forms', function (Blueprint $table) {
            $table->id('form_id');
            $table->timestamps();

            $table->foreignId('user_id')->references('Users_id')->on('users')->onDelete('cascade');
            $table->foreignId('Service_id')->references('service_id')->on('services')->onDelete('cascade');

            $table->datetime('service_date');

            $table->string('user_name');
            $table->string('user_lastname');
            $table->string('email');
            $table->integer('phone_number');
            $table->string('location');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_forms');
    }
};
