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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->timestamps();

            $table->foreignId('user_id')->references('Users_id')->on('users')->onDelete('cascade');

            $table->foreignId('Payment_id')->references('payment_id')->on('payments')->onDelete('cascade');

            $table->foreignId('Form_id')->references('form_id')->on('services_forms')->onDelete('cascade');


            $table->enum('status', ['waiting', 'inprogress', 'done'])->default('waiting');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
