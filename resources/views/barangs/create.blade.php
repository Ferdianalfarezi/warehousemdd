<!-- CREATE Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

        <div class="sticky top-0 bg-white border-b border-gray-200 px-5 py-3 rounded-t-2xl z-10 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Add New Barang</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="createForm" class="p-5 space-y-3">

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kode Barang</label>
                    <input type="text" id="createKodeBarang" name="kode_barang" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-kode_barang"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama / Part Name</label>
                    <input type="text" id="createNama" name="nama" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-nama"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Supplier</label>
                    <select id="createSupplierId" name="supplier_id" required
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs error-message" id="error-create-supplier_id"></span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Customer</label>
                    <input type="text" id="createCust" name="cust" placeholder="e.g. ADM, AAA"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-cust"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Model</label>
                    <input type="text" id="createModel" name="model" placeholder="e.g. D01N"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-model"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Line</label>
                    <select id="createLineId" name="line_id"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                        <option value="">Select Line</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}">{{ $line->nama_line }}{{ $line->mesin ? ' — ' . $line->mesin : '' }}</option>
                        @endforeach
                    </select>
                    <span class="text-red-500 text-xs error-message" id="error-create-line_id"></span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Address / Location</label>
                    <input type="text" id="createAddress" name="address"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-address"></span>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Image (Optional)</label>
                    <input type="file" id="createGambar" name="gambar" accept="image/*" onchange="previewImage(event, 'createPreview')"
                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    <span class="text-red-500 text-xs error-message" id="error-create-gambar"></span>
                    <div id="createPreviewContainer" class="mt-2 hidden">
                        <img id="createPreview" class="w-16 h-16 rounded-lg object-cover border border-gray-300">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900">Parts Required</h3>
                    <button type="button" onclick="addPartRow()"
                        class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Part</span>
                    </button>
                </div>
                <div id="partsContainer" class="space-y-2"></div>
                <span class="text-red-500 text-xs error-message" id="error-create-parts"></span>
            </div>

            <div class="border-t border-gray-200 pt-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-900">Dies Details (Child Parts)</h3>
                    <button type="button" onclick="addDiesDetailRow('diesDetailsContainer')"
                        class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add Row</span>
                    </button>
                </div>
                <div id="diesDetailsContainer" class="space-y-1.5"></div>
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="closeCreateModal()"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Add Barang
                </button>
            </div>
        </form>
    </div>
</div>