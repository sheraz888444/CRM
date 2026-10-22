<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::withCount(['students', 'admissions'])->orderBy('sort_order')->orderBy('name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $courses = $query->paginate(20)->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20'],
            'default_fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $course = Course::create($data);

        ActivityLog::record(
            'created_course',
            "Created course: {$course->name} (Fee: PKR " . number_format($course->default_fee) . ")",
            ['course_id' => $course->id]
        );

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' added successfully.");
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:20'],
            'default_fee' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        $course->update($data);

        ActivityLog::record(
            'updated_course',
            "Updated course: {$course->name}",
            ['course_id' => $course->id]
        );

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' updated successfully.");
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->admissions()->count() > 0 || $course->students()->count() > 0) {
            $course->update(['status' => 'inactive']);
            return redirect()->back()->with('success', "Course '{$course->name}' has existing records, so its status was set to Inactive.");
        }

        $name = $course->name;
        $course->delete();

        ActivityLog::record(
            'deleted_course',
            "Deleted course: {$name}",
            ['course_id' => $course->id]
        );

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$name}' deleted successfully.");
    }
}
