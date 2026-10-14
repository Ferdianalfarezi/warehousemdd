{{-- Form pilih tindakan NG. $sid = standard id, $cid = checkup id --}}
<!-- Dropdown untuk pilih action type.
     Inhouse & Outhouse sementara di-hide, tinggal Ganti Part -> dropdown disembunyikan & form Ganti Part langsung tampil.
     Untuk mengaktifkan lagi: buka komentar opsi di bawah, hapus style display:none, dan kembalikan class "hidden" di #partForm. -->
<div class="mb-3" style="display:none;">
    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tindakan:</label>
    <select
        id="actionTypeSelect-{{ $sid }}"
        onchange="showActionForm({{ $sid }})"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
    >
        <option value="part" selected>Ganti Part</option>
        {{-- <option value="inhouse">Proses Inhouse</option> --}}
        {{-- <option value="outhouse">Proses Outhouse</option> --}}
    </select>
</div>

<!-- Form Ganti Part -->
<div id="partForm-{{ $sid }}">
    <button 
        type="button"
        onclick="openAddPartModal({{ $cid }}, {{ $sid }})"
        class="inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        Tambah Part
    </button>

    <!-- Part List -->
    <div class="mt-3 space-y-2" id="partList-{{ $sid }}">
        <!-- Will be populated dynamically -->
    </div>
</div>

<!-- Form Inhouse -->
<div id="inhouseForm-{{ $sid }}" class="hidden space-y-3">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Problem:</label>
        <textarea 
            id="inhouseProblem-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
            rows="2"
            placeholder="Jelaskan masalah yang terjadi..."
        ></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Proses yang Dilakukan:</label>
        <textarea 
            id="inhouseProses-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
            rows="2"
            placeholder="Jelaskan proses perbaikan yang akan dilakukan..."
        ></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mesin:</label>
        <input 
            type="text"
            id="inhouseMesin-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
            placeholder="Nama mesin yang akan diperbaiki..."
        >
    </div>
    <button 
        type="button"
        onclick="submitInhouseRequest({{ $cid }}, {{ $sid }})"
        class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition"
    >
        Submit Permintaan Inhouse
    </button>
</div>

<!-- Form Outhouse -->
<div id="outhouseForm-{{ $sid }}" class="hidden space-y-3">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Problem:</label>
        <textarea 
            id="outhouseProblem-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
            rows="2"
            placeholder="Jelaskan masalah yang terjadi..."
        ></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mesin:</label>
        <input 
            type="text"
            id="outhouseMesin-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
            placeholder="Nama mesin yang akan diperbaiki..."
        >
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
        <input 
            type="text"
            id="outhouseSupplier-{{ $sid }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
            placeholder="Nama supplier..."
        >
    </div>
    <button 
        type="button"
        onclick="submitOuthouseRequest({{ $cid }}, {{ $sid }})"
        class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg font-medium hover:bg-purple-600 transition"
    >
        Submit Permintaan Outhouse
    </button>
</div>
