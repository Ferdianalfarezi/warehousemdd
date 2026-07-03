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
                    <div class="flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-zinc-600"></span>
                        <span class="text-xs font-medium text-zinc-600">Live</span>
                    </div>
                </div>
                <p id="durasiDisplay" class="font-mono text-3xl font-bold tracking-tight text-zinc-800">Menghitung...</p>
                <p class="mt-2 text-xs text-zinc-500">Dihitung sejak request dibuat · otomatis berhenti saat submit</p>
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

                    {{-- Catatan Penggantian Sparepart --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                            Catatan Penggantian Sparepart <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="additionalCatatanSparepart"
                            placeholder="Masukkan catatan penggantian sparepart..."
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