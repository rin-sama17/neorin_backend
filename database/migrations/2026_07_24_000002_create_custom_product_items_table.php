<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_product_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_id')->constrained('custom_products')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('min_qty')->default(1);
            $table->unsignedInteger('max_qty')->default(1);
            $table->integer('sort')->default(0);
            $table->tinyInteger('status')->default(1)->comment('1=>enable, 0=>disable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_product_items');
    }
};
