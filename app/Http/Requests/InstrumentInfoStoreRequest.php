<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class InstrumentInfoStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'financial_report_type' => [
                'required',
                'string',
            ],

            'instrument_url' => [
                'nullable',
                'url',
            ],

            'instrument_json' => [
                'nullable',
                'json',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {

                $type = $this->input('financial_report_type');

                $hasUrl = $this->filled('instrument_url');
                $hasJson = $this->filled('instrument_json');

                if ($type === 'financial_statements') {

                    if (!$hasUrl) {
                        $validator->errors()->add(
                            'instrument_url',
                            'Instrument URL is required.'
                        );
                    }

                    if ($hasJson) {
                        $validator->errors()->add(
                            'instrument_json',
                            'Instrument JSON is prohibited.'
                        );
                    }

                    return;
                }

                if ($hasUrl === $hasJson) {
                    $validator->errors()->add(
                        'instrument_url',
                        'Exactly one of URL or JSON must be provided.'
                    );

                    $validator->errors()->add(
                        'instrument_json',
                        'Exactly one of URL or JSON must be provided.'
                    );
                }
            },
        ];
    }
}
