{{-- resources/views/parts/bulk-request.blade.php --}}

<!-- BULK REQUEST Modal -->
<div id="bulkRequestModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all flex flex-col my-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100 flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Request Parts ke Warehouse
            </h2>
            <button onclick="closeBulkRequestModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body - Scrollable -->
        <form id="bulkRequestForm" class="flex flex-col flex-1 overflow-hidden">
            @csrf
            
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Items Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto max-h-72">
                        <table class="w-full">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Part</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Part</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bulkRequestItemsBody" class="divide-y divide-gray-200">
                                <!-- Items will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Catatan -->
                <div>
                    <label for="bulkCatatan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Request (Opsional)
                    </label>
                    <textarea 
                        id="bulkCatatan" 
                        name="catatan"
                        rows="3"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition resize-none"
                        placeholder="Tambahkan catatan untuk request ini (mis: urgency, project name, dll)..."
                        maxlength="1000"
                    ></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-gray-500">
                            <span id="catatanCount">0</span>/1000
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
                            <li>Request akan dikirim ke sistem warehouse untuk diproses</li>
                            <li>Status akan otomatis tersinkronisasi dengan warehouse</li>
                            <li>Verifikasi diperlukan saat barang sudah "Completed" di warehouse</li>
                            <li>Stock akan bertambah setelah verifikasi berhasil</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Footer - Fixed -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <button 
                        type="button" 
                        onclick="closeBulkRequestModal()"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        id="submitBulkRequestBtn"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span id="submitBulkBtnText">Kirim Request</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>