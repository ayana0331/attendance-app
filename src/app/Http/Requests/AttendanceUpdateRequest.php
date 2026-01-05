<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in'    => 'required',
            'clock_out'   => 'required',
            'break_start' => 'nullable',
            'break_end'   => 'nullable',
            'break2_start' => 'nullable',
            'break2_end'   => 'nullable',
            'remarks'     => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $in  = $this->input('clock_in');
            $out = $this->input('clock_out');
            $b1s = $this->input('break_start');
            $b1e = $this->input('break_end');
            $b2s = $this->input('break2_start');
            $b2e = $this->input('break2_end');

            if ($in && $out && $in > $out) {
                $validator->errors()->add('clock_in', '出勤時間が不適切な値です');
            }

            if ($b1s && $out && $b1s > $out) {
                $validator->errors()->add('break_start', '休憩時間が不適切な値です');
            }

            if ($b2s && $out && $b2s > $out) {
                $validator->errors()->add('break2_start', '休憩時間が不適切な値です');
            }

            if ($b1e && $out && $b1e > $out) {
                $validator->errors()->add('break_end', '休憩時間もしくは退勤時間が不適切な値です');
            }

            if ($b2e && $out && $b2e > $out) {
                $validator->errors()->add('break2_end', '休憩時間もしくは退勤時間が不適切な値です');
            }
        });
    }
}
