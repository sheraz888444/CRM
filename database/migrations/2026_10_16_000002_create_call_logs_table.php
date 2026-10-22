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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('call_type')->default('Incoming'); // Incoming, Outgoing, Walk-in, WhatsApp
            $table->date('call_date');
            $table->string('call_time'); // HH:MM or HH:MM:SS
            $table->string('duration')->nullable();
            $table->text('remarks');
            $table->date('next_follow_up_date')->nullable()->index();
            $table->string('status_after_call')->default('Contacted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
