@extends('admin.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header: title + search + date range -->
        <div class="flex flex-col gap-4">
            <div>
                <h1 class="text-2xl font-extrabold">Analitik & Laporan</h1>
                <p class="mt-1 text-sm text-stone-600">Laporan & Analitik Data</p>
            </div>
            <div class="flex flex-col md:flex-row gap-3">
                <div class="flex-1 rounded-2xl ring-1 ring-stone-200 bg-white px-4 py-3 flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-stone-500"><circle cx="11" cy="11" r="7" stroke-width="1.5"/><path d="M20 20l-3-3" stroke-width="1.5"/></svg>
                    <input id="rec-search" type="text" placeholder="Cari rekomendasi..." class="w-full bg-transparent outline-none text-sm" />
                </div>
                <button id="btn-date-range" class="rounded-2xl ring-1 ring-stone-200 bg-white px-4 py-3 text-sm flex items-center gap-2">
                    <span id="date-range-label">Semua tanggal</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-4 w-4 text-stone-600"><path d="M7 10l5 5 5-5" stroke-width="1.5"/></svg>
                </button>
            </div>
        </div>

        <!-- Quick cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-full grid place-items-center bg-pink-50 ring-1 ring-pink-200 text-pink-600">✂️</div>
                    <div>
                        <p class="font-semibold">{{ $topModel ?? 'Oval Layer With Curtain Bangs' }}</p>
                        <p class="text-xs text-stone-600">Rekomendasi Model Terpopuler</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-full grid place-items-center bg-pink-50 ring-1 ring-pink-200 text-pink-600">💊</div>
                    <div>
                        <p class="font-semibold">{{ $topVitamin ?? 'Vitamin A' }}</p>
                        <p class="text-xs text-stone-600">Vitamin Terpopuler</p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-full grid place-items-center bg-pink-50 ring-1 ring-pink-200 text-pink-600">📦</div>
                    <div>
                        <p class="font-extrabold text-xl">{{ number_format($recs->count()) }}</p>
                        <p class="text-xs text-stone-600">Total Rekomendasi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm h-64 overflow-hidden">
                <p class="text-sm font-semibold text-stone-700 tracking-wide">Analitik Penggunaan Sistem</p>
                <div class="mt-3 h-full">
                    <canvas id="usageLine" class="w-full h-full"></canvas>
                </div>
            </div>
            <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm h-64 overflow-hidden">
                <p class="text-sm font-semibold text-stone-700 tracking-wide">Distribusi Bentuk Muka</p>
                <div class="mt-3 h-full">
                    <canvas id="facePie" class="w-full h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Recommendation list -->
        <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm">
            <p class="text-sm font-semibold text-stone-700 tracking-wide">Vitamin Paling Direkomendasikan</p>
            <div class="mt-4 grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    @forelse(($topVitamins ?? []) as $index => $vitamin)
                        <div class="flex items-center gap-3">
                            <span class="h-6 w-6 grid place-items-center rounded-md bg-pink-100 text-pink-700 text-xs">{{ $index + 1 }}</span>
                            <span>{{ $vitamin['name'] }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-stone-500">Belum ada data vitamin</div>
                    @endforelse
                </div>
                <div class="space-y-2 text-stone-600">
                    @forelse(($topVitamins ?? []) as $vitamin)
                        <p>{{ number_format($vitamin['count']) }} pemakaian</p>
                    @empty
                        <div class="text-sm text-stone-500">-</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Data Rekomendasi -->
        <div class="rounded-2xl bg-white ring-1 ring-stone-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-stone-700 tracking-wide">Data Rekomendasi</p>
            </div>

            <!-- Wrapper berisi kontrol & tabel (tampil langsung, tanpa form) -->
            <div id="rec-wrapper" class="mt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button id="btn-export-csv" class="px-4 py-2 rounded-full ring-1 ring-stone-300 bg-white text-sm hover:bg-stone-50 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-stone-700"><path d="M12 3v12" stroke-width="1.5"/><path d="M8 11l4 4 4-4" stroke-width="1.5"/><rect x="4" y="19" width="16" height="2" rx="1"/></svg>
                            <span>Excel</span>
                        </button>
                        <button id="btn-print-pdf" class="px-4 py-2 rounded-full ring-1 ring-stone-300 bg-white text-sm hover:bg-stone-50 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-stone-700"><rect x="6" y="4" width="12" height="16" rx="2" stroke-width="1.5"/><path d="M8 8h8M8 12h8M8 16h8" stroke-width="1.5"/></svg>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>

                <!-- Controls removed: gunakan kontrol di header atas -->

                <!-- Table -->
                <div class="mt-4 overflow-x-auto">
                    <table id="rec-table" class="min-w-full table-auto">
                        <thead>
                            <tr class="text-left text-sm text-stone-700">
                                <th class="px-4 py-2">No</th>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Nama</th>
                                <th class="px-4 py-2">Nomor Telepon</th>
                                <th class="px-4 py-2">Tipe Rambut</th>
                                <th class="px-4 py-2">Model Rekomendasi</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                        @php $no = 1; @endphp
                        @forelse(($recs ?? []) as $rec)
                            @php
                                $tanggal = optional($rec->created_at)->format('d/m/Y');
                                // Cari vitamin yang cocok berdasarkan hair_condition (case-insensitive + trim)
                                $recommendedVitamin = '-';
                                if ($rec->hair_condition) {
                                    $hairCondition = trim($rec->hair_condition);
                                    // Coba exact match dulu
                                    $vitamin = App\Models\HairVitamin::whereRaw('LOWER(TRIM(hair_type)) = ?', [strtolower($hairCondition)])->first();
                                    // Jika tidak ketemu, coba match tanpa case-sensitive
                                    if (!$vitamin) {
                                        $vitamin = App\Models\HairVitamin::where('hair_type', 'like', '%' . $hairCondition . '%')->first();
                                    }
                                    $recommendedVitamin = $vitamin ? $vitamin->name : '-';
                                }
                                $detail = [
                                    'tanggal' => $tanggal,
                                    'nama' => $rec->name,
                                    'kontak' => $rec->phone,
                                    'wajah' => $rec->face_shape,
                                    'panjang' => $rec->hair_length,
                                    'jenis' => $rec->hair_type,
                                    'tipe' => $rec->hair_condition,
                                    'model' => $rec->recommended_models,
                                    'vitamin' => $recommendedVitamin,
                                ];
                            @endphp
                            <tr class="hover:bg-pink-50" data-date="{{ $tanggal }}" data-detail='@json($detail)'>
                                <td class="px-4 py-2">{{ $no++ }}</td>
                                <td class="px-4 py-2">{{ $tanggal }}</td>
                                <td class="px-4 py-2">{{ $rec->name }}</td>
                                <td class="px-4 py-2">{{ $rec->phone }}</td>
                                <td class="px-4 py-2">{{ $rec->hair_condition ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $rec->recommended_models ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    <button class="btn-rec-show" title="Show">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-amber-500">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke-width="1.5"/>
                                            <circle cx="12" cy="12" r="3" stroke-width="1.5"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-stone-600">Belum ada data rekomendasi. Lakukan scan untuk menghasilkan data.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

            <!-- Show more -->
            <div class="mt-3 flex justify-end">
                <button id="btn-show-all" class="px-3 py-1 rounded-xl ring-1 ring-stone-200 bg-white text-sm hover:bg-stone-50">Tampilkan semua</button>
            </div>
            <!-- end wrapper -->
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const colors = {
            pink: 'rgba(244, 114, 182, 0.8)',
            pinkSoft: 'rgba(244, 114, 182, 0.25)',
            amber: 'rgba(251, 191, 36, 0.8)',
            blue: 'rgba(59, 130, 246, 0.8)',
            green: 'rgba(52, 211, 153, 0.8)'
        };
        // Chart instances
        let usageChart = null;
        let faceChart = null;

        // Line usage (will be filled from filtered table rows)
        const usageCtx = document.getElementById('usageLine');
        if (usageCtx) {
            usageChart = new Chart(usageCtx, {
                type: 'line',
                data: { labels: [], datasets: [{ label: 'Penggunaan', data: [], tension: 0.4, borderColor: colors.green, backgroundColor: 'transparent' }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    transitions: { active: { animation: { duration: 0 } } },
                    plugins: { legend: { display: false } },
                    layout: { padding: { left: 8, right: 8, top: 8, bottom: 8 } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true } },
                        y: {
                            grid: { display: false },
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0 }
                        }
                    }
                }
            });
        }

        // Pie face shape (distribution from filtered rows)
        const faceCtx = document.getElementById('facePie');
        if (faceCtx) {
            faceChart = new Chart(faceCtx, {
                type: 'doughnut',
                data: { labels: [], datasets: [{ data: [], backgroundColor: [colors.pink, colors.amber, colors.blue, colors.green], borderColor: '#fff', borderWidth: 2 }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    transitions: { active: { animation: { duration: 0 } } },
                    layout: { padding: { left: 8, right: 8, top: 8, bottom: 8 } },
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, color: '#525252', font: { size: 11 } } } },
                    cutout: '60%'
                }
            });
        }

        // ====== Helpers ======
        function parseIndoDate(str) {
            // format: dd/mm/yyyy
            const [d,m,y] = str.split('/').map(Number);
            return new Date(y, m-1, d);
        }

        // ====== Table: search & date range filter & show more ======
        document.addEventListener('DOMContentLoaded', function() {
            // elements
            const recWrapper = document.getElementById('rec-wrapper');

            const tbody = document.querySelector('#rec-table tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const searchInput = document.getElementById('rec-search');
            const showAllBtn = document.getElementById('btn-show-all');
            const dateBtn = document.getElementById('btn-date-range');
            const dateLabel = document.getElementById('date-range-label');

            let showLimit = 5; // tampilkan sebagian 
            let filterStart = null; let filterEnd = null;

            function matchDate(row) {
                if (!filterStart || !filterEnd) return true;
                const d = parseIndoDate(row.getAttribute('data-date'));
                return d >= filterStart && d <= filterEnd;
            }
            function matchText(row, q) {
                if (!q) return true;
                const text = row.textContent.toLowerCase();
                return text.includes(q.toLowerCase());
            }
            function recomputeCharts() {
                // build datasets from visible rows
                const visibleRows = rows.filter(r => !r.classList.contains('hidden'));
                // Usage: count per date
                const dateMap = new Map();
                visibleRows.forEach(r => {
                    const d = r.getAttribute('data-date');
                    dateMap.set(d, (dateMap.get(d) || 0) + 1);
                });
                const labels = Array.from(dateMap.keys()).sort((a,b)=>{
                    const pa = parseIndoDate(a).getTime();
                    const pb = parseIndoDate(b).getTime();
                    return pa - pb;
                });
                const values = labels.map(l => dateMap.get(l));
                if (usageChart) {
                    const maxY = values.length ? Math.max(...values) : 0;
                    usageChart.data.labels = labels;
                    usageChart.data.datasets[0].data = values;
                    // keep y axis clean with integer steps
                    usageChart.options.scales.y.beginAtZero = true;
                    usageChart.options.scales.y.ticks = { stepSize: 1, precision: 0 };
                    usageChart.options.scales.y.suggestedMax = Math.max(1, maxY + 1);
                    usageChart.update();
                }

                // Face distribution
                const faceMap = new Map();
                visibleRows.forEach(r => {
                    const det = JSON.parse(r.getAttribute('data-detail'));
                    const f = (det?.wajah || '-').trim();
                    faceMap.set(f, (faceMap.get(f) || 0) + 1);
                });
                const faceLabels = Array.from(faceMap.keys());
                const faceValues = faceLabels.map(l => faceMap.get(l));
                if (faceChart) {
                    faceChart.data.labels = faceLabels;
                    faceChart.data.datasets[0].data = faceValues;
                    faceChart.update();
                }
            }

            function applyFilters() {
                const q = searchInput?.value || '';
                let visibleCount = 0;
                rows.forEach((row) => {
                    const ok = matchText(row, q) && matchDate(row);
                    if (!ok) { row.classList.add('hidden'); return; }
                    visibleCount++;
                    if (showLimit && visibleCount > showLimit) row.classList.add('hidden'); else row.classList.remove('hidden');
                });
                recomputeCharts();
            }

            // init
            applyFilters();

            // tanpa form gate: wrapper sudah tampil dari awal

            // live search
            if (searchInput) searchInput.addEventListener('input', applyFilters);

            // show all
            if (showAllBtn) showAllBtn.addEventListener('click', function(){
                showLimit = null; applyFilters();
                showAllBtn.classList.add('hidden');
            });

            // popup date range (simple)
            const popup = document.createElement('div');
            popup.id = 'popup-date-range';
            popup.className = 'fixed inset-0 z-50 hidden';
            popup.innerHTML = `
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" data-close></div>
                <div class="absolute inset-0 flex items-start justify-center mt-24 p-4">
                  <div class="w-full max-w-sm rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl p-4">
                    <p class="font-semibold mb-2">Pilih Rentang Tanggal</p>
                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="text-xs">Dari</label>
                        <input type="date" id="start-date" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" />
                      </div>
                      <div>
                        <label class="text-xs">Sampai</label>
                        <input type="date" id="end-date" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" />
                      </div>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                      <button class="px-2 py-1 rounded-lg ring-1 ring-stone-200 bg-stone-50" data-range="today">Today</button>
                      <button class="px-2 py-1 rounded-lg ring-1 ring-stone-200 bg-stone-50" data-range="yesterday">Yesterday</button>
                      <button class="px-2 py-1 rounded-lg ring-1 ring-stone-200 bg-stone-50" data-range="lastweek">Last week</button>
                      <button class="px-2 py-1 rounded-lg ring-1 ring-stone-200 bg-stone-50" data-range="lastmonth">Last month</button>
                    </div>
                    <div class="mt-3 flex justify-between">
                      <button id="btn-reset-date" class="text-xs px-2 py-1 rounded-lg ring-1 ring-stone-200 bg-stone-50">Reset</button>
                      <div class="flex gap-2">
                        <button class="px-3 py-1 rounded-xl ring-1 ring-stone-200 bg-stone-50" data-close>Cancel</button>
                        <button id="btn-apply-date" class="px-3 py-1 rounded-xl bg-pink-600 text-white">Apply</button>
                      </div>
                    </div>
                  </div>
                </div>`;
            document.body.appendChild(popup);

            function openPopup(){ popup.classList.remove('hidden'); }
            function closePopup(){ popup.classList.add('hidden'); }
            popup.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click', closePopup));
            if (dateBtn) dateBtn.addEventListener('click', openPopup);

            popup.querySelectorAll('[data-range]').forEach(btn=>{
                btn.addEventListener('click', ()=>{
                    const now = new Date();
                    let s, e;
                    const id = btn.getAttribute('data-range');
                    if (id==='today'){ s=new Date(now.getFullYear(),now.getMonth(),now.getDate()); e=new Date(s); }
                    else if (id==='yesterday'){ e=new Date(now.getFullYear(),now.getMonth(),now.getDate()-1); s=new Date(e); }
                    else if (id==='lastweek'){ e=new Date(now); s=new Date(now); s.setDate(s.getDate()-7); }
                    else if (id==='lastmonth'){ e=new Date(now); s=new Date(now); s.setMonth(s.getMonth()-1); }
                    const sd = document.getElementById('start-date');
                    const ed = document.getElementById('end-date');
                    sd.value = s.toISOString().slice(0,10);
                    ed.value = e.toISOString().slice(0,10);

                    // Auto-apply setelah memilih preset
                    filterStart = new Date(sd.value); filterStart.setHours(0,0,0,0);
                    filterEnd = new Date(ed.value); filterEnd.setHours(23,59,59,999);
                    dateLabel.textContent = `${s.toLocaleDateString('id-ID')} – ${e.toLocaleDateString('id-ID')}`;
                    closePopup(); applyFilters();
                });
            });
            document.getElementById('btn-reset-date').addEventListener('click', ()=>{
                document.getElementById('start-date').value='';
                document.getElementById('end-date').value='';
                // Auto-clear filter
                filterStart=null; filterEnd=null; dateLabel.textContent='Semua tanggal';
                closePopup(); applyFilters();
            });

            // Auto-apply ketika kedua input tanggal terisi
            function autoApplyDate(){
                const sd = document.getElementById('start-date').value;
                const ed = document.getElementById('end-date').value;
                if (sd && ed){
                    filterStart = new Date(sd); filterStart.setHours(0,0,0,0);
                    filterEnd = new Date(ed); filterEnd.setHours(23,59,59,999);
                    dateLabel.textContent = `${new Date(sd).toLocaleDateString('id-ID')} – ${new Date(ed).toLocaleDateString('id-ID')}`;
                    closePopup(); applyFilters();
                }
            }
            document.getElementById('start-date').addEventListener('change', autoApplyDate);
            document.getElementById('end-date').addEventListener('change', autoApplyDate);
            document.getElementById('btn-apply-date').addEventListener('click', ()=>{
                const sd = document.getElementById('start-date').value;
                const ed = document.getElementById('end-date').value;
                if (sd && ed){
                    filterStart = new Date(sd); filterStart.setHours(0,0,0,0);
                    filterEnd = new Date(ed); filterEnd.setHours(23,59,59,999);
                    dateLabel.textContent = `${new Date(sd).toLocaleDateString('id-ID')} – ${new Date(ed).toLocaleDateString('id-ID')}`;
                } else { filterStart=null; filterEnd=null; dateLabel.textContent='Semua tanggal'; }
                closePopup(); applyFilters();
            });

            // Row detail modal (show pop-up)
            const detailModal = document.createElement('div');
            detailModal.id = 'modal-rec-detail';
            detailModal.className = 'fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-50';
            detailModal.innerHTML = `
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-md rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl">
                        <div class="px-6 py-4 border-b"><h3 class="font-bold text-lg">Detail Data Rekomendasi</h3></div>
                        <div class="px-6 py-5 text-sm">
                            <div class="grid grid-cols-2 gap-y-2">
                                <div class="text-stone-600">Tanggal</div><div id="det-tanggal" class="font-medium"></div>
                                <div class="text-stone-600">Nama</div><div id="det-nama" class="font-medium"></div>
                                <div class="text-stone-600">Nomor Telepon</div><div id="det-kontak" class="font-medium"></div>
                                <div class="text-stone-600">Bentuk Wajah</div><div id="det-wajah" class="font-medium"></div>
                                <div class="text-stone-600">Panjang Rambut</div><div id="det-panjang" class="font-medium"></div>
                                <div class="text-stone-600">Jenis Rambut</div><div id="det-jenis" class="font-medium"></div>
                                <div class="text-stone-600">Tipe Rambut</div><div id="det-tipe" class="font-medium"></div>
                                <div class="text-stone-600">Vitamin Rambut</div><div id="det-vitamin" class="font-medium"></div>
                                <div class="text-stone-600">Model Direkomendasikan</div><div id="det-model" class="font-medium"></div>
                            </div>
                        </div>
                        <div class="px-6 pb-5">
                            <button class="w-full px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700" data-close>Kembali</button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(detailModal);
            detailModal.querySelectorAll('[data-close]').forEach(b=>b.addEventListener('click', ()=>detailModal.classList.add('hidden')));
            document.querySelectorAll('.btn-rec-show').forEach(btn=>{
                btn.addEventListener('click', (e)=>{
                    e.stopPropagation();
                    const row = btn.closest('tr');
                    const det = JSON.parse(row.getAttribute('data-detail'));
                    document.getElementById('det-tanggal').textContent = det.tanggal || row.getAttribute('data-date') || '-';
                    document.getElementById('det-nama').textContent = det.nama || '-';
                    document.getElementById('det-kontak').textContent = det.kontak || '-';
                    document.getElementById('det-wajah').textContent = det.wajah || '-';
                    document.getElementById('det-panjang').textContent = det.panjang || '-';
                    document.getElementById('det-jenis').textContent = det.jenis || '-';
                    document.getElementById('det-tipe').textContent = det.tipe || '-';
                    document.getElementById('det-model').textContent = det.model || '-';
                    document.getElementById('det-vitamin').textContent = det.vitamin || '-';
                    detailModal.classList.remove('hidden');
                });
            });

            // Export CSV
            function downloadCSV(){
                const headers = Array.from(document.querySelector('#rec-table thead tr').children).map(th=>th.textContent.trim());
                const data = Array.from(document.querySelectorAll('#rec-table tbody tr:not(.hidden)'))
                    .map(tr=>Array.from(tr.children).map(td=>`"${td.textContent.trim()}"`).join(','))
                    .join('\n');
                const csv = headers.join(',')+'\n'+data;
                const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url; a.download = 'data-rekomendasi.csv'; a.click();
                URL.revokeObjectURL(url);
            }
            document.getElementById('btn-export-csv').addEventListener('click', function(){
                const q = document.getElementById('rec-search')?.value || '';
                const url = `{{ route('admin.reports.export.excel') }}` + (q ? (`?q=`+encodeURIComponent(q)) : '');
                window.location.href = url;
            });

            // Print PDF (table only)
            document.getElementById('btn-print-pdf').addEventListener('click', ()=>{
                const q = document.getElementById('rec-search')?.value || '';
                const url = `{{ route('admin.reports.export.pdf') }}` + (q ? (`?q=`+encodeURIComponent(q)) : '');
                window.location.href = url;
            });
        });
    </script>
@endsection