<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUmrahPackageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('umrah_package')?->id ?? null;

        return [
            'title' => 'required|string|max:255',
            'label' => 'nullable|string|max:30',
            'rating_value' => 'nullable|integer|min:1|max:5',
            'rating_count' => 'nullable|integer|min:0',
            'slug' => 'required|string|max:255|unique:umrah_packages,slug,' . $id,
            'category_id' => 'required|exists:umrah_categories,id',

            'destination' => 'nullable|string|max:255',
            'duration_text' => 'nullable|string|max:255',

            'long_description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'include_text' => 'nullable|string',
            'exclude_text' => 'nullable|string',
'is_active' => 'required|in:0,1',

            'seo_title' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string',
            'seo_description' => 'nullable|string',

            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:png,jpg,jpeg,webp|max:4096',

            'tiers' => 'required|array|min:1',
            'tiers.*.id' => 'nullable|integer',
            'tiers.*.label_text' => 'nullable|string|max:255',
            'tiers.*.price' => 'required|integer|min:0',
            'tiers.*.sort_order' => 'nullable|integer|min:0',
        ];
    }
}
