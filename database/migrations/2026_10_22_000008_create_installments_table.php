<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create installments table.
     */
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->integer('seq');             // 1, 2, 3, ...
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->string('status')->default('pending')->index(); // pending, partial, paid, overdue
            $table->timestamps();

            $table->index(['admission_id', 'seq']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
