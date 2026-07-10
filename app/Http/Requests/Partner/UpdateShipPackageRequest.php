<?php

namespace App\Http\Requests\Partner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shipPackageParam = $this->route('ship_package') ?? $this->route('ship_package_id') ?? $this->route('id');

        $ignoreId = is_object($shipPackageParam)
            ? ($shipPackageParam->id ?? null)
            : $shipPackageParam;

        return [
            'title' => ['required', 'string', 'max:200'],

            'slug'  => [
                'required', 'string', 'max:220',
                Rule::unique('ship_packages', 'slug')->ignore($ignoreId),
            ],

            'label' => ['nullable', 'string', 'max:30'],
            'category_id' => ['nullable', 'integer', 'exists:ship_categories,id'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],

            // partner TIDAK boleh submit is_active
            'is_active' => ['nullable', 'in:0,1'],

            'features' => ['nullable', 'array'],
            'features.*.name' => ['required_with:features', 'string', 'max:120'],
            'features.*.available' => ['nullable', 'in:1'],

            'long_description' => ['nullable', 'string'],

            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],

            'tiers' => ['required', 'array', 'min:1'],
            'tiers.*.type' => ['required', 'in:weekday,weekend'],
            'tiers.*.label_text' => ['required', 'string', 'max:160'],
            'tiers.*.price' => ['required', 'integer', 'min:0'],

            'rating_value' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
