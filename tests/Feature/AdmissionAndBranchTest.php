<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Branch;
use App\Models\Course;
use App\Models\InquiryMode;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionAndBranchTest extends TestCase
{
    use RefreshDatabase;

    public function test_branch_generates_correct_auto_roll_number_prefix(): void
    {
        $branch1 = Branch::create([
            'name' => 'Lahore Campus',
            'code' => 'B1',
            'status' => 'active',
        ]);

        $this->assertEquals('B1-0001', $branch1->generateNextRollNo());

        // Create an admission with B1-0001
        Admission::create([
            'roll_no' => 'B1-0001',
            'branch_id' => $branch1->id,
            'name' => 'Ali Khan',
            'phone' => '0300-1111111',
            'total_package' => 25000,
            'discount' => 0,
            'net_payable' => 25000,
            'payment_type' => 'lump_sum',
            'status' => 'active',
        ]);

        $this->assertEquals('B1-0002', $branch1->generateNextRollNo());

        $branch2 = Branch::create([
            'name' => 'Islamabad Campus',
            'code' => 'B2',
            'status' => 'active',
        ]);

        $this->assertEquals('B2-0001', $branch2->generateNextRollNo());
    }

    public function test_admission_creation_and_payment_updates_remaining_balance(): void
    {
        $branch = Branch::create(['name' => 'Lahore', 'code' => 'B1', 'status' => 'active']);
        $user = User::factory()->create(['role' => 'admin', 'branch_id' => $branch->id]);

        $admission = Admission::create([
            'roll_no' => 'B1-0001',
            'branch_id' => $branch->id,
            'created_by' => $user->id,
            'name' => 'Farhan Qadir',
            'phone' => '0321-4455667',
            'total_package' => 30000,
            'discount' => 5000,
            'net_payable' => 25000,
            'payment_type' => 'installment',
            'status' => 'active',
        ]);

        $this->assertEquals(25000, $admission->net_payable);
        $this->assertEquals(0, $admission->total_received);
        $this->assertEquals(25000, $admission->remaining);

        // Record a payment of 10,000
        Payment::create([
            'admission_id' => $admission->id,
            'amount' => 10000,
            'payment_date' => now()->toDateString(),
            'mode' => 'Cash',
            'received_by' => $user->id,
            'type' => 'payment',
        ]);

        $admission->refresh();
        $this->assertEquals(10000, $admission->total_received);
        $this->assertEquals(15000, $admission->remaining);
    }

    public function test_admin_can_create_staff_user_with_branch_allocation(): void
    {
        $branch = Branch::create(['name' => 'Islamabad Campus', 'code' => 'ISB', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'username' => 'adminuser']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Sarah Ahmed',
            'username' => 'sarah',
            'email' => 'sarah@school.edu',
            'phone' => '0300-9876543',
            'role' => 'staff',
            'branch_id' => $branch->id,
            'status' => 'active',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'sarah',
            'email' => 'sarah@school.edu',
            'branch_id' => $branch->id,
            'role' => 'staff',
        ]);

        $createdUser = User::where('username', 'sarah')->first();
        $this->assertEquals($branch->id, $createdUser->branch_id);
        $this->assertEquals('Islamabad Campus', $createdUser->branch->name);
    }

    public function test_admin_can_view_branch_staff_details_page(): void
    {
        $branch = Branch::create(['name' => 'Faisalabad Campus', 'code' => 'FSD', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'username' => 'admin_fsd']);
        $staff1 = User::factory()->create([
            'name' => 'Usman Tariq',
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'usman_fsd',
        ]);
        $staff2 = User::factory()->create([
            'name' => 'Ayesha Noor',
            'role' => 'staff',
            'status' => 'active',
            'branch_id' => $branch->id,
            'username' => 'ayesha_fsd',
        ]);

        // 1. Check admin dashboard renders campus comparison with staff count and link
        $dashResponse = $this->actingAs($admin)->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Faisalabad Campus');
        $dashResponse->assertSee('2 Staff');
        $dashResponse->assertSee(route('admin.branches.show', $branch->id));

        // 2. Check branch staff details page renders with both staff members
        $showResponse = $this->actingAs($admin)->get(route('admin.branches.show', $branch));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Faisalabad Campus');
        $showResponse->assertSee('Usman Tariq');
        $showResponse->assertSee('Ayesha Noor');
        $showResponse->assertSee('2 Active Staff Assigned');
        $showResponse->assertSee(route('admin.reports.staff', $staff1->id));
        $showResponse->assertSee(route('admin.reports.staff', $staff2->id));
    }

    public function test_payment_store_updates_installment_status_successfully(): void
    {
        $branch = Branch::create(['name' => 'Multan Campus', 'code' => 'MUL', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active', 'username' => 'admin_mul']);

        $admission = Admission::create([
            'roll_no' => 'MUL-0001',
            'branch_id' => $branch->id,
            'created_by' => $admin->id,
            'name' => 'Zain Malik',
            'phone' => '03001234567',
            'total_package' => 30000,
            'discount' => 0,
            'net_payable' => 30000,
            'payment_type' => 'installment',
            'status' => 'active',
        ]);

        $inst1 = \App\Models\Installment::create([
            'admission_id' => $admission->id,
            'seq' => 1,
            'amount' => 15000,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
        ]);

        $inst2 = \App\Models\Installment::create([
            'admission_id' => $admission->id,
            'seq' => 2,
            'amount' => 15000,
            'due_date' => now()->addDays(40),
            'status' => 'pending',
        ]);

        // Post payment targeting installment 1
        $response = $this->actingAs($admin)->post(route('payments.store'), [
            'admission_id' => $admission->id,
            'installment_id' => $inst1->id,
            'amount' => 15000,
            'payment_date' => now()->toDateString(),
            'mode' => 'Cash',
            'type' => 'payment',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admissions.show', $admission));

        $inst1->refresh();
        $this->assertEquals('paid', $inst1->status);
        $this->assertEquals(15000, $inst1->paid_amount);

        // Post partial payment without specifying installment_id (should auto-link to inst2)
        $response2 = $this->actingAs($admin)->post(route('payments.store'), [
            'admission_id' => $admission->id,
            'amount' => 5000,
            'payment_date' => now()->toDateString(),
            'mode' => 'Online',
            'type' => 'payment',
        ]);

        $response2->assertSessionHasNoErrors();
        $response2->assertRedirect(route('admissions.show', $admission));

        $inst2->refresh();
        $this->assertEquals('partial', $inst2->status);
        $this->assertEquals(5000, $inst2->paid_amount);

        // Attempt to pay more than the remaining 10,000 for installment 2 (e.g., trying to pay 20,000)
        $response3 = $this->actingAs($admin)->post(route('payments.store'), [
            'admission_id' => $admission->id,
            'installment_id' => $inst2->id,
            'amount' => 20000,
            'payment_date' => now()->toDateString(),
            'mode' => 'Cash',
            'type' => 'payment',
        ]);

        $response3->assertSessionHas('error');
    }
}
