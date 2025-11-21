<!-- History Detail Modal -->
<div id="historyDetailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl max-w-7xl w-full max-h-[90vh] overflow-hidden shadow-2xl flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="historyModalContent">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex items-center justify-between">
            <h3 class="text-2xl font-bold text-black">Detail History Checkup</h3>

            <button onclick="closeHistoryDetailModal()" class="text-black hover:text-gray-700 transition">
                <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>


        <!-- Modal Body - 2 Columns Layout -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                
                <!-- LEFT COLUMN -->
                <div class="space-y-6">
                    
                    <!-- Basic Information Card -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Informasi Barang
                        </h4>
                        <div class="flex items-start space-x-4">
                            <img id="historyDetailImage" src="" alt="Barang" class="w-24 h-24 rounded-lg object-cover border-2 border-gray-300 shadow-sm">
                            <div class="flex-1 space-y-2">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Kode Barang</p>
                                    <p id="historyDetailKode" class="text-base font-bold text-gray-900">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Nama Barang</p>
                                    <p id="historyDetailNama" class="text-sm font-semibold text-gray-900">-</p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-2 border-t">
                                    <div>
                                        <p class="text-xs text-gray-500">Line</p>
                                        <p id="historyDetailLine" class="text-sm font-bold text-gray-900">-</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tgl Terjadwal</p>
                                        <p id="historyDetailTerjadwal" class="text-sm font-bold text-gray-900">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Card -->
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 border-2 border-blue-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Timeline Perbaikan
                        </h4>
                        <div class="space-y-3">
                            <div class="bg-white rounded-lg p-3 flex items-center justify-between shadow-sm hover:shadow transition-shadow duration-200">
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal Checkup</p>
                                    <p id="historyDetailCheckup" class="text-sm font-bold text-gray-900">-</p>
                                </div>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 flex items-center justify-between shadow-sm hover:shadow transition-shadow duration-200">
                                <div>
                                    <p class="text-xs text-gray-500">Mulai Perbaikan</p>
                                    <p id="historyDetailMulai" class="text-sm font-bold text-gray-900">-</p>
                                </div>
                                <div class="p-2 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-3 flex items-center justify-between shadow-sm hover:shadow transition-shadow duration-200">
                                <div>
                                    <p class="text-xs text-gray-500">Waktu Selesai</p>
                                    <p id="historyDetailSelesai" class="text-sm font-bold text-gray-900">-</p>
                                </div>
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="bg-gradient-to-r from-orange-100 to-red-100 rounded-lg p-3 flex items-center justify-between border-2 border-orange-300 hover:shadow-lg transition-all duration-200">
                                <div>
                                    <p class="text-xs text-orange-700 font-bold uppercase">Total Durasi</p>
                                    <p id="historyDetailDurasi" class="text-l font-black text-orange-900">-</p>
                                </div>
                                <div class="p-2 bg-white rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-green-50 border-2 border-green-300 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200">
                            <div class="p-2 bg-green-200 rounded-lg mx-auto w-fit mb-2">
                                <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-green-700 uppercase">Total OK</p>
                            <p id="historyDetailOK" class="text-2xl font-black text-green-800 mt-1">0</p>
                        </div>
                        <div class="bg-red-50 border-2 border-red-300 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200">
                            <div class="p-2 bg-red-200 rounded-lg mx-auto w-fit mb-2">
                                <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-red-700 uppercase">Total NG</p>
                            <p id="historyDetailNG" class="text-2xl font-black text-red-800 mt-1">0</p>
                        </div>
                        <div class="bg-orange-50 border-2 border-orange-300 rounded-xl p-3 text-center hover:scale-105 transition-transform duration-200">
                            <div class="p-2 bg-orange-200 rounded-lg mx-auto w-fit mb-2">
                                <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-orange-700 uppercase">Parts Used</p>
                            <p id="historyDetailParts" class="text-2xl font-black text-orange-800 mt-1">0</p>
                        </div>
                    </div>

                    <!-- Catatan Umum -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            Catatan Umum
                        </h4>
                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-300 min-h-[80px]">
                            <p id="historyDetailCatatan" class="text-sm text-gray-700 whitespace-pre-line">-</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6">
                    
                    <!-- Checkup Details -->
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Detail Checkup per Parameter
                        </h4>
                        <div id="historyCheckupDetails" class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                            <!-- Will be populated via JavaScript -->
                        </div>
                    </div>

                    <!-- Part Replacements -->
                    <div id="historyPartReplacementsSection" class="hidden bg-white border-2 border-gray-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Part yang Digunakan (General)
                        </h4>
                        <div id="historyPartReplacements" class="grid grid-cols-1 gap-3 max-h-[300px] overflow-y-auto pr-2">
                            <!-- Will be populated via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-100 border-t-2 border-gray-300 px-6 py-3 flex justify-end">
            <button 
                onclick="closeHistoryDetailModal()"
                class="px-6 py-2.5 bg-black text-white rounded-lg font-bold 
                    hover:bg-gray-800 transition shadow-lg hover:shadow-xl 
                    transform hover:scale-105"
            >
                Tutup
            </button>
        </div>

    </div>
</div>