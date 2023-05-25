<?php

namespace App\Http\Requests\Ad;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class AdStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'text' => 'string|min:1|max:2000',
            'files' => [
                'required',
                'min:1',
                'max:10',
                File::types(['jpg', 'png', 'avif', 'webp'])
                    ->min(1024)
                    ->max(12 * 1024),
            ]
        ];
    }
}
