@extends('layouts.app')

@section('title', 'Course Master - EduTrack CRM')
@section('page_title', 'Course & Program Master')
@section('page_subtitle', 'Manage language courses, test preparation programs, and standard fee packages')

@section('content')
<div class="space-y-6" x-data="{ courseModal: false, isEditing: false, modalTitle: 'Add New Course', actionUrl: '{{ route('admin.courses.store') }}', method: 'POST', form: { name: '', code: '', default_fee: '', sort_order: 0, status: 'active' } }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Institute Course Offerings</h3>
            <p class="text-xs text-slate-500">IELTS, PTE, German, Korean & customized multi-course admission packages</p>
        </div>

        <button @click="isEditing = false; modalTitle = 'Add New Course'; actionUrl = '{{ route('admin.courses.store') }}'; method = 'POST'; form = { name: '', code: '', default_fee: '', sort_order: 0, status: 'active' }; courseModal = true;"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Course</span>
        </button>
    </div>

    <!-- Courses Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Course Name</th>
                        <th class="px-4 py-3.5">Code</th>
                        <th class="px-4 py-3.5">Standard Package Fee</th>
                        <th class="px-4 py-3.5 text-center">Inquiries</th>
                        <th class="px-4 py-3.5 text-center">Admitted</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($courses as $course)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($course->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $course->name }}</div>
                                        <div class="text-xs text-slate-400">Order: {{ $course->sort_order }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                    {{ $course->code ?: '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900">PKR {{ number_format($course->default_fee) }}</span>
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">
                                {{ $course->students_count }}
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-600">
                                {{ $course->admissions_count }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($course->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="isEditing = true; modalTitle = 'Edit Course'; actionUrl = '{{ route('admin.courses.update', $course) }}'; method = 'PUT'; form = { name: '{{ addslashes($course->name) }}', code: '{{ addslashes($course->code) }}', default_fee: '{{ $course->default_fee }}', sort_order: {{ $course->sort_order }}, status: '{{ $course->status }}' }; courseModal = true;"
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Course">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No courses configured yet. Click "Add New Course" to add IELTS, PTE, German, etc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course Modal (Add / Edit) -->
    <div x-show="courseModal" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="courseModal = false"
             class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 transition-all">
            
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <h4 class="text-base font-bold text-slate-900" x-text="modalTitle"></h4>
                <button @click="courseModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="actionUrl" method="POST" class="space-y-5">
                @csrf
                <template x-if="method === 'PUT'">
                    @method('PUT')
                </template>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Course Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" required
                           placeholder="e.g. IELTS Academic / General"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Short Code</label>
                        <input type="text" name="code" x-model="form.code"
                               placeholder="e.g. IELTS"
                               class="w-full font-mono uppercase px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Default Package Fee (PKR) <span class="text-rose-500">*</span></label>
                        <input type="number" name="default_fee" x-model="form.default_fee" required min="0" step="any"
                               placeholder="25000"
                               class="w-full font-bold px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Display Sort Order</label>
                        <input type="number" name="sort_order" x-model="form.sort_order" min="0"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Status <span class="text-rose-500">*</span></label>
                        <select name="status" x-model="form.status" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-600 outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="courseModal = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25">
                        Save Course
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
