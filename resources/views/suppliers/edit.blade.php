<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Edit Supplier</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="editSupplierId" name="supplier_id">

            <!-- Nama Supplier -->
            <div>
                <label for="editNama" class="block text-sm font-semibold text-gray-700 mb-2">Nama Supplier</label>
                <input 
                    type="text" 
                    id="editNama" 
                    name="nama" 
                    required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                    placeholder="Enter supplier name"
                >
                <span class="text-red-500 text-sm error-message" id="error-edit-nama"></span>
            </div>

            <!-- Alamat -->
            <div>
                <label for="editAlamat" class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                <textarea 
                    id="editAlamat" 
                    name="alamat" 
                    rows="4" 
                    required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition resize-none"
                    placeholder="Enter supplier address"
                ></textarea>
                <span class="text-red-500 text-sm error-message" id="error-edit-alamat"></span>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button 
                    type="button" 
                    onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition"
                >
                    Update
                </button>
            </div>
        </form>
    </div>
</div>