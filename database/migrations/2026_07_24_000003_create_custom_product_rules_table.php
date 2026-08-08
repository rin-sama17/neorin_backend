<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_product_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_item_id')->constrained('custom_product_items')->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('action', ['show', 'hide', 'require', 'set_value'])->default('show');

            $table->foreignId('condition_attribute_id')->constrained('category_attributes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('condition_operator', ['equals', 'not_equals', 'contains', 'greater_than', 'less_than'])->default('equals');
            $table->text('condition_value')->nullable();

            $table->foreignId('target_attribute_id')->nullable()->constrained('category_attributes')->nullOnDelete()->cascadeOnUpdate();
            $table->text('target_value')->nullable();

            $table->unsignedInteger('priority')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_product_rules');
    }
};
