<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMicePackageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:30',
            'rating_value' => 'nullable|integer|min:1|max:5',
            'rating_count' => 'nullable|integer|min:0',

            'slug' => 'required|string|max:255|unique:mice_packages,slug',
            'category_id' => 'required|integer|exists:mice_categories,id',

            'destination' => 'nullable|string|max:255',
            'duration_text' => 'nullable|string|max:255',

            'long_description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'include_text' => 'nullable|string',
            'exclude_text' => 'nullable|string',

            'is_active' => 'required|boolean',

            'thumbnail' => 'nullable|image|max:5120',
            'gallery.*' => 'nullable|image|max:5120',

            // DOMESTIC wajib ada minimal 1 baris
            'tiers.domestic' => 'required|array|min:1',
            'tiers.domestic.*.id' => 'nullable|integer',
            'tiers.domestic.*.label_text' => 'nullable|string|max:255',
            'tiers.domestic.*.price' => 'required|integer|min:0',
            'tiers.domestic.*.sort_order' => 'nullable|integer|min:0',
            'tiers.domestic.*.type' => 'nullable|in:domestic,foreign',

            // FOREIGN opsional
            'tiers.foreign' => 'nullable|array',
            'tiers.foreign.*.id' => 'nullable|integer',
            'tiers.foreign.*.label_text' => 'nullable|string|max:255',
            'tiers.foreign.*.price' => 'required|integer|min:0',
            'tiers.foreign.*.sort_order' => 'nullable|integer|min:0',
            'tiers.foreign.*.type' => 'nullable|in:domestic,foreign',

            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:1000',
            'seo_keywords' => 'nullable|string|max:1000',
        ];
    }
}
