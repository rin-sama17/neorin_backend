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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('state_id')
                ->constrained('states')
                ->restrictOnDelete();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();

            $table->string('address');           // متن آدرس
            $table->string('plaque');             // پلاک
            $table->string('unit')->nullable();   // واحد (اختیاری)
            $table->string('postal_code', 10);    // کد پستی

            $table->string('title')->nullable();  // مثلاً «منزل» / «محل کار» — پیشنهادی
            $table->boolean('is_default')->default(false); // پیشنهادی
            $table->index('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
