<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('custom_product_rules');

        Schema::create('calculation_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('strategy_type');
            $table->json('config')->nullable();
            $table->decimal('profit_percent', 5, 2)->default(20);
            $table->decimal('vat_percent', 5, 2)->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_profile_id')->constrained('calculation_profiles')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('formula_type');
            $table->json('config')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('custom_product_items', function (Blueprint $table) {
            $table->foreignId('calculation_profile_id')->nullable()->after('category_id')->constrained('calculation_profiles')->nullOnDelete();
        });

        Schema::create('custom_product_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_product_item_id')->constrained('custom_product_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedBigInteger('parent_rule_id')->nullable();

            $table->string('condition_type')->default('selected_value');
            $table->string('condition_source')->nullable();
            $table->string('condition_operator')->default('equals');
            $table->text('condition_value')->nullable();
            $table->unsignedBigInteger('condition_reference_id')->nullable();

            $table->string('action')->default('show');
            $table->string('target_type')->default('attribute');
            $table->unsignedBigInteger('target_id')->nullable();

            $table->json('payload')->nullable();

            $table->unsignedInteger('priority')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_rule_id')->references('id')->on('custom_product_rules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_product_rules');
        Schema::dropIfExists('formulas');
        Schema::dropIfExists('calculation_profiles');

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
};
