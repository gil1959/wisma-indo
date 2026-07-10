<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourPackageRequest;
use App\Http\Requests\Admin\UpdateTourPackageRequest;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\TourPackagePhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TourPackageController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);

        $packages = TourPackage::query()
            ->where('created_by_partner_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('partner.tour-packages.index', compact('packages'));
    }

    public function create()
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);

        $categories = TourCategory::query()
    ->whereNull('parent_id')
    ->where(function ($q) {
        $q->whereNull('created_by_partner_id')           // kategori admin/global
          ->orWhere('created_by_partner_id', auth()->id()); // kategori partner sendiri
    })
    ->orderBy('name')
    ->get();

return view('partner.tour-packages.create', compact('categories'));

    }

    public function store(StoreTourPackageRequest $request)
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);

        DB::transaction(function () use ($request) {
            $includes = $this->htmlToLines($request->input('include_text'));
            $excludes = $this->htmlToLines($request->input('exclude_text'));

            $package = TourPackage::create([
                'title'            => $request->title,
                'label'            => $request->label,
                'rating_value'     => $request->rating_value ?? 5,
                'rating_count'     => $request->rating_count ?? 0,
                'slug'             => $request->slug,
                'category_id'      => $request->category_id,
                'subcategory_id'   => $request->subcategory_id,
                'duration_text'    => $request->duration_text,
                'destination'      => $request->destination,
                'long_description' => $request->long_description,
                'includes'         => $includes,
                'excludes'         => $excludes,
                'flight_info'      => $request->flight_info,
                'seo_title'        => $request->seo_title,
                'seo_description'  => $request->seo_description,
                'seo_keywords'     => $request->seo_keywords,

                // tambahan wajib (tanpa ubah field lain)
                'created_by_partner_id' => auth()->id(),
                'partner_review_status' => 'pending',
                'is_active'             => false,
            ]);

            // thumbnail (copy mentah)
            if ($request->hasFile('thumbnail')) {
                $thumbPath = $request->file('thumbnail')->store('tour-packages', 'public');
                $package->update(['thumbnail_path' => $thumbPath]);
            }

            // gallery (copy mentah)
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $package->photos()->create(['file_path' => $path]);
                }
            }

            $this->replaceItinerariesFromHtml($package, $request->input('itinerary_text'));
            $this->syncTiers($package, $request->tiers);
        });

        return redirect()->route('partner.tour-packages.index')
            ->with('success', 'Paket berhasil dibuat dan menunggu review admin.');
    }

    public function edit(TourPackage $tour_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);
        abort_unless((int)$tour_package->created_by_partner_id === (int)auth()->id(), 403);

        $categories = TourCategory::query()
    ->where(function ($q) {
        $q->whereNull('created_by_partner_id')
          ->orWhere('created_by_partner_id', auth()->id());
    })
    ->orderBy('name')
    ->get();

$package = $tour_package->load(['tiers', 'itineraries', 'photos']);

return view('partner.tour-packages.edit', compact('package', 'categories'));

    }

    public function update(UpdateTourPackageRequest $request, TourPackage $tour_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);
        abort_unless((int)$tour_package->created_by_partner_id === (int)auth()->id(), 403);

        DB::transaction(function () use ($request, $tour_package) {
            $includes = $this->htmlToLines($request->input('include_text'));
            $excludes = $this->htmlToLines($request->input('exclude_text'));

            $tour_package->update([
                'title'            => $request->title,
                'label'            => $request->label,
                'subcategory_id'   => $request->subcategory_id,
                'rating_value'     => $request->rating_value ?? $tour_package->rating_value ?? 5,
                'rating_count'     => $request->rating_count ?? $tour_package->rating_count ?? 0,
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

            // paksa balik pending lagi (wajib)
            $tour_package->update([
                'is_active'            => false,
                'partner_review_status'=> 'pending',
                'partner_review_note'  => null,
                'partner_reviewed_by'  => null,
                'partner_reviewed_at'  => null,
            ]);

            if ($request->hasFile('thumbnail')) {
                if ($tour_package->thumbnail_path) {
                    Storage::disk('public')->delete($tour_package->thumbnail_path);
                }
                $newThumb = $request->file('thumbnail')->store('tour-packages', 'public');
                $tour_package->update(['thumbnail_path' => $newThumb]);
            }

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $img) {
                    $path = $img->store('tour-packages', 'public');
                    $tour_package->photos()->create(['file_path' => $path]);
                }
            }

            $this->replaceItinerariesFromHtml($tour_package, $request->input('itinerary_text'));
            $this->syncTiers($tour_package, $request->tiers);
        });

        return redirect()->route('partner.tour-packages.index')
            ->with('success', 'Paket berhasil diperbarui dan menunggu review admin.');
    }

    public function destroy(TourPackage $tour_package)
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);
        abort_unless((int)$tour_package->created_by_partner_id === (int)auth()->id(), 403);

        $tour_package->delete();

        return redirect()->route('partner.tour-packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    public function deletePhoto(TourPackagePhoto $photo)
    {
        abort_unless(auth()->user()->partner_type === 'agency_paket_tour', 403);

        $package = $photo->package ?? null;
        abort_unless($package && (int)$package->created_by_partner_id === (int)auth()->id(), 403);

        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    // ======= COPY MENTAH PRIVATE METHODS ADMIN =======

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
                        'min_people' => $row['is_custom'] ? 2 : $row['min_people'],
                        'max_people' => $row['is_custom'] ? null : $row['max_people'],
                        'price'      => $row['price'],
                    ]);
                } else {
                    $new = $package->tiers()->create([
                        'type'       => $row['type'],
                        'is_custom'  => (bool)$row['is_custom'],
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
}
