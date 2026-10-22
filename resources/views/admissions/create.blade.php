@extends('layouts.app')

@section('title', 'New Student Admission - EduTrack CRM')
@section('page_title', 'Admission Registration')
@section('page_subtitle', 'Register student admission, select course package, and configure fee installment schedule')

@section('content')
<div class="max-w-4xl mx-auto" x-data="admissionForm()">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">New Admission & Enrollment</h3>
                @if($prefillStudent)
                    <div class="inline-flex items-center gap-2 mt-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Converted from Inquiry #{{ $prefillStudent->id }} ({{ $prefillStudent->student_name }})</span>
                    </div>
                @else
                    <p class="text-xs text-slate-500">Auto-generates official branch Roll Number and sets up payment schedules</p>
                @endif
            </div>

            <a href="{{ route('admissions.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back</span>
            </a>
        </div>

        <form action="{{ route('admissions.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
            @csrf

            @if($prefillStudent)
                <input type="hidden" name="inquiry_id" value="{{ $prefillStudent->id }}">
            @endif

            <!-- ── SECTION 1: Campus & Student Particulars ── -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">1</span>
                    Campus & Student Details
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Branch -->
                    <div>
                        <label for="branch_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Branch Campus <span class="text-rose-500">*</span></label>
                        @if($user->isSuperAdmin() || $user->role === 'admin')
                            <select name="branch_id" id="branch_id" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ (old('branch_id', $prefillStudent?->branch_id ?? $user->branch_id) == $b->id) ? 'selected' : '' }}>
                                        {{ $b->name }} (Prefix: {{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="hidden" name="branch_id" value="{{ $user->branch_id }}">
                            <div class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold text-slate-700">
                                {{ $user->branch?->name ?? 'Default Branch' }} ({{ $user->branch?->code ?? 'B1' }})
                            </div>
                        @endif
                    </div>

                    <!-- Admission Date -->
                    <div>
                        <label for="admission_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Admission Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>

                    <!-- Student Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Student Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $prefillStudent?->student_name ?? request('name')) }}" required
                               placeholder="e.g. Ali Ahmed"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>

                    <!-- Father / Guardian Name -->
                    <div>
                        <label for="father_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Father / Guardian Name</label>
                        <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $prefillStudent?->parent_name ?? request('father_name')) }}"
                               placeholder="e.g. Muhammad Ahmed"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Primary Phone / WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $prefillStudent?->phone_number ?? request('phone')) }}" required
                               placeholder="0300-1234567"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none font-mono">
                    </div>

                    <!-- Alt Phone -->
                    <div>
                        <label for="alt_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Alternate Phone</label>
                        <input type="text" name="alt_phone" id="alt_phone" value="{{ old('alt_phone', $prefillStudent?->alternate_phone ?? request('alt_phone')) }}"
                               placeholder="0321-9876543"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none font-mono">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $prefillStudent?->email ?? request('email')) }}"
                               placeholder="student@example.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>

                    <!-- Qualification -->
                    <div>
                        <label for="qualification" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Highest Qualification</label>
                        <input type="text" name="qualification" id="qualification" value="{{ old('qualification') }}"
                               placeholder="e.g. Intermediate, BS Computer Science, Matric"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>

                    <!-- Address -->
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Residential Address / Area</label>
                        <textarea name="address" id="address" rows="2"
                                  placeholder="House/Street address, area, locality..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">{{ old('address', ($prefillStudent?->address ?: $prefillStudent?->city) ?? request('address') ?? request('city') ?? request('area')) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 2: Course Selection & Package Calculation ── -->
            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">2</span>
                    Enrolled Course(s) & Fee Package
                </h4>

                <div class="space-y-4">
                    <p class="text-xs text-slate-500">Select one or more courses to enroll. Standard fees are automatically added to the total package.</p>

                    <!-- Course Checkboxes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($courses as $c)
                            <label class="flex items-start gap-3 p-3.5 rounded-2xl border transition-all cursor-pointer select-none"
                                   :class="selectedCourses.includes({{ $c->id }}) ? 'border-blue-600 bg-blue-50/50 shadow-xs ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <input type="checkbox" name="courses[]" value="{{ $c->id }}"
                                       @change="toggleCourse({{ $c->id }}, {{ $c->default_fee }})"
                                       :checked="selectedCourses.includes({{ $c->id }})"
                                       class="mt-1 w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <div class="flex-1">
                                    <div class="text-sm font-bold text-slate-900">{{ $c->name }}</div>
                                    <div class="text-xs font-semibold text-blue-600 mt-0.5">PKR {{ number_format($c->default_fee) }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- Fee Summary Box -->
                    <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Total Package Fee (PKR)</label>
                            <input type="number" name="total_package" x-model.number="totalPackage" required min="0" step="any"
                                   @input="recalculate()"
                                   class="w-full font-bold px-4 py-2 rounded-xl border border-slate-200 bg-white text-base text-slate-900 outline-none focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Special Discount (PKR)</label>
                            <input type="number" name="discount" x-model.number="discount" min="0" step="any"
                                   @input="recalculate()"
                                   placeholder="0"
                                   class="w-full font-bold px-4 py-2 rounded-xl border border-slate-200 bg-white text-base text-slate-900 outline-none focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Net Payable Fee (PKR)</label>
                            <div class="w-full font-extrabold px-4 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-base text-emerald-700"
                                 x-text="'PKR ' + netPayable.toLocaleString()">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION 3: Payment Plan & Installments ── -->
            <div class="pt-6 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-4 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">3</span>
                    Payment Type & Installment Plan
                </h4>

                <div class="space-y-6">
                    <!-- Lump Sum vs Installment Radios -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-3 p-4 rounded-2xl border transition-all cursor-pointer"
                               :class="paymentType === 'lump_sum' ? 'border-blue-600 bg-blue-50/50 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_type" value="lump_sum" x-model="paymentType" class="w-4 h-4 text-blue-600">
                            <div>
                                <div class="text-sm font-bold text-slate-900">Lump Sum Payment</div>
                                <div class="text-xs text-slate-500">Pay total fee in a single transaction</div>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-4 rounded-2xl border transition-all cursor-pointer"
                               :class="paymentType === 'installment' ? 'border-blue-600 bg-blue-50/50 ring-1 ring-blue-600' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="payment_type" value="installment" x-model="paymentType" class="w-4 h-4 text-blue-600">
                            <div>
                                <div class="text-sm font-bold text-slate-900">Installment Plan</div>
                                <div class="text-xs text-slate-500">Split balance into scheduled due dates</div>
                            </div>
                        </label>
                    </div>

                    <!-- Installment Schedule Generator -->
                    <div x-show="paymentType === 'installment'" class="space-y-4 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-bold text-slate-800">Installment Schedule</h5>
                                <p class="text-xs text-slate-500">Set amount and due date for each installment</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="generateEqualInstallments(2)" class="px-3 py-1 rounded-lg text-xs font-bold bg-white border border-slate-200 hover:bg-slate-100">2 Parts</button>
                                <button type="button" @click="generateEqualInstallments(3)" class="px-3 py-1 rounded-lg text-xs font-bold bg-white border border-slate-200 hover:bg-slate-100">3 Parts</button>
                                <button type="button" @click="addInstallmentRow()" class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-700">+ Add Row</button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <template x-for="(inst, index) in installments" :key="index">
                                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200">
                                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0" x-text="'#' + (index + 1)"></span>
                                    <div class="flex-1">
                                        <input type="number" :name="'installments[' + index + '][amount]'" x-model.number="inst.amount" placeholder="Amount (PKR)" required min="1"
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-bold outline-none focus:ring-2 focus:ring-blue-600">
                                    </div>
                                    <div class="flex-1">
                                        <input type="date" :name="'installments[' + index + '][due_date]'" x-model="inst.due_date" required
                                               class="w-full px-3 py-1.5 rounded-lg border border-slate-200 text-sm outline-none focus:ring-2 focus:ring-blue-600">
                                    </div>
                                    <button type="button" @click="removeInstallmentRow(index)" class="p-1.5 text-slate-400 hover:text-rose-500">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Initial Down Payment at Desk -->
                    <div class="bg-blue-50/60 p-5 rounded-2xl border border-blue-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-1.5">Initial Payment Received at Desk (PKR)</label>
                            <input type="number" name="initial_payment" min="0" step="any" value="{{ old('initial_payment', 0) }}"
                                   placeholder="0 if paying later"
                                   class="w-full font-bold px-4 py-2 rounded-xl border border-blue-200 bg-white text-base text-slate-900 outline-none focus:ring-2 focus:ring-blue-600">
                            <p class="text-[11px] text-blue-700/80 mt-1">If entered, an official fee payment voucher and receipt # will be auto-generated.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-1.5">Payment Method</label>
                            <select name="payment_mode" class="w-full px-4 py-2 rounded-xl border border-blue-200 bg-white text-sm font-semibold text-slate-800 outline-none focus:ring-2 focus:ring-blue-600">
                                <option value="Cash">Cash at Front Desk</option>
                                <option value="Bank">Bank Deposit / Transfer</option>
                                <option value="Online">Online Gateway / JazzCash / EasyPaisa</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admissions.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
                    Confirm & Complete Admission
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function admissionForm() {
    return {
        selectedCourses: [{{ $prefillStudent?->course_id ?? ($courses->first()?->id ?? 1) }}],
        courseFeeMap: {
            @foreach($courses as $c)
                {{ $c->id }}: {{ (float) $c->default_fee }},
            @endforeach
        },
        totalPackage: {{ (float) ($prefillStudent?->course?->default_fee ?? ($courses->first()?->default_fee ?? 25000)) }},
        discount: 0,
        netPayable: {{ (float) ($prefillStudent?->course?->default_fee ?? ($courses->first()?->default_fee ?? 25000)) }},
        paymentType: 'installment',
        installments: [
            { amount: Math.round({{ (float) ($prefillStudent?->course?->default_fee ?? ($courses->first()?->default_fee ?? 25000)) }} / 2), due_date: new Date().toISOString().split('T')[0] },
            { amount: Math.round({{ (float) ($prefillStudent?->course?->default_fee ?? ($courses->first()?->default_fee ?? 25000)) }} / 2), due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0] }
        ],

        toggleCourse(id, fee) {
            const index = this.selectedCourses.indexOf(id);
            if (index > -1) {
                if (this.selectedCourses.length > 1) {
                    this.selectedCourses.splice(index, 1);
                }
            } else {
                this.selectedCourses.push(id);
            }
            this.calculateTotalFromCourses();
        },

        calculateTotalFromCourses() {
            let total = 0;
            this.selectedCourses.forEach(id => {
                total += (this.courseFeeMap[id] || 0);
            });
            this.totalPackage = total;
            this.recalculate();
        },

        recalculate() {
            this.netPayable = Math.max(0, this.totalPackage - (this.discount || 0));
            this.generateEqualInstallments(this.installments.length || 2);
        },

        generateEqualInstallments(count) {
            if (count < 1) count = 1;
            const perInst = Math.round(this.netPayable / count);
            this.installments = [];
            for (let i = 0; i < count; i++) {
                const dueDate = new Date(Date.now() + i * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                this.installments.push({
                    amount: (i === count - 1) ? (this.netPayable - (perInst * (count - 1))) : perInst,
                    due_date: dueDate
                });
            }
        },

        addInstallmentRow() {
            const dueDate = new Date(Date.now() + this.installments.length * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            this.installments.push({ amount: 0, due_date: dueDate });
        },

        removeInstallmentRow(index) {
            if (this.installments.length > 1) {
                this.installments.splice(index, 1);
            }
        }
    };
}
</script>
@endpush
@endsection
