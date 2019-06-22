<?php

namespace Modules\Branch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchMenuUpdate extends FormRequest
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
            'id' => 'required|exists:branch_menus,id',
            'price' => 'required'
        ];
    }
}
