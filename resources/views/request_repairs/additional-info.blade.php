{{-- ════════════════════════════════════════════════════
    Modal: Additional Info → On Trial
    File: resources/views/request_repairs/additional-info.blade.php
════════════════════════════════════════════════════ --}}
<div id="additionalInfoModal"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    onclick="handleAdditionalInfoBackdrop(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div id="additionalInfoContent"
        class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-[0_25px_60px_rgba(0,0,0,0.3)]
               transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="relative bg-zinc-900 px-6 py-5 flex-shrink-0">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 border border-zinc-700">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-white">Konfirmasi ke On Trial</h3>
                        <p id="additionalInfoNo" class="mt-1 text-sm font-mono text-zinc-400"></p>
                    </div>
                </div>
                <button onclick="closeAdditionalInfoModal()"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body (scrollable) --}}
        <div class="overflow-y-auto flex-1 px-6 py-6 space-y-6">

            {{-- Durasi --}}
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                <div class="mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-100">
                            <svg class="h-5 w-5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-600">Durasi Repair</p>
                            <p class="text-xs text-zinc-500">Status: On Process</p>
                        </div>
                    </div>
                    <div id="durasiLiveBadge" class="flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-zinc-600"></span>
                        <span class="text-xs font-medium text-zinc-600">Live</span>
                    </div>
                </div>
                <p id="durasiDisplay" class="font-mono text-3xl font-bold tracking-tight text-zinc-800">Menghitung...</p>
                <p class="mt-2 text-xs text-zinc-500">Dihitung sejak request dibuat · otomatis berhenti saat submit · waktu pause tidak dihitung</p>

                {{-- Banner alasan pause, muncul kalau sedang paused --}}
                <div id="pauseReasonBanner" class="hidden mt-3 flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 py-2.5">
                    <svg class="h-4 w-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 5h4v14H6V5zm8 0h4v14h-4V5z"/>
                    </svg>
                    <p class="text-xs font-semibold text-red-700">Sedang di-pause: <span id="pauseReasonText">-</span></p>
                </div>

                {{-- Tombol Pause / Resume --}}
                <div class="mt-4 flex items-center gap-2">
                    <button type="button" id="pauseBtn" onclick="togglePausePanel()"
                        class="flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-4 py-2 text-xs font-semibold text-zinc-700 transition hover:bg-zinc-100">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 5h4v14H6V5zm8 0h4v14h-4V5z"/></svg>
                        Pause
                    </button>
                    <button type="button" id="resumeBtn" onclick="resumeRepair()"
                        class="hidden flex items-center gap-2 rounded-xl bg-zinc-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-black">
                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        Resume
                    </button>
                </div>

                {{-- Panel pilihan alasan pause --}}
                <div id="pauseReasonPanel" class="hidden mt-3 rounded-xl border border-zinc-200 bg-white p-3">
                    <p class="mb-2 text-xs font-semibold text-zinc-600">Pilih alasan pause:</p>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        <button type="button" onclick="pauseRepair('adjust_dimensi')"
                            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                            Adjust Dimensi
                        </button>
                        <button type="button" onclick="pauseRepair('repair_line')"
                            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                            Repair di Line
                        </button>
                        <button type="button" onclick="pauseRepair('trial')"
                            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                            Trial
                        </button>
                        <button type="button" onclick="pauseRepair('cek_dies')"
                            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                            Cek Dies
                        </button>
                        <button type="button" onclick="pauseRepair('meeting')"
                            class="rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs font-medium text-zinc-700 transition hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                            Meeting
                        </button>
                    </div>
                    <p id="errorPauseReason" class="mt-2 hidden text-xs text-red-500"></p>
                </div>

                {{-- Hint kalau submit diblok karena masih paused --}}
                <p id="pausedSubmitHint" class="hidden mt-3 text-xs font-medium text-red-600">
                    ⚠️ Resume dulu sebelum bisa konfirmasi ke On Trial.
                </p>

                {{-- Mode Durasi (khusus role 1) — ⬅️ baru --}}
                @if(auth()->user()->role_id === 1)
                <div class="mt-4 border-t border-zinc-200 pt-4">
                    <p class="mb-2 text-xs font-semibold text-zinc-600">Mode Durasi (Admin):</p>
                    <div class="flex gap-2">
                        <button type="button" id="durasiModeAutoBtn" onclick="setDurasiMode('otomatis')"
                            class="flex-1 rounded-xl border-2 border-zinc-900 bg-zinc-900 px-4 py-2 text-xs font-semibold text-white transition">
                            Otomatis
                        </button>
                        <button type="button" id="durasiModeManualBtn" onclick="setDurasiMode('manual')"
                            class="flex-1 rounded-xl border-2 border-zinc-200 bg-white px-4 py-2 text-xs font-semibold text-zinc-600 transition hover:border-zinc-400">
                            Manual
                        </button>
                    </div>

                    <div id="durasiManualInputs" class="hidden mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-zinc-600">Jam</label>
                            <input type="number" min="0" id="durasiManualJam" placeholder="0"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-700
                                       focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-zinc-600">Menit</label>
                            <input type="number" min="0" max="59" id="durasiManualMenit" placeholder="0"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-700
                                       focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100">
                        </div>
                        <p id="errorDurasiManual" class="col-span-2 hidden text-xs text-red-500"></p>
                    </div>
                </div>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════
                SECTION 1: Tindakan Perbaikan
            ══════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-px flex-1 bg-zinc-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 px-2">Tindakan Perbaikan</span>
                    <div class="h-px flex-1 bg-zinc-200"></div>
                </div>
                <div class="space-y-4">

                    {{-- Analisa Penyebab --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Analisa Penyebab <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="additionalAnalisaPenyebab"
                            placeholder="Masukkan analisa penyebab..."
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                   transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        <p id="errorAdditionalAnalisaPenyebab" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>

                    {{-- Tindakan Perbaikan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Tindakan Perbaikan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="additionalTindakanPerbaikan"
                            placeholder="Masukkan tindakan perbaikan..."
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                   transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        <p id="errorAdditionalTindakanPerbaikan" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>

                    {{-- Sparepart Selector (⬅️ baru — multi-select search dari tabel parts) --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Sparepart yang Diganti <span class="text-red-500">*</span>
                        </label>
                        <select id="additionalSparepartSelect" class="w-full" style="width:100%">
                            <option></option>
                        </select>
                        <p id="errorAdditionalSparepartItems" class="mt-1 hidden text-xs text-red-500"></p>

                        {{-- List part terpilih + qty --}}
                        <div id="sparepartSelectedList" class="mt-3 space-y-2"></div>
                    </div>

                    {{-- Catatan Tambahan (opsional, sebelumnya ini yg wajib jadi free text) --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Catatan Tambahan Sparepart
                        </label>
                        <input type="text" id="additionalCatatanSparepart"
                            placeholder="Catatan tambahan (opsional)..."
                            class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                   transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        <p id="errorAdditionalCatatanSparepart" class="mt-1 hidden text-xs text-red-500"></p>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════
                SECTION 2: Penanganan Problem Burry
            ══════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-px flex-1 bg-zinc-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 px-2">Penanganan Problem Burry</span>
                    <div class="h-px flex-1 bg-zinc-200"></div>
                </div>
                <div class="space-y-4">

                    {{-- Row: Item + Proses Grinding --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Item</label>
                            <input type="text" id="additionalItem" placeholder="Item..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Proses Grinding</label>
                            <input type="text" id="additionalProsesGrinding" placeholder="Proses grinding..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Shim Up + Status Burry --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Shim Up</label>
                            <input type="text" id="additionalShimUp" placeholder="Shim up..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Status</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalStatusBurry" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalStatusBurry" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Row: Standart + Group Leader --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Standart</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalStandartBurry" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalStandartBurry" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Group Leader</label>
                            <input type="text" id="additionalGroupLeader" placeholder="Nama group leader..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Operator --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Operator</label>
                            <input type="text" id="additionalOperator" placeholder="Nama operator..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ══════════════════════════════════════════════
                SECTION 3: Target Trial After Repair
            ══════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-px flex-1 bg-zinc-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 px-2">Target Trial After Repair</span>
                    <div class="h-px flex-1 bg-zinc-200"></div>
                </div>
                <div class="space-y-4">

                    {{-- Row: Plan + Actual --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Plan</label>
                            <input type="text" id="additionalPlan" placeholder="Plan..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Actual</label>
                            <input type="text" id="additionalActual" placeholder="Actual..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Remark + Judge --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Remark</label>
                            <input type="text" id="additionalRemark" placeholder="Remark..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Judge</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalJudge" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="additionalJudge" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-zinc-100 bg-zinc-50 px-6 py-5 flex-shrink-0">
            <button onclick="closeAdditionalInfoModal()"
                class="rounded-2xl border border-zinc-300 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                Batal
            </button>
            <button onclick="submitAdditionalInfo()" id="submitAdditionalInfoBtn"
                class="flex items-center gap-2 rounded-2xl bg-zinc-900 px-6 py-2.5 text-sm font-semibold text-white
                       transition-all duration-200 hover:bg-black hover:scale-[1.03]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Konfirmasi On Trial</span>
            </button>
        </div>

    </div>
</div>

<style>
/* ── Fix: cegah scroll saat radio diklik ── */
.okng-radio-hidden {
    position: fixed;
    top: 0;
    left: 0;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}

/* ── OK/NG active state ── */
.okng-option input:checked ~ .okng-card {
    border-color: #18181b;
    background: #f4f4f5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.okng-option input:checked ~ .okng-card .okng-dot {
    background: #18181b;
}
</style>

<script>
// ════════════════════════════════════════════════════════
// PAUSE / RESUME — logic khusus modal Additional Info
// Catatan: _additionalInfoId, startDurasiTimer(), stopDurasiTimer(),
// _durasiStartSeconds, _durasiStartTime sudah dideklarasikan di
// request_repairs/index.blade.php dan bisa diakses dari sini karena
// keduanya sama-sama top-level <script> di halaman yang sama.
// ════════════════════════════════════════════════════════

function resetPauseUI() {
    document.getElementById('pauseReasonPanel').classList.add('hidden');
    document.getElementById('pauseReasonBanner').classList.add('hidden');
    document.getElementById('pauseBtn').classList.remove('hidden');
    document.getElementById('resumeBtn').classList.add('hidden');
    const errEl = document.getElementById('errorPauseReason');
    if (errEl) { errEl.textContent = ''; errEl.classList.add('hidden'); }
    const hint = document.getElementById('pausedSubmitHint');
    if (hint) hint.classList.add('hidden');
}

function togglePausePanel() {
    document.getElementById('pauseReasonPanel').classList.toggle('hidden');
}

async function pauseRepair(alasan) {
    const errEl = document.getElementById('errorPauseReason');
    errEl.textContent = ''; errEl.classList.add('hidden');

    try {
        const res = await fetch('/request-repairs/' + _additionalInfoId + '/pause', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ alasan: alasan }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('pauseReasonPanel').classList.add('hidden');
            await refreshDurasiState();
            if (typeof loadData === 'function') loadData(); // refresh row highlight di tabel belakang modal
        } else {
            const msg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Gagal melakukan pause.');
            errEl.textContent = msg;
            errEl.classList.remove('hidden');
        }
    } catch (e) {
        errEl.textContent = 'Terjadi kesalahan.';
        errEl.classList.remove('hidden');
    }
}

async function resumeRepair() {
    try {
        const res = await fetch('/request-repairs/' + _additionalInfoId + '/resume', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        });
        const data = await res.json();
        if (data.success) {
            await refreshDurasiState();
            if (typeof loadData === 'function') loadData();
        } else {
            if (typeof Swal !== 'undefined') Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
        }
    } catch (e) {
        if (typeof Swal !== 'undefined') Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
    }
}

async function refreshDurasiState() {
    try {
        const res  = await fetch('/request-repairs/' + _additionalInfoId + '/durasi');
        const data = await res.json();
        if (!data.success) return;
        applyDurasiState(data);
    } catch (e) { /* no-op */ }
}

function applyDurasiState(data) {
    const pauseBtn   = document.getElementById('pauseBtn');
    const resumeBtn  = document.getElementById('resumeBtn');
    const banner     = document.getElementById('pauseReasonBanner');
    const reasonText = document.getElementById('pauseReasonText');
    const liveBadge  = document.getElementById('durasiLiveBadge');
    const submitBtn  = document.getElementById('submitAdditionalInfoBtn');
    const pausedHint = document.getElementById('pausedSubmitHint');

    // ⬅️ baru — kalau mode manual lagi aktif, state pause dari server diabaikan
    if (typeof window._durasiMode !== 'undefined' && window._durasiMode === 'manual') {
        return;
    }

    if (data.is_paused) {
        // ── Freeze timer, tampilkan state paused ──
        stopDurasiTimer();
        document.getElementById('durasiDisplay').textContent = data.durasi_formatted;

        pauseBtn.classList.add('hidden');
        resumeBtn.classList.remove('hidden');
        banner.classList.remove('hidden');
        reasonText.textContent = PAUSE_REASON_LABEL[data.pause_reason] || data.pause_reason || '-';
        liveBadge.innerHTML = '<span class="h-2 w-2 rounded-full bg-red-500"></span><span class="text-xs font-medium text-red-600">Paused</span>';

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        if (pausedHint) pausedHint.classList.remove('hidden');
    } else {
        // ── Jalan normal, lanjutkan timer ──
        pauseBtn.classList.remove('hidden');
        resumeBtn.classList.add('hidden');
        banner.classList.add('hidden');
        liveBadge.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-zinc-600"></span><span class="text-xs font-medium text-zinc-600">Live</span>';

        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        if (pausedHint) pausedHint.classList.add('hidden');

        _durasiStartSeconds = data.seconds;
        _durasiStartTime    = Date.now();
        startDurasiTimer();
    }
}

// ════════════════════════════════════════════════════════
// SPAREPART SELECTOR (⬅️ baru) — select2 ajax multi-pick + qty per item
// selectedSpareparts didefinisikan sebagai window-level array supaya
// bisa dibaca dari submitAdditionalInfo() di index.blade.php
// ════════════════════════════════════════════════════════
window.selectedSpareparts = window.selectedSpareparts || []; // {part_id, kode_part, nama, satuan, stock, qty}

function initSparepartSelect() {
    if (typeof $ === 'undefined' || !$.fn.select2) return;

    try { $('#additionalSparepartSelect').select2('destroy'); } catch (e) {}

    $('#additionalSparepartSelect').select2({
        placeholder: 'Cari kode / nama part...',
        allowClear: true,
        width: '100%',
        minimumInputLength: 1,
        dropdownParent: $('#additionalInfoModal'),
        ajax: {
            url: '{{ route("request-repairs.search-parts") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
    }).on('select2:select', function (e) {
        const d = e.params.data;
        if (!window.selectedSpareparts.find(p => p.part_id === d.id)) {
            window.selectedSpareparts.push({
                part_id: d.id, kode_part: d.kode, nama: d.nama,
                satuan: d.satuan, stock: d.stock, qty: 1,
            });
        }
        renderSparepartList();
        // reset select2 biar bisa search part lain lagi
        $(this).val(null).trigger('change');
    });
}

function resetSparepartSelector() {
    window.selectedSpareparts = [];
    renderSparepartList();
    if (typeof $ !== 'undefined' && $.fn.select2) {
        try { $('#additionalSparepartSelect').val(null).trigger('change'); } catch (e) {}
    }
    const errEl = document.getElementById('errorAdditionalSparepartItems');
    if (errEl) { errEl.textContent = ''; errEl.classList.add('hidden'); }
}

function renderSparepartList() {
    const wrap = document.getElementById('sparepartSelectedList');
    if (!wrap) return;
    if (window.selectedSpareparts.length === 0) {
        wrap.innerHTML = '<p class="text-xs text-zinc-400">Belum ada part dipilih.</p>';
        return;
    }
    wrap.innerHTML = window.selectedSpareparts.map((p, idx) => `
        <div class="flex items-center gap-3 rounded-xl border border-zinc-200 bg-zinc-50 px-3 py-2">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-zinc-700 truncate">${p.kode_part} — ${p.nama}</p>
                <p class="text-xs text-zinc-500">Stock tersedia: ${p.stock} ${p.satuan || ''}</p>
            </div>
            <input type="number" min="1" max="${p.stock}" value="${p.qty}"
                onchange="updateSparepartQty(${idx}, this.value)"
                class="w-20 rounded-lg border border-zinc-300 px-2 py-1 text-sm text-center flex-shrink-0">
            <button type="button" onclick="removeSparepart(${idx})"
                class="text-red-500 hover:text-red-700 flex-shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `).join('');
}

function updateSparepartQty(idx, val) {
    const p = window.selectedSpareparts[idx];
    if (!p) return;
    let qty = parseInt(val) || 1;
    if (qty < 1) qty = 1;
    if (p.stock && qty > p.stock) qty = p.stock;
    p.qty = qty;
    renderSparepartList();
}

function removeSparepart(idx) {
    window.selectedSpareparts.splice(idx, 1);
    renderSparepartList();
}

// ════════════════════════════════════════════════════════
// MODE DURASI — Otomatis / Manual (khusus role 1) — ⬅️ baru
// ════════════════════════════════════════════════════════
window._durasiMode = 'otomatis';

function setDurasiMode(mode) {
    window._durasiMode = mode;

    const autoBtn    = document.getElementById('durasiModeAutoBtn');
    const manualBtn  = document.getElementById('durasiModeManualBtn');
    const manualWrap = document.getElementById('durasiManualInputs');
    const pauseBtn   = document.getElementById('pauseBtn');
    const resumeBtn  = document.getElementById('resumeBtn');
    const submitBtn  = document.getElementById('submitAdditionalInfoBtn');
    const pausedHint = document.getElementById('pausedSubmitHint');

    if (!autoBtn || !manualBtn || !manualWrap) return; // elemen cuma ada buat role 1

    const activeCls   = ['bg-zinc-900', 'border-zinc-900', 'text-white'];
    const inactiveCls = ['border-zinc-200', 'bg-white', 'text-zinc-600'];

    if (mode === 'manual') {
        autoBtn.classList.remove(...activeCls);     autoBtn.classList.add(...inactiveCls);
        manualBtn.classList.remove(...inactiveCls); manualBtn.classList.add(...activeCls);
        manualWrap.classList.remove('hidden');

        stopDurasiTimer();

        pauseBtn.disabled = true;  pauseBtn.classList.add('opacity-50', 'cursor-not-allowed');
        resumeBtn.disabled = true; resumeBtn.classList.add('opacity-50', 'cursor-not-allowed');

        // manual override -> submit gak boleh keblok gara-gara status paused
        if (submitBtn)  { submitBtn.disabled = false; submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
        if (pausedHint) pausedHint.classList.add('hidden');
    } else {
        manualBtn.classList.remove(...activeCls);  manualBtn.classList.add(...inactiveCls);
        autoBtn.classList.remove(...inactiveCls);  autoBtn.classList.add(...activeCls);
        manualWrap.classList.add('hidden');

        pauseBtn.disabled = false;  pauseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        resumeBtn.disabled = false; resumeBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        refreshDurasiState(); // balik ke state asli (termasuk re-cek pause)
    }
}

function resetDurasiModeUI() {
    window._durasiMode = 'otomatis';

    const autoBtn    = document.getElementById('durasiModeAutoBtn');
    const manualBtn  = document.getElementById('durasiModeManualBtn');
    const manualWrap = document.getElementById('durasiManualInputs');
    if (autoBtn && manualBtn && manualWrap) {
        autoBtn.classList.add('bg-zinc-900', 'border-zinc-900', 'text-white');
        autoBtn.classList.remove('border-zinc-200', 'bg-white', 'text-zinc-600');
        manualBtn.classList.remove('bg-zinc-900', 'border-zinc-900', 'text-white');
        manualBtn.classList.add('border-zinc-200', 'bg-white', 'text-zinc-600');
        manualWrap.classList.add('hidden');
    }

    const jamEl   = document.getElementById('durasiManualJam');
    const menitEl = document.getElementById('durasiManualMenit');
    if (jamEl)   jamEl.value = '';
    if (menitEl) menitEl.value = '';

    const errEl = document.getElementById('errorDurasiManual');
    if (errEl) { errEl.textContent = ''; errEl.classList.add('hidden'); }

    const pauseBtn  = document.getElementById('pauseBtn');
    const resumeBtn = document.getElementById('resumeBtn');
    if (pauseBtn)  { pauseBtn.disabled = false;  pauseBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
    if (resumeBtn) { resumeBtn.disabled = false; resumeBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
}

function getDurasiManualPayload() {
    if (typeof window._durasiMode === 'undefined' || window._durasiMode !== 'manual') {
        return { durasi_mode: 'otomatis', durasi_manual_seconds: null };
    }
    const jam   = parseInt(document.getElementById('durasiManualJam').value) || 0;
    const menit = parseInt(document.getElementById('durasiManualMenit').value) || 0;
    return { durasi_mode: 'manual', durasi_manual_seconds: (jam * 3600) + (menit * 60) };
}
</script>