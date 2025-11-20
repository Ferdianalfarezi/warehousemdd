<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900">Add Part</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="createForm" class="p-6 space-y-4" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <!-- Kode Part -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Part</label>
                    <input type="text" id="createKodePart" name="kode_part" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., PRT-001">
                    <span class="text-red-500 text-sm error-message" id="error-create-kode_part"></span>
                </div>

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Part</label>
                    <input type="text" id="createNama" name="nama" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="Enter part name">
                    <span class="text-red-500 text-sm error-message" id="error-create-nama"></span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <!-- Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                    <input type="number" id="createStock" name="stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-create-stock"></span>
                </div>

                <!-- Min Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Min Stock</label>
                    <input type="number" id="createMinStock" name="min_stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-create-min_stock"></span>
                </div>

                <!-- Max Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Max Stock</label>
                    <input type="number" id="createMaxStock" name="max_stock" required min="0"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <span class="text-red-500 text-sm error-message" id="error-create-max_stock"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Satuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                    <select id="createSatuan" name="satuan" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        <option value="pcs">Pcs</option>
                        <option value="kg">Kg</option>
                        <option value="liter">Liter</option>
                        <option value="meter">Meter</option>
                        <option value="box">Box</option>
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-satuan"></span>
                </div>

                <!-- Supplier -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Supplier</label>
                    <select id="createSupplierId" name="supplier_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition select2">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-sm error-message" id="error-create-supplier_id"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Address -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address/Location</label>
                    <input type="text" id="createAddress" name="address"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., Rack A1">
                    <span class="text-red-500 text-sm error-message" id="error-create-address"></span>
                </div>

                <!-- Line -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Line</label>
                    <input type="text" id="createLine" name="line"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition"
                        placeholder="e.g., Line 1">
                    <span class="text-red-500 text-sm error-message" id="error-create-line"></span>
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                <input type="file" id="createGambar" name="gambar" accept="image/*" onchange="previewImage(event, 'createPreview')"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                <span class="text-red-500 text-sm error-message" id="error-create-gambar"></span>
                
                <!-- Image Preview -->
                <div id="createPreviewContainer" class="mt-3 hidden">
                    <img id="createPreview" class="w-32 h-32 rounded-lg object-cover border-2 border-gray-300">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Save Part
                </button>
            </div>
        </form>
    </div>
</div>