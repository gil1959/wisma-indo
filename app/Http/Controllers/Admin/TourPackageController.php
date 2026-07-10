<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPackageRequest;
use App\Http\Requests\Admin\UpdateTourPackageRequest;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\TourPackagePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    public function index(Request $request)
{
    $q        = trim((string) $request->query('q', ''));
    $category = $request->query('category');
    $status   = $request->query('status'); // active | inactive | (kosong)

    $query = TourPackage::query()->with('category');

    if ($q !== '') {
        $query->where(function ($qq) use ($q) {
            $qq->where('title', 'like', "%{$q}%")
               ->orWhere('slug', 'like', "%{$q}%");
        });
    }

    if (!empty($category)) {
        $query->where('category_id', $category);
    }

    if ($status === 'active') {
        $query->where('is_active', 1);
    } elseif ($status === 'inactive') {
        $query->where('is_active', 0);
    }

    $packages = $query->latest()->paginate(20)->withQueryString();
    $categories = TourCategory::whereNull('parent_id')->orderBy('name')->get();

    return view('admin.tour-packages.index', compact('packages', 'categories', 'q', 'category', 'status'));
}


    public function create()
    {
        $categories = TourCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.tour-packages.create', compact('categories'));
    }

    public function store(StoreTourPackageRequest $request)
    {

        DB::transaction(function () use ($request) {
$includes = $this->htmlToLines($request->input('include_text'));
$excludes = $this->htmlToLines($request->input('exclude_text'));
            $package = TourPackage::create([
                'title'            => $request->title,
                'label' => $request->label,
'is_active'        => (int) $request->input('is_active', 1),
                'rating_value' => $request->rating_value ?? 5,
                'rating_count' => $request->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'long_description' => $request->long_description,
                'includes'         => $includes,
    'excludes'         => $excludes,
                'flight_info'      => $request->flight_info,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // =====================
            // SAVE THUMBNAIL
            // =====================
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('tour-packages', 'public');
                $package->update(['thumbnail_path' => $thumbPath]);
            }

            // =====================
            // SAVE GALLERY
            // =====================
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $package->photos()->create([
                        'file_path' => $path   // FIX: field berubah
                    ]);
                }
            }

         $this->replaceItinerariesFromHtml($package, $request->input('itinerary_text'));
            $this->syncTiers($package, $request->tiers);

            \App\Jobs\Translate\TourPackageToEn::dispatch($package->id)
    ->onQueue('translations')
    ->afterCommit();

        });

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(TourPackage $tour_package)
    {
        $categories = TourCategory::orderBy('name')->get();
        $package = $tour_package->load(['tiers', 'itineraries', 'photos']);
        return view('admin.tour-packages.edit', compact('package', 'categories'));
    }

    public function update(UpdateTourPackageRequest $request, TourPackage $tour_package)
    {
        DB::transaction(function () use ($request, $tour_package) {
$includes = $this->htmlToLines($request->input('include_text'));
$excludes = $this->htmlToLines($request->input('exclude_text'));
            $tour_package->update([
                'title'            => $request->title,
                'label' => $request->label,
'subcategory_id' => $request->subcategory_id,
'is_active'        => (int) $request->input('is_active', $tour_package->is_active ? 1 : 0),
                'rating_value' => $request->rating_value ?? $tour_package->rating_value ?? 5,
                'rating_count' => $request->rating_count ?? $tour_package->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'long_description' => $request->long_description,
                'includes'         => $includes,
    'excludes'         => $excludes,
                'flight_info'      => $request->flight_info,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,
            ]);

            // UPDATE THUMBNAIL
            if ($request->hasFile('thumbnail')) {
                if ($tour_package->thumbnail_path) {
                    Storage::disk('public')->delete($tour_package->thumbnail_path);
                }
                $newThumb = $request->file('thumbnail')->store('tour-packages', 'public');
                $tour_package->update(['thumbnail_path' => $newThumb]);
            }

            // ADD NEW GALLERY PHOTOS
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $tour_package->photos()->create(['file_path' => $path]);
                }
            }

            $this->replaceItinerariesFromHtml($tour_package, $request->input('itinerary_text'));
            $this->syncTiers($tour_package, $request->tiers);
            \App\Jobs\Translate\TourPackageToEn::dispatch($tour_package->id)
    ->onQueue('translations')
    ->afterCommit();

        });

        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(TourPackage $tour_package)
    {
        $tour_package->delete();
        return redirect()->route('admin.tour-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    private function syncItineraries(TourPackage $package, ?array $items)
    {
        $existing = $package->itineraries()->pluck('id')->toArray();
        $submitted = [];

        if ($items) {
            foreach ($items as $row) {
                if (!empty($row['id'])) {
                    $submitted[] = (int)$row['id'];
                    $package->itineraries()->where('id', $row['id'])->update([
                        'title' => $row['title'],
                    ]);
                } else {
                    $new = $package->itineraries()->create([
                        'title' => $row['title'],
                    ]);
                    $submitted[] = $new->id;
                }
            }
        }

        $toDelete = array_diff($existing, $submitted);

        if ($toDelete) {
            $package->itineraries()->whereIn('id', $toDelete)->delete();
        }
    }
private function htmlToLines(?string $html): array
{
    if (!$html) return [];

    $text = $html;

    // br -> newline
    $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $text);

    // penutup block -> newline
    $text = preg_replace('/<\/\s*(p|div|li|h[1-6])\s*>/i', "\n", $text);

    // buang semua tag
    $text = strip_tags($text);

    // FIX: decode entity HTML (biar &nbsp; jadi spasi beneran)
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // FIX: normalisasi non-breaking space (NBSP) jadi spasi biasa
    $text = str_replace("\xC2\xA0", ' ', $text);

    $lines = preg_split("/\r\n|\r|\n/", $text) ?: [];
    $lines = array_map(fn($l) => trim(preg_replace('/\s+/', ' ', $l)), $lines);
    $lines = array_values(array_filter($lines, fn($l) => $l !== ''));

    return $lines;
}


private function replaceItinerariesFromHtml(TourPackage $package, ?string $html): void
{
    $lines = $this->htmlToLines($html);

    // reset total supaya urutan bener & gak nyisa data lama
    $package->itineraries()->delete();

    foreach ($lines as $idx => $title) {
        $package->itineraries()->create([
            'time' => null,
            'title' => $title,
            'sort_order' => $idx,
        ]);
    }
}

    private function syncTiers(TourPackage $package, array $tiers)
    {
        $existing = $package->tiers()->pluck('id')->toArray();
        $submitted = [];

        foreach (['domestic', 'international'] as $type) {

            foreach ($tiers[$type] ?? [] as $row) {
                if ($type === 'international' && (!isset($row['price']) || $row['price'] === '' || $row['price'] === null)) {
    continue;
}


                if (!empty($row['id'])) {
                    $submitted[] = (int)$row['id'];

                    $package->tiers()->where('id', $row['id'])->update([
                        'type'       => $row['type'],
                        'is_custom'  => (bool)$row['is_custom'],
                        'label_text' => $row['label_text'] ?? null,  
                        'min_people' => $row['is_custom'] ? 2 : $row['min_people'],
                        'max_people' => $row['is_custom'] ? null : $row['max_people'],
                        'price'      => $row['price'],
                    ]);
                } else {
                    $new = $package->tiers()->create([
                        'type'       => $row['type'],
                        'is_custom'  => (bool)$row['is_custom'],
                        'label_text' => $row['label_text'] ?? null,  
                        'min_people' => $row['is_custom'] ? 2 : $row['min_people'],
                        'max_people' => $row['is_custom'] ? null : $row['max_people'],
                        'price'      => $row['price'],
                    ]);
                    $submitted[] = $new->id;
                }
            }
        }

        $toDelete = array_diff($existing, $submitted);
        if ($toDelete) {
            $package->tiers()->whereIn('id', $toDelete)->delete();
        }
    }

    public function deletePhoto(TourPackagePhoto $photo)
    {
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
