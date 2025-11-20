<!-- Create Schedule Modal -->
<div id="createModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Tambah Schedule Service</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="createScheduleForm" class="p-6 space-y-6">
            @csrf

            <div class="mb-4">
                <label for="createBarangId" class="block text-sm font-medium text-gray-700 mb-2">Pilih Barang</label>
                <select 
                    id="createBarangId" 
                    name="barang_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition select2"
                >
                    <option value="">Pilih Barang</option>
                    <!-- Options akan diisi oleh JavaScript -->
                </select>
                <div id="error-create-barang_id" class="error-message text-red-500 text-xs mt-1"></div>
            </div>

            <!-- Mulai Service -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mulai Service *</label>
                <input 
                    type="date" 
                    name="mulai_service" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                <p id="error-create-mulai_service" class="error-message text-red-500 text-sm mt-1"></p>
            </div>

            <!-- Periode Configuration -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Periode *</label>
                    <select 
                        name="periode" 
                        required
                        onchange="updateIntervalLabel()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="harian">Harian</option>
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="custom">Custom</option>
                    </select>
                    <p id="error-create-periode" class="error-message text-red-500 text-sm mt-1"></p>
                </div>

                <div>
                    <label id="intervalLabel" class="block text-sm font-medium text-gray-700 mb-2">Interval (hari) *</label>
                    <input 
                        type="number" 
                        name="interval_value" 
                        min="1" 
                        value="1" 
                        required
                        oninput="updateIntervalLabel()"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                    <p id="error-create-interval_value" class="error-message text-red-500 text-sm mt-1"></p>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Preview:</span> Akan dijadwalkan setiap: 
                    <span id="previewInterval" class="font-bold">1 hari</span>
                </p>
            </div>

            <!-- Error Messages -->
            <div id="createErrors" class="hidden">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="text-sm text-red-600 space-y-1">
                        <!-- Errors will be inserted here -->
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeCreateModal()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Schedule</span>
                </button>
            </div>
        </form>
    </div>
</div>