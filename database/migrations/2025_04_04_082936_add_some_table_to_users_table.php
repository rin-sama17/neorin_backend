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
        Schema::table('users', function (Blueprint $table) {
            $table->string("mobile")->after('email')->nullable();
            $table->foreignId('city_id')->after('email')->nullable()->constrained("cities");
            $table->dateTime('mobile_verified_at')->after('email')->nullable();
            $table->tinyInteger('is_active')->after('email')->default(0);
            $table->tinyInteger('user_type')->after('email')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
