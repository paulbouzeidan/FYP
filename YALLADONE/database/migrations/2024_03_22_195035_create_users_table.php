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
        Schema::create('users', function (Blueprint $table) {
            $table->id('Users_id');
            $table->timestamps();
            $table->string('user_name')->nullable();
            $table->string('user_lastname')->nullable();
            $table->string('email')->nullable()->unique();
            $table->date('birthday')->nullable();
            $table->integer('phone_number')->nullable()->unique();
            $table->string('password')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
