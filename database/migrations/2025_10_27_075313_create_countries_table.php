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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code', 2)->unique(); // ISO 3166-1 alpha-2 code (e.g., NL, BE, DE)
            $table->string('iso3_code', 3)->nullable(); // ISO 3166-1 alpha-3 code (e.g., NLD, BEL, DEU)
            $table->string('phone_code', 10)->nullable(); // e.g., +31
            $table->boolean('is_active')->default(false); // Only active countries are shown
            $table->timestamps();

            $table->index('iso_code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
