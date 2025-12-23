<!-- REQUEST TO WAREHOUSE Modal -->
<div id="requestModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Request Part ke Warehouse
            </h2>
            <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="requestForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="requestPartId" name="part_id">
            
            <!-- Part Info Display -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <img id="requestPartImage" src="" alt="Part" 
                             class="w-20 h-20 rounded-lg object-cover border-2 border-white shadow-md"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22%3E%3Crect width=%2280%22 height=%2280%22 fill=%22%23dbeafe%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2214%22 fill=%22%233b82f6%22%3ENo Image%3C/text%3E%3C/svg%3E';">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Kode Part</p>
                        <p id="requestPartCode" class="font-bold text-gray-900 text-lg truncate"></p>
                        <p class="text-sm text-gray-700 mt-1 line-clamp-2" id="requestPartName"></p>
                        <div class="flex items-center mt-2 space-x-4">
                            <div class="text-xs text-gray-600">
                                <span class="font-medium">Stock:</span>
                                <span id="requestPartStock" class="font-bold text-blue-600"></span>
                                <span id="requestPartUnit" class="text-gray-500"></span>
                            </div>
                            <div class="text-xs text-gray-600">
                                <span class="font-medium">ID PUD:</span>
                                <span id="requestPartIdPud" class="font-bold text-blue-600"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quantity Input -->
            <div>
                <label for="requestQuantity" class="block text-sm font-semibold text-gray-700 mb-2">
                    Jumlah Request <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="number" 
                        id="requestQuantity" 
                        name="quantity"
                        class="w-full px-4 py-3 pr-16 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Masukkan jumlah"
                        min="1"
                        required
                    >
                    <span id="requestUnitDisplay" class="absolute right-4 top-3.5 text-gray-500 font-semibold"></span>
                </div>
                <span class="text-red-500 text-sm error-message" id="error-request-quantity"></span>
            </div>

            <!-- Keterangan Input -->
            <div>
                <label for="requestKeterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                    Keterangan (Opsional)
                </label>
                <textarea 
                    id="requestKeterangan" 
                    name="keterangan"
                    rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition resize-none"
                    placeholder="Tambahkan keterangan atau catatan untuk request ini..."
                    maxlength="500"
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                    <span class="text-red-500 text-sm error-message" id="error-request-keterangan"></span>
                    <span class="text-xs text-gray-500">
                        <span id="keteranganCount">0</span>/500
                    </span>
                </div>
            </div>

            <!-- Warning Note -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start space-x-2">
                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs text-yellow-800">
                    Request akan langsung dikirim ke sistem warehouse dan masuk ke antrean approval.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeRequestModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" id="submitRequestBtn"
                    class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span id="submitBtnText">Ajukan Request</span>
                </button>
            </div>
        </form>
    </div>
</div>