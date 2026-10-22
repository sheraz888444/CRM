<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Support both 'area' and 'city' input names seamlessly
        if ($this->filled('area') && !$this->filled('city')) {
            $this->merge(['city' => $this->area]);
        } elseif ($this->filled('city') && !$this->filled('area')) {
            $this->merge(['area' => $this->city]);
        }
    }

    public function rules(): array
    {
        return [
            'student_name' => ['required', 'string', 'max:255'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:50'],
            'alternate_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'class_applying_for' => ['nullable', 'string', 'max:100'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'inquiry_source' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'remarks' => ['nullable', 'string'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'mode_id' => ['nullable', 'exists:inquiry_modes,id'],
            'visit_date' => ['nullable', 'date'],
            'next_followup_date' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'city' => 'area / locality',
            'area' => 'area / locality',
        ];
    }

    public function messages(): array
    {
        return [
            'city.required' => 'Student area / locality is mandatory. Please specify the student residential area.',
        ];
    }
}
