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
        Schema::create('unknown_donations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('pending_mpesa_donation_id')->constrained()->cascadeOnDelete();
            $table->string('channel')->nullable();
            $table->string('msisdn')->nullable();
            $table->double('amount')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unknown_donations');
    }
};
