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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('account_no')->unique();
            $table->text('description');
            $table->double('target_amount');
            $table->double('amount_donated')->default(0); // updated in intervals by custom commands
            $table->double('balance')->nullable(); // updated in intervals by custom commands
            $table->integer('total_donors')->default(0); // updated in intervals by custom commands (query unique instances)
            $table->integer('total_donations')->default(0); // updated in intervals by custom commands (query unique instances)
            $table->dateTime('target_date');
            $table->text('message_to_donor');
            $table->text('message_to_treasurer');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
