{{-- resources/views/request_repairs/create.blade.php --}}
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">

        {{-- Header --}}
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-900">Tambah Request Repair</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div class="overflow-y-auto flex-1 p-6">
            <form id="createForm" class="space-y-5">
                @csrf

                {{-- Row 1: Tanggal + Group + Shift --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Tanggal Pengajuan <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_pengajuan" id="createTanggal"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-tanggal_pengajuan"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Group <span class="text-red-500">*</span>
                        </label>
                        <select name="group" id="createGroup"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">Pilih Group</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                        </select>
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-group"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Shift <span class="text-red-500">*</span>
                        </label>
                        <select name="shift" id="createShift"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">Pilih Shift</option>
                            <option value="Pagi">Pagi</option>
                            <option value="Malam">Malam</option>
                        </select>
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-shift"></p>
                    </div>
                </div>

                {{-- Row 2: Jumlah Stroke + Line/Mesin --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Jumlah Stroke <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="jumlah_stroke" id="createStroke" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black"
                            placeholder="0">
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-jumlah_stroke"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Line / Mesin</label>
                        <select name="line_id" id="createLineId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">Pilih Line / Mesin</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->nama_line }} — {{ $line->mesin }}</option>
                            @endforeach
                        </select>
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-line_id"></p>
                    </div>
                </div>

                {{-- Part No --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Part No <span class="text-red-500">*</span>
                    </label>
                    <select name="barang_id" id="createBarangId"
                        class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                    </select>
                    <p class="error-message text-xs text-red-500 mt-1" id="error-create-barang_id"></p>
                </div>

                {{-- Nama + Customer (auto-fill readonly) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Part</label>
                        <input type="text" id="createNamaDisplay" readonly
                            class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed"
                            placeholder="Otomatis terisi">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Customer</label>
                        <input type="text" id="createCustomerDisplay" readonly
                            class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600 cursor-not-allowed"
                            placeholder="Otomatis terisi">
                    </div>
                </div>

                {{-- Process No --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Process No</label>
                    <div class="flex space-x-2">
                        <select id="createProcessNoSelect"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">— Pilih dulu Part No —</option>
                        </select>
                        <input type="text" name="process_no" id="createProcessNoInput"
                            class="hidden flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black"
                            placeholder="Input manual process no">
                        <button type="button" id="createProcessNoToggleBtn"
                            onclick="toggleManualProcessNo('create')"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-600 hover:bg-gray-50 transition whitespace-nowrap">
                            Manual
                        </button>
                    </div>
                    <p class="error-message text-xs text-red-500 mt-1" id="error-create-process_no"></p>
                </div>

                {{-- Jenis + Kategori --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Jenis <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" id="createJenis"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">Pilih Jenis</option>
                            <option value="Milik Sendiri">Milik Sendiri</option>
                            <option value="Eksternal">Eksternal</option>
                        </select>
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-jenis"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            Kategori Problem <span class="text-red-500">*</span>
                        </label>
                        <select name="kategori_problem" id="createKategori"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
                            <option value="">Pilih Kategori</option>
                            <option value="Dies">Dies</option>
                            <option value="Burry">Burry</option>
                            <option value="Dimensi">Dimensi</option>
                            <option value="Human Error">Human Error</option>
                            <option value="Accessories">Accessories</option>
                        </select>
                        <p class="error-message text-xs text-red-500 mt-1" id="error-create-kategori_problem"></p>
                    </div>
                </div>

                {{-- Kekuatan Stock FG --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kekuatan Stock FG (Hari)</label>
                    <input type="number" name="kekuatan_stock_fg" id="createKekuatanStockFg" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black"
                        placeholder="0">
                    <p class="error-message text-xs text-red-500 mt-1" id="error-create-kekuatan_stock_fg"></p>
                </div>

                {{-- Detail Proyek --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Detail Proyek</label>
                    <textarea name="detail_proyek" id="createDetailProyek" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black resize-none"
                        placeholder="Deskripsi detail masalah / proyek..."></textarea>
                </div>

                {{-- Gambar --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar</label>
                    <input type="file" name="gambar" id="createGambar" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-medium hover:file:bg-gray-200">
                    <p class="text-xs text-gray-400 mt-1">Format JPG/PNG/WEBP, maks 2MB.</p>
                    <img id="createGambarPreview" class="hidden mt-2 rounded-lg border border-gray-200 max-h-40 object-cover" />
                    <p class="error-message text-xs text-red-500 mt-1" id="error-create-gambar"></p>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                <span class="w-2 h-2 rounded-full bg-slate-500 mr-1.5"></span>
                Status: Open
            </span>
            <div class="flex items-center space-x-3">
                <button type="button" onclick="closeCreateModal()"
                    class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" onclick="document.getElementById('createForm').requestSubmit()"
                    class="px-5 py-2.5 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Simpan
                </button>
            </div>
        </div>

    </div>
</div>