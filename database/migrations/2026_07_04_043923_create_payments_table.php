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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('amount');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->text('authority')->nullable();
            $table->text('ref_id')->nullable();
            $table->text('card_pan')->nullable();
            $table->text('trace_no')->nullable();
            $table->json('gateway_response')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
