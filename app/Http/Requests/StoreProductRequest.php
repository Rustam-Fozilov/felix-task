<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.product_name' => 'required|string',
            'products.*.product_qty' => 'required|integer',
            'products.*.materials' => 'required|array',
            'products.*.materials.*.id' => 'required|integer',
            'products.*.materials.*.qty' => 'required|integer',
        ];
    }
}
