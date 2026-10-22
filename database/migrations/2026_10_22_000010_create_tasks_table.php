<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create tasks table for daily task board (follow-ups + installment alerts).
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('type');             // followup, installment
            $table->unsignedBigInteger('ref_id')->nullable(); // student_id or installment_id
            $table->string('ref_type')->nullable();            // App\Models\Student or App\Models\Installment
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->index();
            $table->string('status')->default('pending')->index(); // pending, completed, rescheduled, cancelled
            $table->date('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
