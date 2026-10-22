@extends('layouts.app')

@section('title', 'Manage Branches - EduTrack CRM')
@section('page_title', 'Branch Management')
@section('page_subtitle', 'Multi-branch institute campuses, branch codes, and roll number prefixes')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <h3 class="text-base font-bold text-slate-900">Institute Branches & Campuses</h3>
            <p class="text-xs text-slate-500">Each branch has a unique Roll Number prefix (e.g. B1-0001) and assigned staff officers</p>
        </div>

        <a href="{{ route('admin.branches.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-600/25 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Branch</span>
        </a>
    </div>

    <!-- Branches Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Branch Name & Code</th>
                        <th class="px-4 py-3.5">Roll No Format</th>
                        <th class="px-4 py-3.5">Contact & Location</th>
                        <th class="px-4 py-3.5 text-center">Staff</th>
                        <th class="px-4 py-3.5 text-center">Inquiries</th>
                        <th class="px-4 py-3.5 text-center">Admissions</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($branches as $branch)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center shrink-0 border border-blue-200">
                                        {{ $branch->code }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.branches.show', $branch) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            {{ $branch->name }}
                                        </a>
                                        <div class="text-xs text-slate-500">Code: <span class="font-mono font-bold text-blue-600">{{ $branch->code }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $branch->code }}-0001
                                </span>
                            </td>
                            <td class="px-4 py-4 text-xs">
                                <div class="font-medium text-slate-800">{{ $branch->phone ?: 'No phone' }}</div>
                                <div class="text-slate-400 truncate max-w-[200px]">{{ $branch->address ?: 'No address specified' }}</div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <a href="{{ route('admin.branches.show', $branch) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition-all hover:scale-105"
                                   title="Click to view staff members in this branch">
                                    <span>{{ $branch->users_count }} Staff</span>
                                    <span class="text-blue-500 font-bold">&rarr;</span>
                                </a>
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-700">
                                {{ $branch->students_count }}
                            </td>
                            <td class="px-4 py-4 text-center font-bold text-blue-600">
                                {{ $branch->admissions_count }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($branch->status === 'active')
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
                                    <a href="{{ route('admin.branches.show', $branch) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View Branch Staff & Performance">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.branches.edit', $branch) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Branch">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                No branches found. Click "Add New Branch" to create your first campus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $branches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
