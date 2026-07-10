<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'slug'  => 'required|string|max:220|unique:ship_packages,slug',
            'label' => 'nullable|string|max:30',

            'category_id' => 'nullable|integer|exists:ship_categories,id',

            'thumbnail' => 'nullable|image|max:4096',

            'is_active' => 'required|in:0,1',

            'features' => 'nullable|array',
            'features.*.name' => 'required_with:features|string|max:120',
            'features.*.available' => 'nullable|in:1',

            'long_description' => 'nullable|string',

            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',

            // pricing (weekday/weekend) no custom
            'tiers' => 'required|array|min:1',
            'tiers.*.type' => 'required|in:weekday,weekend',
            'tiers.*.label_text' => 'required|string|max:160',
            'tiers.*.price' => 'required|integer|min:0',

            'rating_value' => 'nullable|numeric|min:1|max:5',
            'rating_count' => 'nullable|integer|min:0',
        ];
    }
}
