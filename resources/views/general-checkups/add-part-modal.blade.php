<!-- Add Part Modal -->
<div id="addPartModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Tambah Part Replacement</h3>
            <button onclick="closeAddPartModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addPartForm" onsubmit="event.preventDefault(); addPart();" class="p-6 space-y-4">
            
            <!-- Part Selection -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Part <span class="text-red-500">*</span>
                </label>
                <select 
                    id="partSelect"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    <option value="">Pilih Part</option>
                </select>
            </div>

            <!-- Quantity -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Quantity <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    id="partQuantity"
                    min="1"
                    required
                    placeholder="Masukkan jumlah part"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan (Opsional)
                </label>
                <textarea 
                    id="partCatatan"
                    placeholder="Tambahkan catatan untuk part replacement ini..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition resize-none"
                    rows="3"
                ></textarea>
            </div>

        </form>

        <!-- Modal Footer -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3 rounded-b-2xl">
            <button 
                type="button"
                onclick="closeAddPartModal()"
                class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition"
            >
                Batal
            </button>
            <button 
                type="button"
                onclick="addPart()"
                class="px-6 py-2 bg-black text-white rounded-lg font-medium hover:bg-gray-800 transition"
            >
                Tambah Part
            </button>
        </div>
    </div>
</div>