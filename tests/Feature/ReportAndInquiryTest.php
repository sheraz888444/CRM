<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAndInquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_reports_print_view_renders_successfully(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
            'username' => 'admin_test',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.print'));
        $response->assertStatus(200);
        $response->assertSeeText('Student Inquiry & Admission', false);
    }

    public function test_student_inquiry_fails_without_area(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
            'username' => 'staff_test',
        ]);

        $response = $this->actingAs($staff)->post(route('students.store'), [
            'student_name' => 'Ahmad Raza',
            'phone_number' => '03001234567',
            'status' => 'New',
        ]);

        $response->assertSessionHasErrors(['city']);
        $this->assertEquals(0, Student::count());
    }

    public function test_student_inquiry_succeeds_with_area(): void
    {
        $branch = Branch::create(['name' => 'Lahore Campus', 'code' => 'LHR', 'status' => 'active']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'staff_test2',
        ]);

        $response = $this->actingAs($staff)->post(route('students.store'), [
            'student_name' => 'Ahmad Raza',
            'phone_number' => '03001234567',
            'city' => 'Gulberg III, Lahore',
            'status' => 'New',
            'branch_id' => $branch->id,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('students', [
            'student_name' => 'Ahmad Raza',
            'city' => 'Gulberg III, Lahore',
        ]);

        $student = Student::first();
        $this->assertEquals('Gulberg III, Lahore', $student->area);
        $this->assertEquals('Gulberg III, Lahore', $student->city);
    }

    public function test_admin_reports_filters_by_payment_mode_and_shows_staff_stats(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'username' => 'admin_rep']);
        $branch = Branch::create(['name' => 'Islamabad Campus', 'code' => 'ISB', 'status' => 'active']);
        $staff = User::factory()->create([
            'name' => 'Maria Tariq',
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'maria',
        ]);

        $student = Student::create([
            'student_name' => 'Hamza Ali',
            'phone_number' => '03335556677',
            'city' => 'G-11, Islamabad',
            'status' => 'Admitted',
            'branch_id' => $branch->id,
            'created_by' => $staff->id,
            'class_applying_for' => 'Web Development',
        ]);

        \App\Models\Admission::create([
            'roll_no' => 'ISB-0001',
            'inquiry_id' => $student->id,
            'branch_id' => $branch->id,
            'created_by' => $staff->id,
            'name' => 'Hamza Ali',
            'phone' => '03335556677',
            'total_package' => 40000,
            'discount' => 5000,
            'net_payable' => 35000,
            'payment_type' => 'lump_sum',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['payment_type' => 'lump_sum']));
        $response->assertStatus(200);
        $response->assertSee('Maria Tariq');
        $response->assertSee('Hamza Ali');
        $response->assertSee('G-11, Islamabad');
        $response->assertSee('Full Paid (Lump-Sum)');
        $response->assertSee(route('admin.reports.staff', $staff));
    }

    public function test_admin_can_view_staff_specific_report_and_print_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'username' => 'admin_rep2']);
        $branch = Branch::create(['name' => 'Rawalpindi Campus', 'code' => 'RWP', 'status' => 'active']);
        $staff = User::factory()->create([
            'name' => 'Sarah Khan',
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'sarah_k',
        ]);

        $student = Student::create([
            'student_name' => 'Bilal Ahmed',
            'phone_number' => '03129876543',
            'city' => 'Saddar, Rawalpindi',
            'status' => 'New',
            'branch_id' => $branch->id,
            'created_by' => $staff->id,
            'class_applying_for' => 'Graphic Designing',
        ]);

        // 1. Staff Report View
        $response = $this->actingAs($admin)->get(route('admin.reports.staff', $staff));
        $response->assertStatus(200);
        $response->assertSee('Sarah Khan');
        $response->assertSee('Bilal Ahmed');
        $response->assertSee('Saddar, Rawalpindi');
        $response->assertSee('Graphic Designing');
        $response->assertSee(route('admin.reports.staff.print', $staff));

        // 2. Staff Report Print / PDF View
        $printResponse = $this->actingAs($admin)->get(route('admin.reports.staff.print', $staff));
        $printResponse->assertStatus(200);
        $printResponse->assertSee('Sarah Khan');
        $printResponse->assertSee('Bilal Ahmed');
        $printResponse->assertSee('Counselor Signature');
        $printResponse->assertSee('window.print()', false);

        // 3. Staff CSV export
        $csvResponse = $this->actingAs($admin)->get(route('admin.reports.staff.csv', $staff));
        $csvResponse->assertStatus(200);
        $csvResponse->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_staff_can_delete_student_inquiry(): void
    {
        $branch = Branch::create(['name' => 'Gujranwala Campus', 'code' => 'GRW', 'status' => 'active']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'staff_grw',
        ]);

        $student = Student::create([
            'student_name' => 'Ali Raza',
            'phone_number' => '03009988776',
            'class_applying_for' => 'Web Development',
            'city' => 'Model Town, Gujranwala',
            'status' => 'New',
            'branch_id' => $branch->id,
            'created_by' => $staff->id,
        ]);

        $response = $this->actingAs($staff)->delete(route('students.destroy', $student));
        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        // Verify soft-deleted
        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_staff_can_delete_admission(): void
    {
        $branch = Branch::create(['name' => 'Sialkot Campus', 'code' => 'SKT', 'status' => 'active']);
        $staff = User::factory()->create([
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'staff_skt',
        ]);

        $admission = \App\Models\Admission::create([
            'roll_no' => 'SKT-0001',
            'branch_id' => $branch->id,
            'created_by' => $staff->id,
            'name' => 'Kamran Akmal',
            'phone' => '03004433221',
            'total_package' => 20000,
            'discount' => 0,
            'net_payable' => 20000,
            'payment_type' => 'lump_sum',
            'status' => 'active',
        ]);

        $response = $this->actingAs($staff)->delete(route('admissions.destroy', $admission));
        $response->assertRedirect(route('admissions.index'));
        $response->assertSessionHas('success');

        // Verify soft-deleted
        $this->assertSoftDeleted('admissions', ['id' => $admission->id]);
    }
}
