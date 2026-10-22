<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add branch_id, course_id, mode_id, phone_normalized, visit_date, next_followup_date to students (inquiries).
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->after('class_applying_for')->constrained('courses')->nullOnDelete();
            $table->foreignId('mode_id')->nullable()->after('inquiry_source')->constrained('inquiry_modes')->nullOnDelete();
            $table->string('phone_normalized')->nullable()->index()->after('phone_number');
            $table->date('visit_date')->nullable()->after('status');
            $table->date('next_followup_date')->nullable()->index()->after('visit_date');
            $table->text('remarks')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['mode_id']);
            $table->dropColumn([
                'branch_id', 'course_id', 'mode_id',
                'phone_normalized', 'visit_date', 'next_followup_date', 'remarks'
            ]);
        });
    }
};
