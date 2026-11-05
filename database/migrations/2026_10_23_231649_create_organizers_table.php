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
    {Schema::create('organizers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('logo')->nullable();
    $table->string('phone')->nullable();
    $table->string('payout_operator')->nullable(); // MTN, Moov...
    $table->string('payout_phone')->nullable();
    $table->unsignedTinyInteger('commission_rate')->default(10);
    $table->string('status')->default('pending'); // pending, approved, suspended
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizers');
    }
};
