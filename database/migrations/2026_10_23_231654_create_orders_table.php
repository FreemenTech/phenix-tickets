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
    {Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('reference')->unique();
    $table->foreignId('event_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('buyer_name');
    $table->string('buyer_email');
    $table->string('buyer_phone');
    $table->unsignedInteger('total');
    $table->unsignedInteger('commission_amount')->default(0);
    $table->unsignedInteger('organizer_amount')->default(0);
    $table->string('status')->default('pending'); // pending, paid, failed, refunded
    $table->string('payment_provider')->nullable();
    $table->string('payment_reference')->nullable()->index();
    $table->dateTime('paid_at')->nullable();
    $table->timestamps();
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
