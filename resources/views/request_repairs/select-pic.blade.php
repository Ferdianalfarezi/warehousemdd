{{-- ════════════════════════════════════════════════════
    Modal: Pilih PIC → Open ke On Process
    File: resources/views/request_repairs/select-pic.blade.php
════════════════════════════════════════════════════ --}}
<div id="selectPicModal"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    onclick="handleSelectPicBackdrop(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div id="selectPicContent"
        class="relative w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-[0_25px_60px_rgba(0,0,0,0.3)]
               transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="relative bg-zinc-900 px-6 py-5 flex-shrink-0">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-zinc-800 border border-zinc-700">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-tight text-white">Proses Request</h3>
                        <p id="selectPicNo" class="mt-1 text-sm font-mono text-zinc-400"></p>
                    </div>
                </div>
                <button onclick="closeSelectPicModal()"
                    class="flex h-10 w-10 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 px-6 py-6 space-y-5">

            <p class="text-sm text-gray-600">
                Status akan berubah dari <span class="font-semibold text-gray-800">Open</span> menjadi
                <span class="font-semibold text-gray-800">On Process</span>. Siapa yang akan menangani perbaikan ini?
            </p>

            {{-- Pilihan mode: Sendiri / Bersama Tim --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="pic-mode-option cursor-pointer">
                    <input type="radio" name="picMode" value="sendiri" class="pic-mode-radio-hidden" checked>
                    <div class="pic-mode-card flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-zinc-200 bg-white py-4 transition-all duration-200 hover:border-zinc-400">
                        <svg class="h-6 w-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Sendiri</span>
                    </div>
                </label>
                <label class="pic-mode-option cursor-pointer">
                    <input type="radio" name="picMode" value="tim" class="pic-mode-radio-hidden">
                    <div class="pic-mode-card flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-zinc-200 bg-white py-4 transition-all duration-200 hover:border-zinc-400">
                        <svg class="h-6 w-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/>
                        </svg>
                        <span class="text-sm font-semibold text-gray-700">Bersama Tim</span>
                    </div>
                </label>
            </div>

            {{-- Pilih anggota tim (muncul kalau mode = tim) --}}
            <div id="selectPicTeamWrapper" class="hidden">
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Pilih Anggota Tim <span class="text-red-500">*</span>
                </label>
                <select id="selectPicTeamSelect" multiple
                    class="w-full rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm text-gray-700">
                </select>
                <p class="mt-1 text-xs text-gray-400">Kamu otomatis tercatat sebagai salah satu PIC.</p>
                <p id="errorSelectPicTeam" class="mt-1 hidden text-xs text-red-500"></p>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-zinc-100 bg-zinc-50 px-6 py-5 flex-shrink-0">
            <button onclick="closeSelectPicModal()"
                class="rounded-2xl border border-zinc-300 bg-white px-5 py-2.5 text-sm font-semibold text-zinc-600 transition hover:bg-zinc-100">
                Batal
            </button>
            <button onclick="submitSelectPic()" id="submitSelectPicBtn"
                class="flex items-center gap-2 rounded-2xl bg-zinc-900 px-6 py-2.5 text-sm font-semibold text-white
                       transition-all duration-200 hover:bg-black hover:scale-[1.03]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Proses Request</span>
            </button>
        </div>

    </div>
</div>

<style>
.pic-mode-radio-hidden {
    position: fixed;
    top: 0;
    left: 0;
    opacity: 0;
    pointer-events: none;
    width: 0;
    height: 0;
}
.pic-mode-option input:checked ~ .pic-mode-card {
    border-color: #18181b;
    background: #f4f4f5;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.pic-mode-option input:checked ~ .pic-mode-card svg,
.pic-mode-option input:checked ~ .pic-mode-card span {
    color: #18181b;
}
</style>