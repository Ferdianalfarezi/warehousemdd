{{-- resources/views/request_repairs/quick-add-barang.blade.php --}}
<div id="quickAddBarangModal" class="fixed inset-0 hidden items-center justify-center z-[60] p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-900">Tambah Part Baru</h2>
            <button type="button" onclick="closeQuickAddBarangModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-6">
            <p class="text-xs text-gray-400 mb-4">Data lengkap (parts, dies details, gambar) bisa dilengkapi nanti lewat menu Dies.</p>
            <form id="quickAddBarangForm" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Kode Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="kode_barang" id="qabKodeBarang"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    <p class="error-message text-xs text-red-500 mt-1" id="error-qab-kode_barang"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Nama / Part Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" id="qabNama"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    <p class="error-message text-xs text-red-500 mt-1" id="error-qab-nama"></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="qabSupplierId"
                        class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                        @endforeach
                    </select>
                    <p class="error-message text-xs text-red-500 mt-1" id="error-qab-supplier_id"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Customer</label>
                        <input type="text" name="cust" id="qabCust" placeholder="e.g. ADM, AAA"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                        <p class="error-message text-xs text-red-500 mt-1" id="error-qab-cust"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Model</label>
                        <input type="text" name="model" id="qabModel" placeholder="e.g. D01N"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                        <p class="error-message text-xs text-red-500 mt-1" id="error-qab-model"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Address / Location</label>
                    <input type="text" name="address" id="qabAddress"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    <p class="error-message text-xs text-red-500 mt-1" id="error-qab-address"></p>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-end space-x-3 flex-shrink-0">
            <button type="button" onclick="closeQuickAddBarangModal()"
                class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="submitQuickAddBarang()" id="qabSubmitBtn"
                class="px-5 py-2.5 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                Simpan & Pilih
            </button>
        </div>

    </div>
</div>