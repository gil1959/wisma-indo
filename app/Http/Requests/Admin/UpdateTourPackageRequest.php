<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTourPackageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $packageId = $this->route('tour_package'); // id dari route model binding

        return [
            // ========= BASIC INFO =========
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => [
                'required',
                'string',
                'max:255',
                Rule::unique('tour_packages', 'slug')->ignore($packageId),
            ],
            'label' => ['nullable', 'string', 'max:30'],

            'duration_text'    => ['required', 'string', 'max:255'],
            'destination'      => ['nullable', 'string', 'max:255'],
            'category_id'      => ['required', 'exists:tour_categories,id'],
            'long_description' => ['nullable', 'string'],
'is_active' => ['nullable', 'in:0,1'],


            'itinerary_text'  => ['nullable', 'string'],
'include_text'    => ['nullable', 'string'],
'exclude_text'    => ['nullable', 'string'],



            // ========= TIERS =========
            'tiers'                     => ['required', 'array'],

            // Domestic
            'tiers.domestic'            => ['required', 'array'],
            'tiers.domestic.*.label_text' => ['nullable', 'string', 'max:255'],

            'tiers.domestic.*.id'       => ['nullable', 'integer'],
            'tiers.domestic.*.min_people' => ['required', 'integer', 'min:1'],
            'tiers.domestic.*.max_people' => ['nullable', 'integer', 'gte:tiers.domestic.*.min_people'],
            'tiers.domestic.*.price'      => ['required', 'integer', 'min:0'],
            'tiers.domestic.*.type'       => ['required', Rule::in(['domestic'])],
            'tiers.domestic.*.is_custom'  => ['required', 'boolean'],
 'subcategory_id' => [
            'nullable',
            'integer',
            Rule::exists('tour_categories', 'id')
                ->where(fn ($q) => $q->where('parent_id', $this->input('category_id'))),
        ],
            // International (OPSIONAL)
'tiers.international'               => ['nullable', 'array'],
'tiers.international.*.label_text' => ['nullable', 'string', 'max:255'],

'tiers.international.*.id'          => ['nullable', 'integer'],
'tiers.international.*.min_people'  => ['required_with:tiers.international.*.price', 'integer', 'min:1'],
'tiers.international.*.max_people'  => ['nullable', 'integer', 'gte:tiers.international.*.min_people'],
'tiers.international.*.price'       => ['nullable', 'integer', 'min:0'],
'tiers.international.*.type'        => ['nullable', Rule::in(['international'])],
'tiers.international.*.is_custom'   => ['nullable', 'boolean'],


            'thumbnail' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'rating_value' => ['nullable', 'integer', 'min:1', 'max:5'],
            'rating_count' => ['nullable', 'integer', 'min:0'],

            // ========= FLIGHT INFO =========
            'flight_info' => ['required', Rule::in(['included', 'not_included'])],
        ];
    }

    public function prepareForValidation()
    {
        $tiers = $this->tiers ?? [];

if (isset($tiers['international']) && is_array($tiers['international'])) {
    $tiers['international'] = collect($tiers['international'])
        ->filter(function ($row) {
            $price = $row['price'] ?? null;
            return !($price === null || $price === '');
        })
        ->values()
        ->all();
}

$this->merge([
    'tiers' => $tiers,
]);

        // Filter includes
        $includes = array_filter(
            $this->includes ?? [],
            fn($v) =>
            $v !== null && trim($v) !== ''
        );

        // Filter excludes
        $excludes = array_filter(
            $this->excludes ?? [],
            fn($v) =>
            $v !== null && trim($v) !== ''
        );

        // Filter itinerary yang kosong (BAIK DI CREATE ATAU EDIT)
        $itineraries = collect($this->itineraries ?? [])
            ->filter(function ($row) {

                // Buang baris jika time & title dua-duanya kosong
                if (!isset($row['title']) || trim($row['title']) === '') {
                    return false;
                }
                return true;
            })
            ->values()
            ->all();

        $this->merge([
            'includes'    => $includes,
            'excludes'    => $excludes,
            'itineraries' => $itineraries,
        ]);
    }


    public function messages()
    {
        return [
            'slug.unique' => 'Slug sudah digunakan paket lain.',
            'itineraries.*.time.date_format' => 'Format jam itinerary harus HH:MM',
        ];
    }
}
