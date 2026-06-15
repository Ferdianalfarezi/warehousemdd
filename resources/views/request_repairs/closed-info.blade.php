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
        class="relative w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-[0_25px_60px_rgba(0,0,0,0.3)]
               transform scale-95 opacity-0 transition-all duration-300">

        {{-- Header --}}
        <div class="relative bg-zinc-900 px-6 py-5">
            <div class="relative flex items-start justify-between">
                <div class="flex items-center gap-4">

                    {{-- Icon --}}
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 border border-zinc-700">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>

                    {{-- Title --}}
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-white">
                            Konfirmasi ke Closed
                        </h3>
                        <p id="closedInfoNo"
                            class="mt-1 text-sm font-mono text-zinc-400">
                        </p>
                    </div>
                </div>

                {{-- Close --}}
                <button onclick="closeClosedInfoModal()"
                    class="flex h-10 w-10 items-center justify-center rounded-xl
                           text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="space-y-6 px-6 py-6">

            {{-- Status After Trial --}}
            <div>
                <label class="mb-3 block text-sm font-semibold text-gray-700">
                    Status After Trial
                    <span class="text-red-500">*</span>
                </label>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                    {{-- OK --}}
                    <label class="status-trial-option cursor-pointer">
                        <input type="radio"
                            name="closedStatusAfterTrial"
                            value="OK"
                            class="sr-only">

                        <div class="status-trial-card flex items-center gap-4 rounded-2xl border-2 border-zinc-200 bg-white p-4 transition-all duration-200 hover:border-zinc-400">
                            <div class="status-trial-radio flex h-6 w-6 items-center justify-center rounded-full border-2 border-zinc-300 transition">
                                <div class="status-trial-dot hidden h-3 w-3 rounded-full bg-zinc-700"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">OK</p>
                                <p class="text-xs text-gray-500">Hasil trial diterima</p>
                            </div>
                        </div>
                    </label>

                    {{-- NG --}}
                    <label class="status-trial-option cursor-pointer">
                        <input type="radio"
                            name="closedStatusAfterTrial"
                            value="NG"
                            class="sr-only">

                        <div class="status-trial-card flex items-center gap-4 rounded-2xl border-2 border-zinc-200 bg-white p-4 transition-all duration-200 hover:border-zinc-400">
                            <div class="status-trial-radio flex h-6 w-6 items-center justify-center rounded-full border-2 border-zinc-300 transition">
                                <div class="status-trial-dot hidden h-3 w-3 rounded-full bg-zinc-700"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">NG</p>
                                <p class="text-xs text-gray-500">Hasil trial ditolak</p>
                            </div>
                        </div>
                    </label>

                </div>

                <p id="errorClosedStatusAfterTrial"
                    class="mt-1 hidden text-xs text-red-500"></p>
            </div>

            {{-- Point Verifikasi --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Point Verifikasi
                    <span class="text-red-500">*</span>
                    <span class="ml-1 text-xs font-normal text-gray-400">(Quality Part)</span>
                </label>

                <textarea id="closedPointVerifikasi"
                    rows="4"
                    placeholder="Tuliskan point verifikasi quality part..."
                    class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm text-gray-700
                           transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100
                           resize-none placeholder:text-gray-400"></textarea>

                <p id="errorClosedPointVerifikasi"
                    class="mt-1 hidden text-xs text-red-500"></p>
            </div>

            {{-- Approval Section Chief --}}
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Approval Section Chief
                    <span class="text-red-500">*</span>
                </label>

                <input type="text"
                    id="closedApprovalSectionChief"
                    placeholder="Nama Section Chief yang menyetujui..."
                    class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm text-gray-700
                           transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100
                           placeholder:text-gray-400">

                <p id="errorClosedApprovalSectionChief"
                    class="mt-1 hidden text-xs text-red-500"></p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-zinc-100 bg-zinc-50 px-6 py-5">

            <button onclick="closeClosedInfoModal()"
                class="rounded-2xl border border-zinc-300 bg-white px-5 py-2.5
                       text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                Batal
            </button>

            <button onclick="submitClosedInfo()"
                id="submitClosedInfoBtn"
                class="flex items-center gap-2 rounded-2xl bg-zinc-900
                       px-6 py-2.5 text-sm font-semibold text-white
                       transition-all duration-200 hover:bg-black hover:scale-[1.03]">

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 13l4 4L19 7"/>
                </svg>

                <span>Konfirmasi Closed</span>
            </button>

        </div>
    </div>
</div>

<style>
/* =========================================================
   STATUS TRIAL ACTIVE STATE
========================================================= */
.status-trial-option input:checked ~ .status-trial-card {
    border-color: #18181b;
    background: #f4f4f5;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}
.status-trial-option input:checked ~ .status-trial-card .status-trial-radio {
    border-color: #18181b;
}
.status-trial-option input:checked ~ .status-trial-card .status-trial-dot {
    display: block;
}
</style>