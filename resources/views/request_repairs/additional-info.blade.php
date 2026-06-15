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
        class="relative w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-[0_25px_60px_rgba(0,0,0,0.3)]
               transform scale-95 opacity-0 transition-all duration-300">

        {{-- Header --}}
        <div class="relative bg-zinc-900 px-6 py-5">

            <div class="relative flex items-start justify-between">

                <div class="flex items-center gap-4">

                    {{-- Icon --}}
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 border border-zinc-700">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>

                    {{-- Title --}}
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-white">
                            Konfirmasi ke On Trial
                        </h3>

                        <p id="additionalInfoNo"
                            class="mt-1 text-sm font-mono text-zinc-400">
                        </p>
                    </div>
                </div>

                {{-- Close --}}
                <button onclick="closeAdditionalInfoModal()"
                    class="flex h-10 w-10 items-center justify-center rounded-xl
                           text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        </div>

        {{-- Body --}}
        <div class="space-y-6 px-6 py-6">

            {{-- Durasi --}}
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">

                <div class="mb-3 flex items-center justify-between">

                    <div class="flex items-center gap-2">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-100">
                            <svg class="h-5 w-5 text-zinc-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-600">
                                Durasi Repair
                            </p>

                            <p class="text-xs text-zinc-500">
                                Status: On Process
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 rounded-full bg-zinc-100 px-3 py-1">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-zinc-600"></span>
                        <span id="durasiLiveLabel"
                            class="text-xs font-medium text-zinc-600">
                            Live
                        </span>
                    </div>

                </div>

                <p id="durasiDisplay"
                    class="font-mono text-3xl font-bold tracking-tight text-zinc-800">
                    Menghitung...
                </p>

                <p class="mt-2 text-xs text-zinc-500">
                    Dihitung sejak request dibuat · otomatis berhenti saat submit
                </p>

            </div>

            {{-- Penyebab --}}
            <div>

                <label class="mb-2 block text-sm font-semibold text-gray-700">
                    Penyebab 
                    <span class="text-red-500">*</span>
                </label>

                <textarea id="additionalPenyebabVc"
                    rows="4"
                    placeholder="Jelaskan penyebab kerusakan / problem..."
                    class="w-full rounded-2xl border border-zinc-300 bg-white px-4 py-3 text-sm text-gray-700
                           transition focus:border-zinc-400 focus:ring-4 focus:ring-zinc-100
                           resize-none placeholder:text-gray-400"></textarea>

                <p id="errorAdditionalPenyebabVc"
                    class="mt-1 hidden text-xs text-red-500">
                </p>

            </div>

            {{-- Tindakan --}}
            <div>

                <label class="mb-3 block text-sm font-semibold text-gray-700">
                    Tindakan
                    <span class="text-red-500">*</span>
                </label>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                    {{-- Pertama --}}
                    <label class="tindakan-option cursor-pointer">
                        <input type="radio"
                            name="additionalTindakan"
                            value="Pertama"
                            class="sr-only">

                        <div class="tindakan-card group flex items-center gap-4 rounded-2xl border-2 border-zinc-200 bg-white p-4 transition-all duration-200 hover:border-zinc-400">

                            <div class="tindakan-radio flex h-6 w-6 items-center justify-center rounded-full border-2 border-zinc-300 transition">
                                <div class="tindakan-dot hidden h-3 w-3 rounded-full bg-zinc-700"></div>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Pertama
                                </p>

                                <p class="text-xs text-gray-500">
                                    Kejadian pertama kali
                                </p>
                            </div>

                        </div>
                    </label>

                    {{-- Berulang --}}
                    <label class="tindakan-option cursor-pointer">
                        <input type="radio"
                            name="additionalTindakan"
                            value="Berulang"
                            class="sr-only">

                        <div class="tindakan-card group flex items-center gap-4 rounded-2xl border-2 border-zinc-200 bg-white p-4 transition-all duration-200 hover:border-zinc-400">

                            <div class="tindakan-radio flex h-6 w-6 items-center justify-center rounded-full border-2 border-zinc-300 transition">
                                <div class="tindakan-dot hidden h-3 w-3 rounded-full bg-zinc-700"></div>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Berulang
                                </p>

                                <p class="text-xs text-gray-500">
                                    Pernah terjadi sebelumnya
                                </p>
                            </div>

                        </div>
                    </label>

                </div>

                <p id="errorAdditionalTindakanRepair"
                    class="mt-1 hidden text-xs text-red-500">
                </p>

            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-zinc-100 bg-zinc-50 px-6 py-5">

            <button onclick="closeAdditionalInfoModal()"
                class="rounded-2xl border border-zinc-300 bg-white px-5 py-2.5
                       text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                Batal
            </button>

            <button onclick="submitAdditionalInfo()"
                id="submitAdditionalInfoBtn"
                class="flex items-center gap-2 rounded-2xl bg-zinc-900
                       px-6 py-2.5 text-sm font-semibold text-white
                       transition-all duration-200 hover:bg-black hover:scale-[1.03]">

                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <span>Konfirmasi On Trial</span>

            </button>

        </div>
    </div>
</div>

<style>
/* =========================================================
   TINDAKAN ACTIVE STATE
========================================================= */

.tindakan-option input:checked ~ .tindakan-card {
    border-color: #18181b;
    background: #f4f4f5;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.tindakan-option input:checked ~ .tindakan-card .tindakan-radio {
    border-color: #18181b;
}

.tindakan-option input:checked ~ .tindakan-card .tindakan-dot {
    display: block;
}
</style>