<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create admissions table.
     */
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('roll_no')->unique();    // e.g. B1-0001
            $table->foreignId('inquiry_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('phone_normalized')->nullable()->index();
            $table->string('alt_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('qualification')->nullable();
            $table->decimal('total_package', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('net_payable', 12, 2)->default(0);  // total_package - discount
            $table->string('payment_type')->default('lump_sum'); // lump_sum, installment
            $table->string('status')->default('active')->index(); // active, completed, cancelled
            $table->date('admission_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
