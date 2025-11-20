<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Detail Checkup</h3>
            <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            
            <!-- Image and Basic Info -->
            <div class="flex items-start space-x-4">
                <img id="detailImage" src="" alt="Barang" class="w-24 h-24 rounded-lg object-cover border border-gray-200">
                <div class="flex-1">
                    <h4 id="detailNama" class="text-lg font-bold text-gray-900"></h4>
                    <p id="detailKode" class="text-sm text-gray-600 mt-1"></p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Line: <span id="detailLine" class="ml-1"></span>
                        </span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- Checkup Information -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Tanggal Terjadwal</label>
                    <p id="detailTerjadwal" class="mt-1 text-sm font-medium text-gray-900"></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Tanggal Checkup</label>
                    <p id="detailCheckup" class="mt-1 text-sm font-medium text-gray-900"></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Mulai Perbaikan</label>
                    <p id="detailMulai" class="mt-1 text-sm font-medium text-gray-900"></p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase">Waktu Selesai</label>
                    <p id="detailSelesai" class="mt-1 text-sm font-medium text-gray-900"></p>
                </div>
            </div>

            <hr class="border-gray-200">

            <!-- Status -->
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                <p id="detailStatus" class="mt-1 text-sm font-medium text-gray-900"></p>
            </div>

            <!-- Catatan Umum -->
            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase">Catatan Umum</label>
                <p id="detailCatatan" class="mt-1 text-sm text-gray-700 whitespace-pre-wrap"></p>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end rounded-b-2xl">
            <button 
                onclick="closeDetailModal()"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>