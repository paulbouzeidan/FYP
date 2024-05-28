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
            $table->string('user_name');
            $table->string('user_lastname');
            $table->string('email')->unique();
            $table->timestamp('is_verified')->nullable()->default(NULL);
            $table->date('birthday');
            $table->integer('phone_number')->unique();
            $table->string('password');
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
