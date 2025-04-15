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
            $table->text('product_type')->nullable()->comment('نوع محصول:بازی');
            $table->text('product_status')->default('as_good_as_new')->comment('در حد نو');
            $table->foreignId('category_id')->nullable()->constrained("categories")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId('user_id')->nullable()->constrained("users")->onDelete("cascade")->onUpdate("cascade");
            $table->foreignId('city_id')->nullable()->constrained("cities");
            $table->tinyInteger("status")->default(1)->comment("1=>enable , 0=>disable ,3=>pending");
            $table->dateTime('published_at')->nullable();
            $table->dateTime('expierd_at')->nullable();
            $table->unsignedBigInteger('view')->default(0);
            $table->string('contact')->nullable();
            $table->tinyInteger('is_special')->default(0);
            $table->tinyInteger('is_ladder')->default(0);
            $table->text('image')->nullable();
            $table->text('slug')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->text('tags')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->tinyInteger('willing_to_trade')->default(0);
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
