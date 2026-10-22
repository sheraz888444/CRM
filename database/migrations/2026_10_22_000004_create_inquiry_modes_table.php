<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create inquiry_modes master table.
     */
    public function up(): void
    {
        Schema::create('inquiry_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // Call, WhatsApp, Social Media, Visit
            $table->string('icon')->nullable(); // optional icon class/name
            $table->string('status')->default('active')->index();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_modes');
    }
};
