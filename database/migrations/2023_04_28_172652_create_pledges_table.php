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
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name');
            $table->string('msisdn');
            $table->string('email');
            $table->double('target_amount');
            $table->dateTime('target_date');
            $table->string('account_no')->default('PL-MSSC');
            $table->integer('frequency'); // 0 - once, 1 - daily, 2 - weekly, 3 - monthly
            $table->double('frequency_amount');
            $table->integer('once_and_monthly_frequency_date')->nullable(); // once & monthly frequency
            $table->string('day_of_the_week')->nullable(); // weekly frequency
            $table->boolean('opt_out')->default(false); // 0-yet to opt out, 1-opted out
            $table->dateTime('last_alert_time')->default(now());
            $table->boolean('alerted')->default(false); // 0 - no, 1 - yes
            $table->boolean('payment_status')->default(false); // 0 - pending, 1 - completed
            $table->integer('stk_count_daily')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};
