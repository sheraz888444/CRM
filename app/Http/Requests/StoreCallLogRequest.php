<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'call_type' => ['required', 'in:Incoming,Outgoing,Walk-in,WhatsApp'],
            'call_date' => ['required', 'date'],
            'call_time' => ['required', 'string'],
            'duration' => ['nullable', 'string', 'max:50'],
            'remarks' => ['required', 'string'],
            'next_follow_up_date' => ['nullable', 'date'],
            'status_after_call' => ['required', 'string', 'max:100'],
        ];
    }
}
