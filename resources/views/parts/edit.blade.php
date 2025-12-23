<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900">Edit Part</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="editPartId" name="part_id">

            <div class="grid grid-cols-2 gap-4">
                <!-- Kode Barang -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Part</label>
                    <input type="text" id="editKodePart" name="kode_part" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., PRT-001">
                    <span class="text-red-500 text-sm error-message" id="error-edit-kode_part"></span>
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Part</label>
                    <input type="text" id="editNama" name="nama" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter part name">
                    <span class="text-red-500 text-sm error-message" id="error-edit-nama"></span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                    <input type="number" id="editStock" name="stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-edit-stock"></span>
                </div>

                <!-- Min Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Min Stock</label>
                    <input type="number" id="editMinStock" name="min_stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-edit-min_stock"></span>
                </div>

                <!-- Max Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Stock</label>
                    <input type="number" id="editMaxStock" name="max_stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-edit-max_stock"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Satuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                    <select id="editSatuan" name="satuan" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="pcs">Pcs</option>
                        <option value="kg">Kg</option>
                        <option value="liter">Liter</option>
                        <option value="meter">Meter</option>
                        <option value="box">Box</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-edit-satuan"></span>
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Supplier</label>
                    <select id="editSupplierId" name="supplier_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-edit-supplier_id"></span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Address -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address/Location</label>
                    <input type="text" id="editAddress" name="address"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., Rack A1">
                    <span class="text-red-500 text-sm error-message" id="error-edit-address"></span>
                </div>

                <!-- Line -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Line</label>
                    <input type="text" id="editLine" name="line"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., Line 1">
                    <span class="text-red-500 text-sm error-message" id="error-edit-line"></span>
                </div>

                <!-- ID PUD -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">ID PUD</label>
                    <input type="number" id="editIdPud" name="id_pud"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter ID PUD">
                    <span class="text-red-500 text-sm error-message" id="error-edit-id_pud"></span>
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Image (Optional - Leave empty to keep current)</label>
                <input type="file" id="editGambar" name="gambar" accept="image/*" 
                    onchange="previewEditImage(event)"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                <span class="text-red-500 text-sm error-message" id="error-edit-gambar"></span>
                
                <!-- Image Preview - ALWAYS VISIBLE -->
                <div id="editPreviewContainer" class="mt-3">
                    <p class="text-xs text-gray-500 mb-2">Current Image:</p>
                    <div class="relative inline-block">
                        <img id="editPreview" src="" alt="Preview" class="w-32 h-32 rounded-lg object-cover border-2 border-gray-300">
                        <p id="noImageText" class="text-sm text-gray-400 hidden mt-2">No image uploaded</p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Update Part
                </button>
            </div>
        </form>
    </div>
</div>