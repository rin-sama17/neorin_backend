<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_attributes', function (Blueprint $table) {
            $table->decimal('min_value', 10, 2)->nullable()->after('sort');
            $table->decimal('max_value', 10, 2)->nullable()->after('min_value');
            $table->decimal('default_value', 10, 2)->nullable()->after('max_value');
        });
    }

    public function down(): void
    {
        Schema::table('category_attributes', function (Blueprint $table) {
            $table->dropColumn(['min_value', 'max_value', 'default_value']);
        });
    }
};
