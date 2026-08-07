<?php

namespace App\Jobs;

use App\Models\BulkUpload;
use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\ListingCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Imports\BulkListingImport;
use Maatwebsite\Excel\Facades\Excel;

class ProcessBulkUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour

    protected $bulkUpload;

    public function __construct(BulkUpload $bulkUpload)
    {
        $this->bulkUpload = $bulkUpload;
    }

    public function handle()
    {
        $this->bulkUpload->update(['status' => 'processing']);

        $filePath = storage_path('app/public/' . $this->bulkUpload->file_path);
        
        if (!file_exists($filePath)) {
            $this->bulkUpload->update([
                'status' => 'failed',
                'error_log' => ['error' => 'File tidak ditemukan di server.']
            ]);
            return;
        }

        try {
            $import = new BulkListingImport;
            Excel::import($import, $filePath);
            $rows = $import->data;

            $processed = 0;
            $failed = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                // withHeadingRow converts headings to slug format e.g. "Judul Iklan" -> "judul_iklan"
                $judulIklan = $this->matchKey($row, 'judul_iklan', 'judul');
                if (empty($judulIklan)) {
                    continue;
                }

                try {
                    $categoryName = $this->matchKey($row, 'kategori');
                    $category = ListingCategory::where('type', $this->bulkUpload->type)->where('name', $categoryName)->first();
                    $categoryId = $category ? $category->id : null;

                    $hargaRaw = $this->matchKey($row, 'harga', 'price');
                    $price = (float) preg_replace('/[^0-9]/', '', $hargaRaw ?? '0');

                    $bisaNego = $this->matchKey($row, 'bisa_nego', 'nego');
                    $deskripsi = $this->matchKey($row, 'deskripsi', 'description');
                    $lokasiSingkat = $this->matchKey($row, 'lokasi_singkat', 'lokasi');
                    $alamatLengkap = $this->matchKey($row, 'alamat_lengkap', 'alamat');
                    $googleMapsUrl = $this->matchKey($row, 'google_maps', 'maps');
                    $whatsapp = $this->matchKey($row, 'whatsapp', 'wa');
                    $telepon = $this->matchKey($row, 'telepon', 'phone');
                    $youtube = $this->matchKey($row, 'youtube', 'yt');

                    $listingData = [
                        'user_id' => $this->bulkUpload->user_id,
                        'title' => $judulIklan,
                        'slug' => Str::slug($judulIklan . '-' . uniqid()),
                        'description' => $deskripsi,
                        'price' => $price,
                        'negotiable' => strtolower($bisaNego ?? 'tidak') === 'ya',
                        'category' => $this->bulkUpload->type,
                        'type' => $this->bulkUpload->type,
                        'listing_category_id' => $categoryId,
                        'location' => $lokasiSingkat,
                        'address' => $alamatLengkap,
                        'maps_url' => $googleMapsUrl,
                        'whatsapp' => $whatsapp,
                        'phone' => $telepon,
                        'youtube_url' => $youtube,
                        'status' => 'tersedia', 
                        'is_active' => true,
                    ];

                    if ($this->bulkUpload->type === 'property') {
                        $tipeTransaksi = $this->matchKey($row, 'tipe_transaksi', 'transaksi');
                        $listingData['transaction_type'] = strtolower($tipeTransaksi ?? 'dijual');
                        $listingData['land_area'] = (int) ($this->matchKey($row, 'luas_tanah') ?: 0);
                        $listingData['building_area'] = (int) ($this->matchKey($row, 'luas_bangunan') ?: 0);
                        $listingData['bedrooms'] = (int) ($this->matchKey($row, 'kamar_tidur') ?: 0);
                        $listingData['bathrooms'] = (int) ($this->matchKey($row, 'kamar_mandi') ?: 0);
                        $listingData['floors'] = (int) ($this->matchKey($row, 'jml_lantai', 'jumlah_lantai', 'lantai') ?: 0);
                        $listingData['carport'] = (int) ($this->matchKey($row, 'carport') ?: 0);
                        $listingData['garage'] = (int) ($this->matchKey($row, 'garasi') ?: 0);
                        $listingData['build_year'] = $this->matchKey($row, 'tahun_bangun', 'tahun');
                        $listingData['certificate'] = $this->matchKey($row, 'sertifikat');
                        $listingData['furnished_status'] = $this->matchKey($row, 'kondisi_perabotan', 'perabotan', 'furnished');
                        $listingData['imb'] = strtolower($this->matchKey($row, 'imb') ?: 'tidak') === 'ya';
                        $listingData['pbb'] = strtolower($this->matchKey($row, 'pbb') ?: 'tidak') === 'ya';

                        // Case Insensitive Filtering for Facilities
                        $rawFacilities = explode(',', $this->matchKey($row, 'fasilitas'));
                        $validFacilities = ['Area Hiburan', 'Balkon', 'Gym', 'Halaman Terbuka', 'Jalan Raya', 'Karaoke', 'Keamanan 24 Jam', 'Kitchen Set', 'Kolam Renang', 'Lapangan Basket', 'Lapangan Tenis', 'One Gate System', 'Parkir', 'Pemanas Air', 'Pendingin Ruangan (AC)', 'Pos Security', 'Rooftop', 'Ruang Rapat', 'Ruang Serbaguna', 'Spa dan Sauna', 'Taman', 'Taman Bermain Anak', 'Telepon', 'Televisi', 'Tempat BBQ', 'Teras', 'Transportasi Umum', 'Trek Lari', 'WiFi'];
                        $filteredFacilities = [];
                        foreach ($rawFacilities as $rf) {
                            $cleanRaw = strtolower(trim($rf));
                            if(empty($cleanRaw)) continue;
                            foreach ($validFacilities as $vf) {
                                if ($cleanRaw === strtolower($vf)) {
                                    $filteredFacilities[] = $vf;
                                    break;
                                }
                            }
                        }
                        $listingData['facilities'] = $filteredFacilities;

                        // Case Insensitive Filtering for Surroundings
                        $rawSurroundings = explode(',', $this->matchKey($row, 'area_sekitar', 'area'));
                        $validSurroundings = ['Apotek', 'Kolam Renang', 'Mall', 'Masjid', 'Pasar', 'Rumah Sakit', 'Sarana Pendidikan', 'Sarana Perbelanjaan', 'Tempat Olahraga'];
                        $filteredSurroundings = [];
                        foreach ($rawSurroundings as $rs) {
                            $cleanRaw = strtolower(trim($rs));
                            if(empty($cleanRaw)) continue;
                            foreach ($validSurroundings as $vs) {
                                if ($cleanRaw === strtolower($vs)) {
                                    $filteredSurroundings[] = $vs;
                                    break;
                                }
                            }
                        }
                        $listingData['surroundings'] = $filteredSurroundings;

                    } elseif ($this->bulkUpload->type === 'goods') {
                        $listingData['transaction_type'] = 'dijual';
                        $listingData['brand'] = $this->matchKey($row, 'merek_brand', 'merek', 'brand');
                        $listingData['condition'] = strtolower($this->matchKey($row, 'kondisi') ?: 'baru');
                    } elseif ($this->bulkUpload->type === 'services') {
                        $listingData['transaction_type'] = 'jasa';
                        $listingData['service_area'] = $this->matchKey($row, 'area_layanan', 'area');
                    }

                    $listing = Listing::create($listingData);

                    // Cover Image
                    $coverUrl = $this->matchKey($row, 'cover_image_url', 'cover_image', 'cover');
                    if ($coverUrl && filter_var($coverUrl, FILTER_VALIDATE_URL)) {
                        $path = $this->downloadImage($coverUrl);
                        if ($path) {
                            $listing->update(['cover_image' => $path]);
                        }
                    }

                    // Images 2-12
                    for ($i = 2; $i <= 12; $i++) {
                        $imgUrl = $this->matchKey($row, 'image_'.$i.'_url', 'image_'.$i, 'image '.$i, 'image'.$i);
                        if ($imgUrl && filter_var($imgUrl, FILTER_VALIDATE_URL)) {
                            $path = $this->downloadImage($imgUrl);
                            if ($path) {
                                ListingImage::create([
                                    'listing_id' => $listing->id,
                                    'image_path' => $path,
                                ]);
                            }
                        }
                    }

                    $processed++;

                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $this->bulkUpload->update([
                'status' => 'completed',
                'processed_rows' => $processed,
                'failed_rows' => $failed,
                'error_log' => $errors
            ]);

        } catch (\Exception $e) {
            $this->bulkUpload->update([
                'status' => 'failed',
                'error_log' => ['System Error' => $e->getMessage()]
            ]);
        }
    }

    private function downloadImage($url)
    {
        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful()) {
                $ext = 'jpg';
                $contentType = $response->header('Content-Type');
                if (strpos($contentType, 'image/png') !== false) $ext = 'png';
                if (strpos($contentType, 'image/webp') !== false) $ext = 'webp';

                $filename = 'listings/' . Str::random(40) . '.' . $ext;
                Storage::disk('public')->put($filename, $response->body());
                return '/storage/' . $filename;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    private function matchKey($row, ...$keywords)
    {
        foreach ($row as $key => $val) {
            foreach ($keywords as $kw) {
                if (str_contains(strtolower($key), strtolower($kw))) {
                    return $val;
                }
            }
        }
        return '';
    }
}
