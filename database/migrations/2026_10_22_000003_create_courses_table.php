<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create courses master table.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. IELTS, PTE, German, Korean, Spoken English
            $table->string('code', 20)->nullable(); // short code e.g. "IELTS"
            $table->decimal('default_fee', 10, 2)->default(0); // default package price
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
        Schema::dropIfExists('courses');
    }
};
