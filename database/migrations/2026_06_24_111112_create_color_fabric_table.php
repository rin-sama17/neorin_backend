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
        Schema::create('color_fabric', function (Blueprint $table) {
            $table->foreignId('fabric_id')->constrained('fabrics')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('color_id')->constrained('colors')->onDelete('cascade')->onUpdate('cascade');
            $table->id();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('color_fabric');
    }
};
