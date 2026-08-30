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
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->nullable();
            $table->tinyInteger('selection_type')->default(0)->comment("0 => admin , 1 => user");

            /*
    0 => select
    1 => text
    2 => number
    3 => boolean
    4 => color
    5 => image
    6 => textarea
    */
            $table->tinyInteger('type')->default(0);
            $table->boolean('is_required')->default(false);
            $table->integer('sort')->default(0);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->timestamps();
            $table->foreignId('category_id')->constrained("categories")->onDelete("cascade")->onUpdate("cascade");
            $table->tinyInteger("status")->default(1)->comment("1=>enable , 0=>disable");
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
    }
};
