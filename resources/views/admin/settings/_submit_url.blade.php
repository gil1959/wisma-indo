<!-- TAB: SUBMIT URL (Google Indexing API Bulk Manager) -->
<div x-show="tab === 'submit_url'" x-cloak class="p-6 bg-slate-50 min-h-screen">
    
    <div class="mb-8">
        <h3 class="text-2xl font-extrabold text-slate-800 mb-2 flex items-center gap-2">
            <i data-lucide="monitor-up" class="w-6 h-6 text-[#0194F3]"></i> Google Indexing API Bulk Manager
        </h3>
        <p class="text-sm text-slate-500">Input puluhan hingga ratusan URL sekaligus untuk dipublikasikan atau dihapus dari index Google.</p>
    </div>

    <!-- SECTION 1: Statistik & Chart -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Card 1 -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total URL Disubmit</p>
            <h4 class="text-3xl font-extrabold text-slate-800 mb-2">{{ number_format($googleIndexingStats['total'] ?? 0) }}</h4>
            <p class="text-xs text-emerald-600 font-bold bg-emerald-50 inline-block px-2 py-1 rounded">
                <i data-lucide="trending-up" class="w-3 h-3 inline"></i> 100% dari akumulasi
            </p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-4 right-4 bg-emerald-100 text-emerald-600 p-2 rounded-lg">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Sukses (200 OK)</p>
            <h4 class="text-3xl font-extrabold text-slate-800 mb-2">{{ number_format($googleIndexingStats['success'] ?? 0) }}</h4>
            <p class="text-xs text-slate-500">Tingkat keberhasilan: <span class="font-bold text-emerald-600">{{ $googleIndexingStats['success_rate'] ?? 0 }}%</span></p>
        </div>
        <!-- Card 3 -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-4 right-4 bg-red-100 text-red-600 p-2 rounded-lg">
                <i data-lucide="x-circle" class="w-5 h-5"></i>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Gagal / Exceeded</p>
            <h4 class="text-3xl font-extrabold text-slate-800 mb-2">{{ number_format($googleIndexingStats['failed'] ?? 0) }}</h4>
            <p class="text-xs text-slate-500">Error 403, 429, atau Quota</p>
        </div>
        <!-- Card 4 -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-4 right-4 bg-amber-100 text-amber-600 p-2 rounded-lg">
                <i data-lucide="rss" class="w-5 h-5"></i>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Mode</p>
            <h4 class="text-xl font-extrabold text-slate-800 mb-2 mt-2">
                @if(App\Models\Setting::getValue('g_index_auth_mode') == 'oauth')
                    <span class="text-[#0194F3]">OAuth 2.0</span>
                @else
                    <span class="text-indigo-600">JSON File</span>
                @endif
            </h4>
            <p class="text-xs text-slate-500">Siap diekstrak & diping otomatis</p>
        </div>
    </div>

    <!-- CHART -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-8">
        <div class="flex items-center justify-between mb-6">
            <h4 class="font-bold text-slate-700 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-[#0194F3]"></i> Statistik Pengiriman URL
            </h4>
            <span class="px-3 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-full border border-slate-200">7 Hari Terakhir</span>
        </div>
        <div class="relative h-72 w-full">
            <canvas id="indexingChart"></canvas>
        </div>
    </div>

    <!-- SECTION 2: INDEXING -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- KOLOM KIRI (70%): Input Area & Config -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Konfigurasi API (JSON vs OAuth) -->
            <div x-data="{ authTab: '{{ App\Models\Setting::getValue('g_index_auth_mode', 'json') }}' }" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex flex-wrap border-b border-slate-200 bg-slate-50">
                    <button @click="authTab = 'json'" :class="authTab === 'json' ? 'border-b-2 border-indigo-600 text-indigo-700 bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Mode JSON (Rekomendasi)</button>
                    <button @click="authTab = 'oauth'" :class="authTab === 'oauth' ? 'border-b-2 border-[#0194F3] text-[#0194F3] bg-white' : 'text-slate-500 hover:text-slate-700'" class="px-6 py-4 font-bold text-sm">Mode OAuth 2.0</button>
                </div>

                <div class="p-6">
                    <!-- Mode JSON -->
                    <div x-show="authTab === 'json'">
                        <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2"><i data-lucide="key" class="w-4 h-4 text-amber-500"></i> Service Account Private Key (JSON File)</h4>
                        <p class="text-xs text-slate-500 mb-4">Untuk pengerjaan backend / key khusus Server-to-Server.</p>
                        <form action="{{ route('admin.settings.google_indexing.upload_json') }}" method="POST" enctype="multipart/form-data" class="flex gap-4 items-center">
                            @csrf
                            <input type="file" name="json_file" accept=".json" class="flex-grow text-sm rounded-xl border border-slate-300 p-2 bg-slate-50" required>
                            <button type="submit" class="bg-slate-800 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-slate-700 text-sm whitespace-nowrap">Upload JSON</button>
                        </form>
                        
                        @if(file_exists(storage_path('app/google/service-account.json')))
                            <p class="text-xs font-bold text-emerald-600 mt-3 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> File JSON saat ini sudah tersimpan dan aktif di Server.</p>
                        @elseif(file_exists(base_path('wismaindo-504816-f6014a87c478.json')))
                            <p class="text-xs font-bold text-emerald-600 mt-3 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> File JSON wismaindo (default) aktif.</p>
                        @endif
                    </div>

                    <!-- Mode OAuth -->
                    <div x-show="authTab === 'oauth'" x-cloak>
                        <h4 class="font-bold text-slate-700 mb-2 flex items-center gap-2"><i data-lucide="user-check" class="w-4 h-4 text-[#0194F3]"></i> Google OAuth Login</h4>
                        <p class="text-xs text-slate-500 mb-4">Pastikan akun Google Anda terdaftar sebagai Owner di Search Console property target.</p>
                        
                        <a href="{{ route('admin.settings.google_indexing.oauth_redirect') }}" class="inline-flex items-center justify-center gap-3 bg-white border border-slate-300 text-slate-700 font-bold px-6 py-2.5 rounded-xl hover:bg-slate-50 transition shadow-sm text-sm">
                            <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/><path d="M1 1h22v22H1z" fill="none"/></svg>
                            Hubungkan Akun Google
                        </a>

                        @if(App\Models\Setting::getValue('g_index_access_token'))
                            <p class="text-xs font-bold text-emerald-600 mt-3 flex items-center gap-1"><i data-lucide="check-circle" class="w-3 h-3"></i> Token OAuth valid & tersimpan. Auto-refresh aktif.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Bulk Form -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold text-slate-700 flex items-center gap-2">
                        <i data-lucide="list-ordered" class="w-5 h-5 text-[#0194F3]"></i> Daftar URL untuk Disubmit (1 URL Per Baris)
                    </h4>
                    <div class="flex gap-2">
                        <!-- Hidden input for file upload -->
                        <input type="file" id="importTxtBtn" accept=".txt,.csv" class="hidden">
                        <button type="button" onclick="document.getElementById('importTxtBtn').click()" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded text-xs font-bold hover:bg-slate-200 flex items-center gap-1 border border-slate-200">
                            <i data-lucide="file-input" class="w-3 h-3"></i> Import TXT/CSV
                        </button>
                        <button type="button" onclick="document.getElementById('urlsToSubmit').value = ''; updateUrlCount();" class="bg-red-50 text-red-600 px-3 py-1.5 rounded text-xs font-bold hover:bg-red-100 flex items-center gap-1 border border-red-200">
                            <i data-lucide="trash-2" class="w-3 h-3"></i> Bersihkan
                        </button>
                    </div>
                </div>
                
                <textarea id="urlsToSubmit" rows="10" class="w-full rounded-xl border-slate-300 font-mono text-sm bg-slate-50 flex-grow mb-4 whitespace-nowrap overflow-x-auto p-4" placeholder="https://example.com/blog/pos-1&#10;https://example.com/blog/pos-2&#10;https://example.com/produk/sepatu-laris&#10;https://example.com/halaman-baru"></textarea>
                
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                    <p class="text-sm font-bold text-slate-500">Total URL terdeteksi: <span id="urlCount" class="text-2xl font-black text-[#0194F3]">0</span></p>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-bold text-slate-500">Tipe Aksi:</label>
                        <select id="actionType" class="rounded-xl border-slate-300 text-sm font-bold text-slate-700 bg-slate-50">
                            <option value="URL_UPDATED">Publish / Update Index (URL_UPDATED)</option>
                            <option value="URL_DELETED">Hapus Dari Index (URL_DELETED)</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-amber-50 border border-amber-200 p-4 rounded-xl mb-6">
                    <div class="text-amber-500"><i data-lucide="clock" class="w-5 h-5"></i></div>
                    <div class="flex-grow">
                        <label class="block text-xs font-bold text-amber-700 mb-1">Delay antar request API (mencegah Rate Limit 429): <span id="delayValueLabel">500ms</span></label>
                        <input type="range" id="delaySlider" min="100" max="2000" step="100" value="500" class="w-full h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer">
                    </div>
                </div>

                <button type="button" id="btnStartBulk" class="w-full bg-gradient-to-r from-[#0194F3] to-indigo-600 text-white px-4 py-4 rounded-xl font-black text-lg hover:opacity-90 transition flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30">
                    <i data-lucide="rocket" class="w-5 h-5"></i> JALANKAN INDEXING MASSAL SEKARANG
                </button>
            </div>
        </div>

        <!-- KOLOM KANAN (30%): Eksekusi Batch & Quota -->
        <div class="space-y-6">
            
            <!-- Status Eksekusi Batch -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-slate-100" id="progressTrack">
                    <div class="h-full bg-[#0194F3] w-0 transition-all duration-300" id="progressBar"></div>
                </div>
                
                <h4 class="font-bold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="loader" class="w-5 h-5 text-[#0194F3]" id="statusIcon"></i> Status Eksekusi Batch
                </h4>
                
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-slate-500" id="statusText">Menunggu Antrean...</p>
                    <p class="text-xs font-black text-[#0194F3]" id="progressPercentage">0%</p>
                </div>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl text-center">
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider mb-1">Proses Sukses</p>
                        <h4 class="text-3xl font-black text-emerald-600" id="countSuccess">0</h4>
                    </div>
                    <div class="bg-red-50 border border-red-100 p-3 rounded-xl text-center">
                        <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-1">Proses Gagal</p>
                        <h4 class="text-3xl font-black text-red-600" id="countFail">0</h4>
                    </div>
                </div>
                
                <button type="button" id="btnStopBulk" disabled class="w-full bg-slate-100 text-slate-400 px-4 py-2 rounded-lg font-bold text-sm cursor-not-allowed transition flex items-center justify-center gap-1 border border-slate-200">
                    <i data-lucide="square" class="w-3 h-3"></i> Hentikan Proses
                </button>
            </div>

            <!-- Access Token Status / Quota -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h4 class="font-bold text-slate-700 flex items-center gap-2 mb-4">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i> Kuota & Keamanan
                </h4>
                
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl text-sm mb-4">
                    <p class="font-bold text-slate-700 mb-1">Sisa Kuota Hari Ini:</p>
                    @php
                        $used = $googleIndexingStats['quota_used'] ?? 0;
                        $limit = $googleIndexingStats['quota_limit'] ?? 200;
                        $remaining = max(0, $limit - $used);
                        $percentage = $limit > 0 ? ($used / $limit) * 100 : 0;
                        $colorClass = $remaining < 20 ? 'bg-red-500' : 'bg-emerald-500';
                        $textClass = $remaining < 20 ? 'text-red-600' : 'text-emerald-600';
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl font-black {{ $textClass }}">{{ $remaining }}</span>
                        <span class="text-xs font-bold text-slate-400">/ {{ $limit }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 mb-2">
                        <div class="{{ $colorClass }} h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-500">Google membatasi maksimal 200 URL per hari per <i>Service Account</i> / Proyek. (Di-reset tiap hari)</p>
                </div>
                
                <p class="text-[10px] text-slate-400 leading-relaxed text-justify">
                    Access token dan request ini disalurkan secara aman langsung melalui Server (Server-Side Request) menuju Google API Endpoint. 
                    Anda tidak perlu khawatir mengenai ekspos kredensial API.
                </p>
            </div>
            
        </div>
    </div>
    
    <!-- SECTION 3: LOG TABLE -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col mb-8">
        <div class="p-6 border-b border-slate-200 flex justify-between items-center bg-slate-50">
            <h4 class="font-bold text-slate-700 flex items-center gap-2">
                <i data-lucide="history" class="w-5 h-5 text-[#0194F3]"></i> Riwayat Submit Terakhir
            </h4>
            <a href="javascript:void(0)" onclick="window.location.reload()" class="text-xs text-[#0194F3] bg-blue-50 px-3 py-1 rounded-full font-bold hover:bg-blue-100 transition">Refresh Data</a>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">URL</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pesan API</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($googleIndexingLogs ?? [] as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-4 text-sm text-slate-700 break-all max-w-xs"><a href="{{ $log->url }}" target="_blank" class="text-[#0194F3] hover:underline">{{ $log->url }}</a></td>
                        <td class="p-4 text-xs font-mono font-bold text-slate-500">{{ $log->action_type }}</td>
                        <td class="p-4 text-center">
                            @if($log->status_code == 200)
                                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded-lg text-xs font-bold">200 OK</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-xs font-bold">{{ $log->status_code ?? 'ERR' }}</span>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-slate-500">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="p-4 text-xs text-slate-500 max-w-xs truncate" title="{{ $log->response_message }}">{{ $log->response_message }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 text-sm">Belum ada data riwayat submit.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // ==== CHART.JS INIT ====
        const ctx = document.getElementById('indexingChart');
        if(ctx) {
            const labels = @json($googleIndexingStats['chart_labels'] ?? []);
            const dataSuccess = @json($googleIndexingStats['chart_success'] ?? []);
            const dataFailed = @json($googleIndexingStats['chart_failed'] ?? []);
            
            labels.reverse();
            dataSuccess.reverse();
            dataFailed.reverse();

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Sukses (200)',
                            data: dataSuccess,
                            backgroundColor: '#10B981',
                            borderRadius: 4
                        },
                        {
                            label: 'Gagal',
                            data: dataFailed,
                            backgroundColor: '#EF4444',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                        y: { grid: { borderDash: [5, 5] }, ticks: { font: { size: 10 }, stepSize: 1, beginAtZero: true } }
                    },
                    plugins: { legend: { position: 'top', labels: { boxWidth: 8, font: { size: 11 } } } }
                }
            });
        }


        // ==== BULK MANAGER LOGIC ====
        const textareaUrls = document.getElementById('urlsToSubmit');
        const urlCountDisplay = document.getElementById('urlCount');
        const delaySlider = document.getElementById('delaySlider');
        const delayValueLabel = document.getElementById('delayValueLabel');
        const importBtn = document.getElementById('importTxtBtn');
        
        const btnStart = document.getElementById('btnStartBulk');
        const btnStop = document.getElementById('btnStopBulk');
        const progressBar = document.getElementById('progressBar');
        const progressPercentage = document.getElementById('progressPercentage');
        const statusText = document.getElementById('statusText');
        const statusIcon = document.getElementById('statusIcon');
        const countSuccess = document.getElementById('countSuccess');
        const countFail = document.getElementById('countFail');
        const actionType = document.getElementById('actionType');
        
        // Quota
        const remainingQuota = parseInt("{{ max(0, ($googleIndexingStats['quota_limit'] ?? 200) - ($googleIndexingStats['quota_used'] ?? 0)) }}");

        let stopFlag = false;
        
        // Update Total URLs count
        window.updateUrlCount = function() {
            const text = textareaUrls.value;
            const urls = text.split('\n').map(u => u.trim()).filter(u => u.length > 5);
            urlCountDisplay.innerText = urls.length;
        };
        textareaUrls.addEventListener('input', updateUrlCount);

        // Slider UI
        delaySlider.addEventListener('input', function() {
            delayValueLabel.innerText = this.value + 'ms';
        });

        // Import File
        importBtn.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const existing = textareaUrls.value.trim();
                textareaUrls.value = existing ? existing + '\n' + e.target.result : e.target.result;
                updateUrlCount();
            };
            reader.readAsText(file);
            this.value = ''; // Reset input
        });

        // Sleep Promise Helper
        const sleep = ms => new Promise(r => setTimeout(r, ms));

        // Start Process
        btnStart.addEventListener('click', async function() {
            if (remainingQuota <= 0) {
                return Swal.fire('Limit Tercapai', 'Kuota harian Google Indexing Anda sudah habis. Coba lagi besok!', 'error');
            }

            const rawText = textareaUrls.value;
            const urls = rawText.split('\n').map(u => u.trim()).filter(u => u.length > 5);
            
            if (urls.length === 0) {
                return Swal.fire('Kosong!', 'Tidak ada URL valid yang ditemukan.', 'warning');
            }

            if (urls.length > remainingQuota) {
                return Swal.fire('URL Terlalu Banyak', `Anda mencoba submit ${urls.length} URL, tapi sisa kuota hari ini hanya ${remainingQuota}. Harap kurangi URL.`, 'warning');
            }

            const result = await Swal.fire({
                title: 'Mulai Batch Proses?',
                text: `Sistem akan mengirim ${urls.length} URL secara sekuensial dengan delay ${delaySlider.value}ms. Pastikan halaman tidak ditutup selama proses!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0194F3',
                confirmButtonText: 'Ya, Jalankan!'
            });

            if (!result.isConfirmed) return;

            // Setup UI for running state
            stopFlag = false;
            btnStart.disabled = true;
            btnStart.innerHTML = '<i data-lucide="loader" class="w-5 h-5 animate-spin"></i> PROSES BERJALAN...';
            btnStart.classList.add('opacity-50', 'cursor-not-allowed');
            
            btnStop.disabled = false;
            btnStop.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            btnStop.classList.add('bg-red-500', 'text-white', 'hover:bg-red-600', 'shadow-md');
            
            textareaUrls.disabled = true;
            statusIcon.classList.add('animate-spin');
            
            let success = 0;
            let fail = 0;
            const total = urls.length;
            const msDelay = parseInt(delaySlider.value);
            const action = actionType.value;

            // Loop batch
            for (let i = 0; i < total; i++) {
                if (stopFlag) {
                    statusText.innerText = 'Dihentikan paksa oleh pengguna.';
                    break;
                }

                statusText.innerText = `Memproses [${i+1}/${total}]: ${urls[i].substring(0, 30)}...`;
                
                try {
                    // We send batch 1 URL at a time using backend
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('urls', urls[i]);
                    formData.append('action_type', action);

                    const response = await fetch('{{ route("admin.settings.google_indexing.submit") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    
                    if (data.success && data.message.includes('1 sukses')) {
                        success++;
                        countSuccess.innerText = success;
                    } else {
                        fail++;
                        countFail.innerText = fail;
                    }
                } catch (e) {
                    fail++;
                    countFail.innerText = fail;
                }

                // Update Progress
                let pct = Math.round(((i + 1) / total) * 100);
                progressBar.style.width = pct + '%';
                progressPercentage.innerText = pct + '%';

                // Delay to prevent 429 Limit
                if (i < total - 1 && !stopFlag) {
                    await sleep(msDelay);
                }
            }

            // Finish State
            btnStart.disabled = false;
            btnStart.innerHTML = '<i data-lucide="rocket" class="w-5 h-5"></i> JALANKAN INDEXING MASSAL SEKARANG';
            btnStart.classList.remove('opacity-50', 'cursor-not-allowed');
            
            btnStop.disabled = true;
            btnStop.classList.add('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
            btnStop.classList.remove('bg-red-500', 'text-white', 'hover:bg-red-600', 'shadow-md');
            
            textareaUrls.disabled = false;
            statusIcon.classList.remove('animate-spin');
            
            if (!stopFlag) {
                statusText.innerText = 'Proses Selesai!';
                Swal.fire('Selesai!', `Proses Bulk Indexing selesai. Sukses: ${success}, Gagal: ${fail}`, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Dihentikan!', `Proses disetop. Sukses: ${success}, Gagal: ${fail}`, 'info');
            }

            if(typeof lucide !== 'undefined') lucide.createIcons();
        });

        // Stop Process
        btnStop.addEventListener('click', function() {
            stopFlag = true;
            this.innerHTML = '<i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Menyetop...';
        });
        
    });
</script>
@endpush
