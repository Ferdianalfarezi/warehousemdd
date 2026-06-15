{{-- ════════════════════════════════════════════════════
    Modal: Detail Request Repair (dengan Timeline)
    File: resources/views/request_repairs/detail.blade.php
═══════════════════════════════════════════════════ --}}
<div id="detailModal"
    style="display:none"
    class="fixed inset-0 z-50 flex items-center justify-center p-6"
    onclick="handleDetailBackdrop(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    {{-- Modal --}}
    <div id="detailModalContent"
        class="relative bg-white rounded-[20px] shadow-xl w-full max-w-2xl
               transform transition-all duration-300 scale-95 opacity-0
               overflow-hidden max-h-[90vh] flex flex-col
               border border-zinc-200">

        {{-- Header --}}
        <div class="bg-white border-b border-zinc-200 px-6 py-5 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-[38px] h-[38px] rounded-[10px] bg-zinc-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[14px] font-medium text-zinc-900 leading-none">Detail Request Repair</p>
                    <p id="detailNo" class="text-[12px] text-zinc-400 font-mono mt-1"></p>
                </div>
            </div>
            <button onclick="closeDetailModal()"
                class="w-[30px] h-[30px] rounded-lg bg-zinc-100 border border-zinc-200 hover:bg-zinc-200 flex items-center justify-center transition text-zinc-500 hover:text-zinc-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-5">

            {{-- Info Grid --}}
            <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Tanggal Pengajuan</p>
                    <p id="detailTanggal" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Part No</p>
                    <p id="detailPartNo" class="text-[13.5px] font-mono text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Group / Shift</p>
                    <p id="detailGroupShift" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Nama Part</p>
                    <p id="detailNama" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Jumlah Stroke</p>
                    <p id="detailStroke" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Process No</p>
                    <p id="detailProcessNo" class="text-[13.5px] font-mono text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Line Mesin</p>
                    <p id="detailLineMesin" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Customer</p>
                    <p id="detailCustomer" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Target Selesai</p>
                    <p id="detailTarget" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Detail Proyek</p>
                    <p id="detailProyek" class="text-[13.5px] text-zinc-900"></p>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Jenis</p>
                    <div id="detailJenis" class="text-[13.5px] text-zinc-900"></div>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Kategori</p>
                    <div id="detailKategori" class="text-[13.5px] text-zinc-900"></div>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Status</p>
                    <div id="detailStatus" class="text-[13.5px] text-zinc-900"></div>
                </div>
            </div>

            {{-- ── Additional Info (On Trial) ── --}}
            <div id="detailAdditionalInfo" class="hidden">
                <hr class="border-zinc-200 mb-5">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <p class="flex items-center gap-1.5 text-[10px] font-medium text-amber-700 uppercase tracking-widest mb-3">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Info On Trial
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        <div>
                            <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Penyebab (VC)</p>
                            <p id="detailPenyebabVc" class="text-[13.5px] text-zinc-900 whitespace-pre-line"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Tindakan</p>
                            <p id="detailTindakanRepair" class="text-[13.5px] text-zinc-900"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Closed Info ── --}}
            <div id="detailClosedInfo" class="hidden">
                <hr class="border-zinc-200 mb-5">
                <div class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <p class="flex items-center gap-1.5 text-[10px] font-medium text-green-700 uppercase tracking-widest mb-3">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Info Closed
                    </p>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        <div>
                            <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Status After Trial</p>
                            <p id="detailStatusAfterTrial" class="text-[13.5px] font-semibold text-zinc-900"></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Approval Section Chief</p>
                            <p id="detailApprovalSectionChief" class="text-[13.5px] text-zinc-900"></p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-[10px] font-medium text-zinc-400 uppercase tracking-widest mb-0.5">Point Verifikasi (Quality Part)</p>
                            <p id="detailPointVerifikasi" class="text-[13.5px] text-zinc-900 whitespace-pre-line"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Timeline ── --}}
            <div id="detailTimeline" class="hidden">
                <hr class="border-zinc-200 mb-5">

                <p class="flex items-center gap-1.5 text-[10px] font-medium text-zinc-500 uppercase tracking-widest mb-4">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Timeline durasi
                </p>

                <div class="relative flex flex-col">
                    {{-- Vertical connector --}}
                    <div class="absolute left-[17px] top-5 bottom-5 w-px bg-zinc-200"></div>

                    {{-- On Process --}}
                    <div class="flex gap-4 pb-5 relative">
                        <div class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full bg-zinc-900 border-[1.5px] border-zinc-900 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 pt-1.5">
                            <p class="text-[13.5px] font-medium text-zinc-900">On Process</p>
                            <p class="text-[12px] text-zinc-400 mt-0.5">Request dibuat & mulai diproses</p>
                            <p id="timelineOnProcessAt" class="text-[11px] font-mono text-zinc-500 mt-1"></p>
                            <div id="timelineDurasiOnProcess" class="hidden mt-2">
                                <span class="inline-flex items-center gap-1.5 bg-zinc-100 border border-zinc-200 rounded-md px-2.5 py-1 text-[11.5px] text-zinc-600">
                                    <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Durasi: <span id="timelineDurasiOnProcessVal" class="font-medium"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- On Trial --}}
                    <div id="timelineOnTrialRow" class="hidden flex gap-4 pb-5 relative">
                        <div class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full bg-green-50 border-[1.5px] border-green-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 pt-1.5">
                            <p class="text-[13.5px] font-medium text-zinc-900">On Trial</p>
                            <p class="text-[12px] text-zinc-400 mt-0.5">Perbaikan selesai, dalam tahap trial</p>
                            <p id="timelineOnTrialAt" class="text-[11px] font-mono text-zinc-500 mt-1"></p>
                            <div id="timelineDurasiOnTrial" class="hidden mt-2">
                                <span class="inline-flex items-center gap-1.5 bg-zinc-100 border border-zinc-200 rounded-md px-2.5 py-1 text-[11.5px] text-zinc-600">
                                    <svg class="w-3 h-3 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                                    </svg>
                                    Durasi: <span id="timelineDurasiOnTrialVal" class="font-medium"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Closed --}}
                    <div id="timelineClosedRow" class="hidden flex gap-4 relative">
                        <div class="relative z-10 flex-shrink-0 w-9 h-9 rounded-full bg-white border-[1.5px] border-zinc-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 pt-1.5">
                            <p class="text-[13.5px] font-medium text-zinc-400">Closed</p>
                            <p class="text-[12px] text-zinc-400 mt-0.5">Repair selesai & dinyatakan closed</p>
                            <p id="timelineClosedAt" class="text-[11px] font-mono text-zinc-400 mt-1"></p>
                        </div>
                    </div>

                </div>

                {{-- Total durasi --}}
                <div id="timelineTotalRow" class="hidden mt-4 flex items-center justify-between bg-zinc-900 rounded-xl px-5 py-3">
                    <div class="flex items-center gap-2 text-[12px] text-zinc-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Total durasi repair
                    </div>
                    <span id="timelineDurasiTotal" class="text-[14px] font-medium text-white font-mono"></span>
                </div>

            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-zinc-200 flex-shrink-0 flex justify-end">
            <button onclick="closeDetailModal()"
                class="px-5 py-2 text-[13.5px] font-medium text-white bg-zinc-900 hover:bg-black rounded-lg transition">
                Tutup
            </button>
        </div>

    </div>
</div>