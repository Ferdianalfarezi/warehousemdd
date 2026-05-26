<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-5 py-3 rounded-t-2xl z-10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Edit Barang</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editForm" class="p-5 space-y-3">
            <input type="hidden" id="editBarangId" name="id">

            <!-- Row 1: Kode + Nama + Supplier -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Barang</label>
                    <input type="text" id="editKodeBarang" name="kode_barang" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-kode_barang"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama / Part Name</label>
                    <input type="text" id="editNama" name="nama" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-nama"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Supplier</label>
                    <select id="editSupplierId" name="supplier_id" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs error-message" id="error-edit-supplier_id"></span>
                </div>
            </div>

            <!-- Row 2: Cust + Model + Line -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Customer</label>
                    <input type="text" id="editCust" name="cust" placeholder="e.g. ADM, AAA"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-cust"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Model</label>
                    <input type="text" id="editModel" name="model" placeholder="e.g. D01N"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-model"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Line</label>
                    <input type="text" id="editLine" name="line"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-line"></span>
                </div>
            </div>

            <!-- Row 3: Address + Image -->
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Address / Location</label>
                    <input type="text" id="editAddress" name="address"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-edit-address"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Image (Optional)</label>
                    <input type="file" id="editGambar" name="gambar" accept="image/*" onchange="previewEditImage(event)"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <div class="mt-2 flex items-center space-x-2">
                        <img id="editPreview" src="" style="display:none;" class="w-16 h-16 rounded-lg object-cover border border-gray-300">
                        <p id="noImageText" class="text-xs text-gray-400">No image</p>
                    </div>
                </div>
            </div>

            <!-- Parts Section -->
            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900">Parts Required</h3>
                    <button type="button" onclick="addEditPartRow()"
                        class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Part</span>
                    </button>
                </div>
                <div id="editPartsContainer" class="space-y-2"></div>
                <span class="text-red-500 text-xs error-message" id="error-edit-parts"></span>
            </div>

            <!-- Dies Details Section -->
            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900">Dies Details (Child Parts)</h3>
                    <button type="button" onclick="addDiesDetailRow('editDiesDetailsContainer')"
                        class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Row</span>
                    </button>
                </div>
                <div class="grid grid-cols-12 gap-2 px-2 mb-1 text-xs font-semibold text-gray-400 uppercase">
                    <div class="col-span-2">Child Part Code</div>
                    <div class="col-span-3">Part Name</div>
                    <div class="col-span-1">Cust</div>
                    <div class="col-span-2">Model</div>
                    <div class="col-span-2">Proses Name</div>
                    <div class="col-span-1">No</div>
                    <div class="col-span-1"></div>
                </div>
                <div id="editDiesDetailsContainer" class="space-y-1.5"></div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Update Barang
                </button>
            </div>
        </form>
    </div>
</div>