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
        Schema::create('pending_mpesa_donations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('trans_id')->nullable()->unique();
            $table->dateTime('trans_time')->nullable();
            $table->double('amount')->nullable();
            $table->string('msisdn', 12)->nullable();
            $table->string('name')->nullable();
            $table->string('account_no')->nullable();
            $table->string('business_short_code')->nullable();
            $table->string('third_party_trans_id')->nullable()->unique();
            $table->ipAddress('ip')->nullable();
            $table->boolean('status')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_mpesa_donations');
    }
};
