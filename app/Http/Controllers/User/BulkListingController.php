<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BulkUpload;
use App\Models\UserQuota;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PropertyTemplateExport;
use App\Exports\GoodsTemplateExport;
use App\Exports\ServicesTemplateExport;
use App\Jobs\ProcessBulkUploadJob;
use Illuminate\Support\Facades\Auth;

class BulkListingController extends Controller
{
    public function index()
    {
        $uploads = BulkUpload::where('user_id', Auth::id())->latest()->paginate(10);
        return view('user.bulk-uploads.index', compact('uploads'));
    }

    public function downloadTemplate($type)
    {
        if ($type === 'property') {
            return Excel::download(new PropertyTemplateExport, 'template_properti.xlsx');
        } elseif ($type === 'goods') {
            return Excel::download(new GoodsTemplateExport, 'template_barang.xlsx');
        } elseif ($type === 'services') {
            return Excel::download(new ServicesTemplateExport, 'template_jasa.xlsx');
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:property,goods,services',
            'file' => 'required|file|max:20480'
        ]);

        $file = $request->file('file');
        
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return back()->withErrors(['file' => "The file must be a file of type: xlsx, xls, csv. (Got: '{$extension}')"]);
        }
        // Save File first to avoid Windows ZipArchive .tmp read errors
        // Use storeAs to guarantee the correct file extension is preserved
        $filename = \Illuminate\Support\Str::random(40) . '.' . $extension;
        $path = $file->storeAs('bulk_uploads', $filename, 'public');
        // IMPORTANT: Convert backslashes to forward slashes for PHP ZipArchive on Windows!
        $fullPath = str_replace('\\', '/', \Illuminate\Support\Facades\Storage::disk('public')->path($path));

        try {
            // Count rows directly using PhpSpreadsheet to completely bypass Maatwebsite/Excel's LocalTemporaryFile
            // This eliminates any possibility of the "zip member" temp file bug on Windows.
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();
            // Fix the "getHighestDataRow" bug that counts validations as data.
            // Loop through Column A (Judul Iklan) to find the actual last row with text.
            $highestRow = 1;
            foreach ($worksheet->getColumnIterator('A') as $column) {
                foreach ($column->getCellIterator(2) as $cell) {
                    $val = $cell->getValue();
                    if ($val !== null && trim((string)$val) !== '') {
                        $highestRow = max($highestRow, $cell->getRow());
                    }
                }
            }
            $totalRows = max(0, $highestRow - 1); // Exclude heading row
        } catch (\Exception $e) {
            // Hapus file jika gagal dibaca
            @unlink($fullPath);
            $err = $e->getMessage();
            return back()->withErrors(['file' => "Gagal membaca struktur ZIP Excel Anda. Jika Anda memakai LibreOffice/WPS, pastikan 'Save As' tipe 'Excel Workbook (*.xlsx)'. Error teknis: {$err}"]);
        }
        
        if ($totalRows <= 0) {
            @unlink($fullPath);
            return back()->withErrors(['file' => 'Tidak ada data baris iklan di dalam file (File Excel kosong).']);
        }

        $user = Auth::user();
        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $user, $totalRows, $fullPath, $path) {
        $quota = UserQuota::where('user_id', $user->id)->lockForUpdate()->first();
        $remainingQuota = $quota ? (int) $quota->listing_quota : 0;

        if ($remainingQuota !== -1 && $totalRows > $remainingQuota) {
            @unlink($fullPath);
            return back()->withErrors(['file' => "Sisa kuota iklan Anda hanya $remainingQuota, tetapi file berisi $totalRows baris iklan. Silakan kurangi isi file atau tambah kuota."]);
        }

        // Deduct Quota upfront
        if ($quota && $remainingQuota !== -1) {
            $quota->decrement('listing_quota', $totalRows);
        }

        // Create Record
        $bulkUpload = BulkUpload::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'file_path' => $path,
            'status' => 'pending',
            'total_rows' => $totalRows
        ]);

        // Dispatch Job
        ProcessBulkUploadJob::dispatch($bulkUpload)->afterCommit();

        return redirect()->route('bulk-uploads.index')->with('status', 'File berhasil di-upload! Iklan dan foto sedang diproses oleh sistem di belakang layar.');
        });
    }
}

