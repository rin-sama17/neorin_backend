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
        Schema::create('product_category_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('products_id')->constrained("products")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId('category_value_id')->constrained("category_values")->onDelete("cascade")->onUpdate("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_value');
    }
};
