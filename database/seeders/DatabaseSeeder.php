<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Admission;
use App\Models\Branch;
use App\Models\CallLog;
use App\Models\Course;
use App\Models\InquiryMode;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $today = Carbon::today();

        // ── 1. Settings ──
        Setting::set('school_name', 'Global Institute of Languages & Exam Prep');
        Setting::set('school_phone', '+92 42 35780000');
        Setting::set('school_email', 'admissions@globalinstitute.edu.pk');
        Setting::set('school_address', 'Main Boulevard, Gulberg III, Lahore, Pakistan');
        Setting::set('classes', implode(', ', [
            'IELTS Academic',
            'IELTS General Training',
            'PTE Academic',
            'German Language (A1-B2)',
            'Korean Language (EPS-TOPIK)',
            'Spoken English & Communication',
            'Study Visa Consultancy',
        ]));
        Setting::set('sources', implode(', ', [
            'Phone Call',
            'WhatsApp',
            'Walk-in / Campus Visit',
            'Facebook Ad',
            'Instagram',
            'TikTok',
            'Friend Referral',
            'Google Search',
        ]));

        // ── 2. Branches ──
        $b1 = Branch::firstOrCreate(['code' => 'B1'], [
            'name' => 'Main Campus - Lahore (Gulberg)',
            'phone' => '042-35781111',
            'email' => 'lahore@globalinstitute.edu.pk',
            'address' => 'Plot 45-B, Main Boulevard, Gulberg III, Lahore',
            'status' => 'active',
        ]);

        $b2 = Branch::firstOrCreate(['code' => 'B2'], [
            'name' => 'Islamabad Campus (Blue Area)',
            'phone' => '051-2892222',
            'email' => 'islamabad@globalinstitute.edu.pk',
            'address' => 'Executive Heights, Sector F-6 / Blue Area, Islamabad',
            'status' => 'active',
        ]);

        $b3 = Branch::firstOrCreate(['code' => 'B3'], [
            'name' => 'Rawalpindi Campus (Saddar)',
            'phone' => '051-5563333',
            'email' => 'pindi@globalinstitute.edu.pk',
            'address' => 'Bank Road, Commercial Zone, Saddar, Rawalpindi',
            'status' => 'active',
        ]);

        // ── 3. Courses ──
        $cIelts = Course::firstOrCreate(['code' => 'IELTS'], [
            'name' => 'IELTS Academic & General',
            'default_fee' => 25000,
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $cPte = Course::firstOrCreate(['code' => 'PTE'], [
            'name' => 'PTE Academic Preparation',
            'default_fee' => 28000,
            'status' => 'active',
            'sort_order' => 2,
        ]);

        $cGerman = Course::firstOrCreate(['code' => 'GERMAN'], [
            'name' => 'German Language (A1 to B2)',
            'default_fee' => 35000,
            'status' => 'active',
            'sort_order' => 3,
        ]);

        $cKorean = Course::firstOrCreate(['code' => 'KOREAN'], [
            'name' => 'Korean Language (EPS-TOPIK)',
            'default_fee' => 30000,
            'status' => 'active',
            'sort_order' => 4,
        ]);

        $cEnglish = Course::firstOrCreate(['code' => 'ENG'], [
            'name' => 'Spoken English & Communication Skills',
            'default_fee' => 18000,
            'status' => 'active',
            'sort_order' => 5,
        ]);

        $cVisa = Course::firstOrCreate(['code' => 'VISA'], [
            'name' => 'Study Visa Consultancy & Guidance',
            'default_fee' => 50000,
            'status' => 'active',
            'sort_order' => 6,
        ]);

        // ── 4. Inquiry Modes ──
        $modeCall = InquiryMode::firstOrCreate(['name' => 'Phone Call'], ['icon' => 'phone', 'status' => 'active']);
        $modeWa = InquiryMode::firstOrCreate(['name' => 'WhatsApp'], ['icon' => 'chat', 'status' => 'active']);
        $modeSocial = InquiryMode::firstOrCreate(['name' => 'Social Media'], ['icon' => 'globe', 'status' => 'active']);
        $modeVisit = InquiryMode::firstOrCreate(['name' => 'Campus Visit'], ['icon' => 'building', 'status' => 'active']);

        // ── 5. Users & Roles ──
        $superAdmin = User::updateOrCreate(['email' => 'superadmin@demo.com'], [
            'name' => 'Director Tariq Malik',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'phone' => '0300-9998888',
            'branch_id' => null, // Super Admin sees all branches
        ]);

        $adminOld = User::updateOrCreate(['email' => 'admin@demo.com'], [
            'name' => 'Muhammad Bilal (Admin)',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '0300-1112233',
            'branch_id' => $b1->id,
        ]);

        $b1Admin = User::updateOrCreate(['email' => 'b1admin@demo.com'], [
            'name' => 'Hamza Tariq (Lahore Head)',
            'username' => 'b1admin',
            'password' => Hash::make('password'),
            'role' => 'branch_admin',
            'status' => 'active',
            'phone' => '0300-4441111',
            'branch_id' => $b1->id,
        ]);

        $b1Staff = User::updateOrCreate(['email' => 'b1staff@demo.com'], [
            'name' => 'Ayesha Khan (Counselor - LHR)',
            'username' => 'b1staff',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'phone' => '0321-4442222',
            'branch_id' => $b1->id,
        ]);

        $b2Admin = User::updateOrCreate(['email' => 'b2admin@demo.com'], [
            'name' => 'Farhan Qureshi (Islamabad Head)',
            'username' => 'b2admin',
            'password' => Hash::make('password'),
            'role' => 'branch_admin',
            'status' => 'active',
            'phone' => '0300-5551111',
            'branch_id' => $b2->id,
        ]);

        $b2Staff = User::updateOrCreate(['email' => 'b2staff@demo.com'], [
            'name' => 'Zainab Bibi (Counselor - ISB)',
            'username' => 'b2staff',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'phone' => '0333-5552222',
            'branch_id' => $b2->id,
        ]);

        // Also ensure Sarah, Ahmed, Zainab demo accounts exist and are linked to branches
        $sarah = User::updateOrCreate(['email' => 'sarah@demo.com'], [
            'name' => 'Sarah Khan (Staff)',
            'username' => 'sarah',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'phone' => '0321-1234567',
            'branch_id' => $b1->id,
        ]);

        $ahmed = User::updateOrCreate(['email' => 'ahmed@demo.com'], [
            'name' => 'Ahmed Ali (Staff)',
            'username' => 'ahmed',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'phone' => '0321-2345678',
            'branch_id' => $b1->id,
        ]);

        $zainab = User::updateOrCreate(['email' => 'zainab@demo.com'], [
            'name' => 'Zainab Fatima (Staff)',
            'username' => 'zainab',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'phone' => '0321-3456789',
            'branch_id' => $b2->id,
        ]);

        // ── 6. Student Inquiries ──
        $inquiriesData = [
            [
                'name' => 'Usman Arshad',
                'parent' => 'Arshad Mahmood',
                'phone' => '0300-5551234',
                'branch' => $b1,
                'course' => $cIelts,
                'mode' => $modeWa,
                'status' => 'Follow-up Scheduled',
                'next_followup' => $today->toDateString(),
                'creator' => $b1Staff,
                'notes' => 'Needs 7.0 band in IELTS for Australian university visa. Inquired about evening batches.',
            ],
            [
                'name' => 'Fatima Noor',
                'parent' => 'Noor Muhammad',
                'phone' => '0321-7778899',
                'branch' => $b1,
                'course' => $cGerman,
                'mode' => $modeVisit,
                'status' => 'Visited',
                'next_followup' => $today->copy()->addDay()->toDateString(),
                'creator' => $b1Staff,
                'notes' => 'Visited Gulberg campus. Wants A1-B1 package for Germany Ausbildung program.',
            ],
            [
                'name' => 'Shahzaib Ali',
                'parent' => 'Ali Raza',
                'phone' => '0333-4445566',
                'branch' => $b1,
                'course' => $cKorean,
                'mode' => $modeCall,
                'status' => 'Contacted',
                'next_followup' => $today->copy()->addDays(2)->toDateString(),
                'creator' => $b1Staff,
                'notes' => 'Preparing for EPS-TOPIK Korean manufacturing test. Requested syllabus and morning timings.',
            ],
            [
                'name' => 'Hassan Raza',
                'parent' => 'Raza Hussain',
                'phone' => '0302-8889900',
                'branch' => $b2,
                'course' => $cPte,
                'mode' => $modeSocial,
                'status' => 'Follow-up Scheduled',
                'next_followup' => $today->toDateString(),
                'creator' => $b2Staff,
                'notes' => 'Saw Instagram ad for PTE Academic. Target score 65+. Wants weekend batch in Islamabad.',
            ],
            [
                'name' => 'Mariam Tariq',
                'parent' => 'Tariq Mehmood',
                'phone' => '0345-1122334',
                'branch' => $b2,
                'course' => $cIelts,
                'mode' => $modeVisit,
                'status' => 'Visited',
                'next_followup' => $today->copy()->addDays(3)->toDateString(),
                'creator' => $b2Staff,
                'notes' => 'Visited Blue Area campus with brother. Planning for UK Master degree.',
            ],
            [
                'name' => 'Kamran Khan',
                'parent' => 'Jan Sher Khan',
                'phone' => '0313-9988776',
                'branch' => $b3,
                'course' => $cEnglish,
                'mode' => $modeCall,
                'status' => 'New',
                'next_followup' => $today->toDateString(),
                'creator' => $b2Staff,
                'notes' => 'Inquired for Spoken English 2-month crash course for job interviews.',
            ],
        ];

        foreach ($inquiriesData as $item) {
            $student = Student::firstOrCreate(
                ['phone_number' => $item['phone']],
                [
                    'student_name' => $item['name'],
                    'parent_name' => $item['parent'],
                    'phone_normalized' => Student::normalizePhone($item['phone']),
                    'email' => strtolower(str_replace(' ', '', $item['name'])) . '@gmail.com',
                    'branch_id' => $item['branch']->id,
                    'course_id' => $item['course']->id,
                    'mode_id' => $item['mode']->id,
                    'class_applying_for' => $item['course']->name,
                    'inquiry_source' => $item['mode']->name,
                    'status' => $item['status'],
                    'next_followup_date' => $item['next_followup'],
                    'created_by' => $item['creator']->id,
                    'notes' => $item['notes'],
                ]
            );

            // Call log
            CallLog::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'user_id' => $item['creator']->id,
                    'call_type' => $item['mode']->name === 'Campus Visit' ? 'Walk-in' : 'Incoming',
                    'call_date' => $today->copy()->subDays(2)->toDateString(),
                    'call_time' => '11:30',
                    'duration' => '6 mins',
                    'remarks' => $item['notes'],
                    'next_follow_up_date' => $item['next_followup'],
                    'status_after_call' => $item['status'],
                ]
            );
        }

        // ── 7. Confirmed Admissions & Fee Plans ──

        // Admission 1: Lahore (B1) - Fully Paid Lump Sum
        $adm1 = Admission::firstOrCreate(
            ['roll_no' => 'B1-0001'],
            [
                'branch_id' => $b1->id,
                'created_by' => $b1Staff->id,
                'name' => 'Muhammad Bilal Akhtar',
                'father_name' => 'Akhtar Ali',
                'phone' => '0301-8884422',
                'phone_normalized' => Admission::normalizePhone('0301-8884422'),
                'email' => 'bilal.akhtar@gmail.com',
                'qualification' => 'BS Electrical Engineering',
                'total_package' => 25000,
                'discount' => 2000,
                'net_payable' => 23000,
                'payment_type' => 'lump_sum',
                'status' => 'active',
                'admission_date' => $today->copy()->subDays(10)->toDateString(),
            ]
        );
        $adm1->courses()->sync([$cIelts->id]);

        Payment::firstOrCreate(
            ['admission_id' => $adm1->id, 'amount' => 23000],
            [
                'payment_date' => $today->copy()->subDays(10)->toDateString(),
                'mode' => 'Cash',
                'received_by' => $b1Staff->id,
                'type' => 'payment',
                'remarks' => 'Full lump-sum fee paid at Lahore desk',
            ]
        );

        // Admission 2: Lahore (B1) - Installment Plan (Part 1 Paid, Part 2 Due Today)
        $adm2 = Admission::firstOrCreate(
            ['roll_no' => 'B1-0002'],
            [
                'branch_id' => $b1->id,
                'created_by' => $b1Staff->id,
                'name' => 'Zeeshan Haider',
                'father_name' => 'Haider Zaman',
                'phone' => '0322-9988112',
                'phone_normalized' => Admission::normalizePhone('0322-9988112'),
                'email' => 'zeeshan.h@yahoo.com',
                'qualification' => 'BSc Computer Science',
                'total_package' => 43000, // IELTS (25k) + Spoken English (18k)
                'discount' => 3000,
                'net_payable' => 40000,
                'payment_type' => 'installment',
                'status' => 'active',
                'admission_date' => $today->copy()->subDays(30)->toDateString(),
            ]
        );
        $adm2->courses()->sync([$cIelts->id, $cEnglish->id]);

        $inst2_1 = Installment::firstOrCreate(
            ['admission_id' => $adm2->id, 'seq' => 1],
            [
                'amount' => 20000,
                'due_date' => $today->copy()->subDays(30)->toDateString(),
                'status' => 'paid',
            ]
        );

        Payment::firstOrCreate(
            ['admission_id' => $adm2->id, 'installment_id' => $inst2_1->id],
            [
                'amount' => 20000,
                'payment_date' => $today->copy()->subDays(30)->toDateString(),
                'mode' => 'Bank',
                'received_by' => $b1Staff->id,
                'type' => 'payment',
                'remarks' => 'Online Bank Transfer via Allied Bank',
            ]
        );

        $inst2_2 = Installment::firstOrCreate(
            ['admission_id' => $adm2->id, 'seq' => 2],
            [
                'amount' => 20000,
                'due_date' => $today->toDateString(), // Due today!
                'status' => 'pending',
            ]
        );

        // Admission 3: Lahore (B1) - Installment Plan (OVERDUE ALERT)
        $adm3 = Admission::firstOrCreate(
            ['roll_no' => 'B1-0003'],
            [
                'branch_id' => $b1->id,
                'created_by' => $b1Staff->id,
                'name' => 'Adnan Yousaf',
                'father_name' => 'Yousaf Masih',
                'phone' => '0304-7766554',
                'phone_normalized' => Admission::normalizePhone('0304-7766554'),
                'qualification' => 'B.Com',
                'total_package' => 30000,
                'discount' => 0,
                'net_payable' => 30000,
                'payment_type' => 'installment',
                'status' => 'active',
                'admission_date' => $today->copy()->subDays(45)->toDateString(),
            ]
        );
        $adm3->courses()->sync([$cKorean->id]);

        $inst3_1 = Installment::firstOrCreate(
            ['admission_id' => $adm3->id, 'seq' => 1],
            [
                'amount' => 15000,
                'due_date' => $today->copy()->subDays(45)->toDateString(),
                'status' => 'paid',
            ]
        );

        Payment::firstOrCreate(
            ['admission_id' => $adm3->id, 'installment_id' => $inst3_1->id],
            [
                'amount' => 15000,
                'payment_date' => $today->copy()->subDays(45)->toDateString(),
                'mode' => 'Cash',
                'received_by' => $b1Staff->id,
                'type' => 'payment',
                'remarks' => '1st Installment deposit',
            ]
        );

        $inst3_2 = Installment::firstOrCreate(
            ['admission_id' => $adm3->id, 'seq' => 2],
            [
                'amount' => 15000,
                'due_date' => $today->copy()->subDays(5)->toDateString(), // Overdue 5 days ago!
                'status' => 'overdue',
            ]
        );

        // Admission 4: Islamabad (B2) - German Language (Part 1 Paid, Part 2 Upcoming)
        $adm4 = Admission::firstOrCreate(
            ['roll_no' => 'B2-0001'],
            [
                'branch_id' => $b2->id,
                'created_by' => $b2Staff->id,
                'name' => 'Sarah Jamil',
                'father_name' => 'Jamil Ur Rehman',
                'phone' => '0331-5559988',
                'phone_normalized' => Admission::normalizePhone('0331-5559988'),
                'email' => 'sarah.jamil@hotmail.com',
                'qualification' => 'MBBS Student',
                'total_package' => 35000,
                'discount' => 5000,
                'net_payable' => 30000,
                'payment_type' => 'installment',
                'status' => 'active',
                'admission_date' => $today->copy()->subDays(15)->toDateString(),
            ]
        );
        $adm4->courses()->sync([$cGerman->id]);

        $inst4_1 = Installment::firstOrCreate(
            ['admission_id' => $adm4->id, 'seq' => 1],
            [
                'amount' => 15000,
                'due_date' => $today->copy()->subDays(15)->toDateString(),
                'status' => 'paid',
            ]
        );

        Payment::firstOrCreate(
            ['admission_id' => $adm4->id, 'installment_id' => $inst4_1->id],
            [
                'amount' => 15000,
                'payment_date' => $today->copy()->subDays(15)->toDateString(),
                'mode' => 'Online',
                'received_by' => $b2Staff->id,
                'type' => 'payment',
                'remarks' => 'Online gateway transaction',
            ]
        );

        $inst4_2 = Installment::firstOrCreate(
            ['admission_id' => $adm4->id, 'seq' => 2],
            [
                'amount' => 15000,
                'due_date' => $today->copy()->addDays(15)->toDateString(), // Upcoming
                'status' => 'pending',
            ]
        );

        // Admission 5: Islamabad (B2) - PTE Academic (Fully Paid)
        $adm5 = Admission::firstOrCreate(
            ['roll_no' => 'B2-0002'],
            [
                'branch_id' => $b2->id,
                'created_by' => $b2Staff->id,
                'name' => 'Danish Abbasi',
                'father_name' => 'Zafar Abbasi',
                'phone' => '0346-6677889',
                'phone_normalized' => Admission::normalizePhone('0346-6677889'),
                'total_package' => 28000,
                'discount' => 3000,
                'net_payable' => 25000,
                'payment_type' => 'lump_sum',
                'status' => 'active',
                'admission_date' => $today->copy()->subDays(8)->toDateString(),
            ]
        );
        $adm5->courses()->sync([$cPte->id]);

        Payment::firstOrCreate(
            ['admission_id' => $adm5->id, 'amount' => 25000],
            [
                'payment_date' => $today->copy()->subDays(8)->toDateString(),
                'mode' => 'Cash',
                'received_by' => $b2Staff->id,
                'type' => 'payment',
                'remarks' => 'Full fee cleared in cash at Blue Area desk',
            ]
        );

        // ── 8. Daily Tasks ──
        Task::firstOrCreate(
            ['title' => "Follow-up with Usman Arshad (0300-5551234)"],
            [
                'user_id' => $b1Staff->id,
                'branch_id' => $b1->id,
                'type' => 'followup',
                'due_date' => $today->toDateString(),
                'status' => 'pending',
                'description' => 'Target 7.0 band in IELTS. Discuss batch confirmation and seat availability.',
            ]
        );

        Task::firstOrCreate(
            ['title' => "Fee Installment Due #2: Zeeshan Haider (Roll: B1-0002)"],
            [
                'user_id' => $b1Staff->id,
                'branch_id' => $b1->id,
                'type' => 'installment',
                'due_date' => $today->toDateString(),
                'status' => 'pending',
                'description' => 'Amount: PKR 20,000 | Phone: 0322-9988112 | 2nd Part Due Today',
            ]
        );

        Task::firstOrCreate(
            ['title' => "⚠️ Overdue Installment #2: Adnan Yousaf (Roll: B1-0003)"],
            [
                'user_id' => $b1Staff->id,
                'branch_id' => $b1->id,
                'type' => 'installment',
                'due_date' => $today->copy()->subDays(5)->toDateString(),
                'status' => 'pending',
                'description' => 'Amount: PKR 15,000 | Overdue by 5 days | Call student for urgent settlement',
            ]
        );

        Task::firstOrCreate(
            ['title' => "Follow-up with Hassan Raza (0302-8889900)"],
            [
                'user_id' => $b2Staff->id,
                'branch_id' => $b2->id,
                'type' => 'followup',
                'due_date' => $today->toDateString(),
                'status' => 'pending',
                'description' => 'Inquired about PTE Academic weekend batch in Islamabad.',
            ]
        );

        ActivityLog::record('system_seeded', 'Database seeded with Multi-Branch, Courses, Admissions, and Tasks for Client Demo.');
    }
}
