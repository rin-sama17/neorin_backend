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
        Schema::create('fabrics', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('slug')->nullable();
            $table->text('material');
            $table->text('image')->nullable();
            $table->foreignId('category_id')->nullable()->constrained("categories")->onDelete("cascade")->onUpdate("cascade");
            $table->text('price');
            $table->tinyInteger("status")->default(1)->comment("1=>enable , 0=>disable ,3=>pending");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fabrics');
    }
};
