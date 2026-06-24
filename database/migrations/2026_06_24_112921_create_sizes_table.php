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
        Schema::create('sizes', function (Blueprint $table) { 
            $table->string('name');
            $table->string("width");
            $table->string("height");
            $table->foreignId('product_id')->constrained("products")->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger('price');
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};
