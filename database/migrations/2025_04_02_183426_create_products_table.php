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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('material');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();   
            $table->foreignId('category_id')->nullable()->constrained("categories")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId('user_id')->nullable()->constrained("users")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId('city_id')->nullable()->constrained("cities");
            $table->tinyInteger("status")->default(1)->comment("1=>enable , 0=>disable ,3=>pending");
            $table->unsignedBigInteger('view')->default(0);
            $table->tinyInteger('is_special')->default(0);
            $table->text('image');
            $table->text('slug')->nullable();
            $table->unsignedBigInteger('price');
            $table->text('tags')->nullable();
            $table->integer('stock')->default(0); 
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
