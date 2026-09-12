<?php

namespace App\Http\Requests;

use App\Models\Listing;
use App\Models\ListingCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaveListingRequest extends FormRequest
{
    public function authorize() { return $this->user() !== null; }

    protected function prepareForValidation()
    {
        // Android sends JSON fields plus uploaded files in one multipart request.
        if ($this->has('payload')) {
            $payload = json_decode($this->input('payload'), true);
            if (!is_array($payload) || json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages(['payload' => 'Data formulir tidak valid.']);
            }
            unset($payload['_method'], $payload['cover_image'], $payload['images']);
            $this->merge($payload);
        }
        if (!$this->has('transaction_type') && $this->has('listing_type')) {
            $this->merge(['transaction_type' => $this->input('listing_type') === 'disewakan' ? 'disewa' : $this->input('listing_type')]);
        }
    }

    public function rules()
    {
        $editing = $this->route('id') !== null;
        $listing = $editing ? Listing::where('user_id', $this->user()->id)->findOrFail($this->route('id')) : null;
        $type = $listing ? $listing->type : $this->input('type');
        $rules = [
            'type' => ['required', Rule::in($listing ? [$listing->type] : ['property', 'goods', 'services'])],
            'listing_category_id' => ['required', Rule::exists('listing_categories', 'id')->where('type', $type)],
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:9999999999999.99',
            'location' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:255',
            'transaction_type' => $type === 'property' ? 'required|in:dijual,disewa' : 'nullable|in:dijual,disewa',
            'condition' => $type === 'goods' ? 'required|in:Baru,Bekas' : 'nullable|string|max:255',
            'latitude' => 'nullable|required_with:longitude|numeric|between:-90,90',
            'longitude' => 'nullable|required_with:latitude|numeric|between:-180,180',
            'maps_url' => 'nullable|url|max:2048',
            'youtube_url' => 'nullable|url|max:255',
            'cover_image' => ($editing && $listing->cover_image ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png,webp|max:20480',
            'images' => 'nullable|array|max:18',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:20480',
            'delete_images' => 'nullable|array|max:18',
            'delete_images.*' => ['integer', 'distinct', Rule::exists('listing_images', 'id')->where('listing_id', $listing ? $listing->id : 0)],
            'facilities' => 'nullable|array|max:100',
            'facilities.*' => 'string|max:255',
            'surroundings' => 'nullable|array|max:100',
            'surroundings.*' => 'string|max:255',
        ];
        foreach (['address', 'currency', 'rental_period', 'min_rental', 'price_type', 'property_type', 'certificate', 'car_access', 'water_source', 'facing_direction', 'build_year', 'furnished_status', 'brand', 'service_area', 'phone'] as $key) {
            $rules[$key] = 'nullable|string|max:255';
        }
        foreach (['bedrooms', 'bathrooms', 'land_area', 'building_area', 'floors', 'electricity', 'maid_bedrooms', 'maid_bathrooms', 'carport', 'garage'] as $key) {
            $rules[$key] = 'nullable|integer|min:0|max:2147483647';
        }
        foreach (['co_broke', 'negotiable', 'imb', 'pbb'] as $key) $rules[$key] = 'nullable|boolean';
        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'Kolom :attribute wajib diisi.',
            'listing_category_id.exists' => 'Kategori tidak sesuai dengan tipe iklan.',
            'type.in' => 'Tipe iklan tidak valid atau tidak dapat diubah.',
            'images.max' => 'Galeri maksimal 18 foto.',
            'cover_image.max' => 'Foto utama maksimal 20 MB.',
            'images.*.max' => 'Setiap foto maksimal 20 MB.',
            'delete_images.*.exists' => 'Foto tidak ditemukan pada iklan ini.',
        ];
    }
}
