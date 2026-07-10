<?php

namespace App\Http\Controllers\Admin;

use App\Models\TourPackage;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\RentCarPackage;
use App\Models\ShipPackage;
use App\Models\UmrahPackage;
use App\Models\MicePackage;


class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $status = 'pending';
        }

        $page = (int) $request->query('page', 1);
        if ($page < 1) $page = 1;

        $reviews = Review::query()
    ->with('reviewable')
    ->where('status', $status)
    ->latest()
    ->paginate(20, ['*'], 'page', $page);


        $reviews->appends($request->query());

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function create()
{
    // gak load semua paket (bisa berat). Paket akan dicari via search bar (AJAX).
    return view('admin.reviews.create');
}
public function packages(Request $request)
{
    $data = $request->validate([
        'type' => ['required', 'in:tour,rent_car,ship,umrah'],
        'q'    => ['nullable', 'string', 'max:80'],
    ]);

    $q = trim((string) ($data['q'] ?? ''));
    $limit = 20;

    $query = match ($data['type']) {
        'tour'      => TourPackage::query(),
        'rent_car'  => RentCarPackage::query(),
        'ship'      => ShipPackage::query(),
        'umrah'     => UmrahPackage::query(),
        'mice'      => MicePackage::query(),
    };

    // optional: cuma yang aktif (kalau lo mau admin bisa review paket nonaktif, hapus filter ini)
    // $query->where('is_active', true);

    if ($q !== '') {
        $query->where('title', 'like', '%' . $q . '%')
              ->orWhere('id', $q); // biar bisa cari by ID juga kalau admin paste angka
    }

    $items = $query->orderBy('title')
        ->limit($limit)
        ->get(['id', 'title'])
        ->map(fn ($p) => [
            'id'    => $p->id,
            'title' => $p->title,
        ]);

    return response()->json([
        'items' => $items,
    ]);
}


    public function store(Request $request)
    {
        $data = $request->validate([
    'package_type' => ['required', 'in:tour,rent_car,ship,umrah'],
    'package_id'   => ['required', 'integer'],
    'name'         => ['required', 'string', 'max:120'],
    'email'        => ['required', 'email', 'max:190'],
    'rating'       => ['required', 'integer', 'min:1', 'max:5'],
    'comment'      => ['required', 'string', 'max:2000'],
]);

$model = match ($data['package_type']) {
    'tour'     => TourPackage::findOrFail($data['package_id']),
    'rent_car' => RentCarPackage::findOrFail($data['package_id']),
    'ship'     => ShipPackage::findOrFail($data['package_id']),
    'umrah'    => UmrahPackage::findOrFail($data['package_id']),
};

$model->reviews()->create([
    'name'       => $data['name'],
    'email'      => $data['email'],
    'rating'     => $data['rating'],
    'comment'    => $data['comment'],
    'status'     => 'approved',
    'ip_address' => $request->ip(),
    'user_agent' => substr((string) $request->userAgent(), 0, 512),
]);

return redirect()
    ->route('admin.reviews.index', ['status' => 'approved'])
    ->with('success', 'Review admin berhasil ditambahkan dan langsung tampil.');

    }


    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);
        return back()->with('success', 'Review di-approve.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);
        return back()->with('success', 'Review di-decline.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review dihapus.');
    }
    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        // datetime-local dari admin dianggap WIB (Asia/Jakarta),
        // disimpan ke DB sebagai UTC (default config app timezone).
        $data = $request->validate([
            'comment'    => ['required', 'string', 'min:1', 'max:1000'],
            'created_at' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $review->comment = $data['comment'];

        $review->created_at = Carbon::createFromFormat('Y-m-d\TH:i', $data['created_at'], 'Asia/Jakarta')
            ->utc();

        $review->save();

        return redirect()
            ->route('admin.reviews.index', ['status' => $review->status])
            ->with('success', 'Review berhasil diupdate.');
    }
}
