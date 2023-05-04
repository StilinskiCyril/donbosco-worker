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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('pledge_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('channel')->nullable();
            $table->string('trans_id')->nullable();
            $table->dateTime('trans_time')->nullable();
            $table->double('amount')->nullable();
            $table->string('business_short_code')->nullable();
            $table->string('account_no')->nullable()->unique();
            $table->string('third_party_trans_id')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('name')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->double('charges')->nullable();
            $table->double('net')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
