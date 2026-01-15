{{-- resources/views/parts/request-part.blade.php --}}

<style>
    /* Modal Animation Styles */
    #requestPartModal {
        background-color: rgba(0, 0, 0, 0);
        backdrop-filter: blur(0px);
        transition: background-color 0.3s ease, backdrop-filter 0.3s ease;
    }
    
    #requestPartModal.modal-fade-in {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }
    
    #requestPartModal > div {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    #requestPartModal.modal-fade-in > div {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
</style>

<!-- REQUEST PART Modal -->
<div id="requestPartModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-gradient-to-r from-blue-50 to-blue-100 z-10">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Request Parts ke Warehouse
            </h2>
            <button onclick="closeRequestPartModal()" class="text-gray-400 hover:text-gray-600 hover:rotate-90 transition-all duration-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="requestPartForm" class="flex flex-col h-full">
            @csrf
            
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Search Part -->
                <div id="partSearchContainer" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cari & Tambah Part
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="partSearchInput"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                            placeholder="Ketik kode atau nama part untuk mencari..."
                            autocomplete="off"
                        >
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div 
                        id="partSearchResults" 
                        class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto"
                    >
                        <!-- Results will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Selected Items Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">Daftar Part yang Akan Di-request</h3>
                    </div>
                    
                    <div class="overflow-x-auto max-h-72">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Part</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Part</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Stock Saat Ini</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity Request</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="requestPartItemsBody" class="divide-y divide-gray-200">
                                <!-- Items will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="requestPartEmptyState" class="p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="mt-3 text-gray-500 font-medium">Belum ada part yang ditambahkan</p>
                        <p class="text-sm text-gray-400">Gunakan pencarian di atas untuk menambahkan part</p>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="requestPartCatatan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Request (Opsional)
                    </label>
                    <textarea 
                        id="requestPartCatatan" 
                        name="catatan"
                        rows="3"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition resize-none"
                        placeholder="Tambahkan catatan untuk request ini (mis: urgency, project name, dll)..."
                        maxlength="1000"
                    ></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500">
                            <span id="requestCatatanCount">0</span>/1000
                        </span>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Informasi Request:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Hanya part yang sudah di-mapping ke warehouse (memiliki ID PUD) yang dapat di-request</li>
                            <li>Request akan dikirim ke sistem warehouse untuk diproses</li>
                            <li>Status akan otomatis tersinkronisasi dengan warehouse</li>
                            <li>Stock akan bertambah setelah barang diterima dan diverifikasi</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <button 
                        type="button" 
                        onclick="closeRequestPartModal()"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        id="submitRequestPartBtn"
                        disabled
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submitRequestBtnText">Kirim Request</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>