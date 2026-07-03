{{-- ════════════════════════════════════════════════════
    Modal: Closed Info → Closed
    File: resources/views/request_repairs/closed-info.blade.php
════════════════════════════════════════════════════ --}}
<div id="closedInfoModal"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    onclick="handleClosedInfoBackdrop(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div id="closedInfoContent"
        class="relative w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-[0_25px_60px_rgba(0,0,0,0.3)]
               transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="relative bg-zinc-900 px-6 py-5 flex-shrink-0">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 border border-zinc-700">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-white">Konfirmasi ke Closed</h3>
                        <p id="closedInfoNo" class="mt-1 text-sm font-mono text-zinc-400"></p>
                    </div>
                </div>
                <button onclick="closeClosedInfoModal()"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body (scrollable) --}}
        <div class="overflow-y-auto flex-1 px-6 py-6 space-y-6">

            {{-- ══════════════════════════════════════════════
                SECTION 1: Monitoring Dies Temporary
            ══════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-px flex-1 bg-zinc-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 px-2">Monitoring Dies Temporary</span>
                    <div class="h-px flex-1 bg-zinc-200"></div>
                </div>
                <div class="space-y-4">

                    {{-- Row: Tanggal Cek + Lot Prod --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Tanggal Cek</label>
                            <input type="date" id="closedTanggalCek"
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Lot Prod</label>
                            <input type="text" id="closedLotProd" placeholder="Lot prod..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Awal + Tengah --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Awal</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedAwal" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedAwal" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Tengah</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedTengah" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedTengah" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Row: Akhir + Qty --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Akhir</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedAkhir" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedAkhir" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Qty</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedQty" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedQty" value="NG" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">NG</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Row: Remark + Judge --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Remark</label>
                            <input type="text" id="closedRemarkMonitoring" placeholder="Remark..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Judge</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedJudgeMonitoring" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedJudgeMonitoring" value="NG" class="okng-radio-hidden">
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

            {{-- ══════════════════════════════════════════════
                SECTION 2: Target Permanen Action
            ══════════════════════════════════════════════ --}}
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-px flex-1 bg-zinc-200"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-zinc-500 px-2">Target Permanen Action</span>
                    <div class="h-px flex-1 bg-zinc-200"></div>
                </div>
                <div class="space-y-4">

                    {{-- Row: Plan + Actual --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Plan</label>
                            <input type="text" id="closedPlanPermanen" placeholder="Plan..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Actual</label>
                            <input type="text" id="closedActualPermanen" placeholder="Actual..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Rootcause + Recovery --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Rootcause</label>
                            <input type="text" id="closedRootcause" placeholder="Rootcause..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Recovery</label>
                            <input type="text" id="closedRecovery" placeholder="Recovery..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                    </div>

                    {{-- Row: Assy Trial Check + Judge --}}
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Assy Trial Check</label>
                            <input type="text" id="closedAssyTrialCheck" placeholder="Assy trial check..."
                                class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700
                                       transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100 placeholder:text-gray-400">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-semibold text-gray-700">Judge</label>
                            <div class="flex gap-3">
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedJudgePermanen" value="OK" class="okng-radio-hidden">
                                    <div class="okng-card flex items-center justify-center gap-2 rounded-xl border-2 border-zinc-200 bg-white py-2.5 transition-all duration-200 hover:border-zinc-400">
                                        <div class="okng-dot h-2.5 w-2.5 rounded-full bg-zinc-300 transition"></div>
                                        <span class="text-sm font-semibold text-gray-700">OK</span>
                                    </div>
                                </label>
                                <label class="okng-option flex-1 cursor-pointer">
                                    <input type="radio" name="closedJudgePermanen" value="NG" class="okng-radio-hidden">
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
            <button onclick="closeClosedInfoModal()"
                class="rounded-2xl border border-zinc-300 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                Batal
            </button>
            <button onclick="submitClosedInfo()" id="submitClosedInfoBtn"
                class="flex items-center gap-2 rounded-2xl bg-zinc-900 px-6 py-2.5 text-sm font-semibold text-white
                       transition-all duration-200 hover:bg-black hover:scale-[1.03]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Konfirmasi Closed</span>
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