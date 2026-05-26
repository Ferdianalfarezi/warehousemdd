<!-- DETAIL Modal -->
<div id="detailModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div id="detailModalContent" 
         class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0">
        
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl z-10">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Detail Barang</h2>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Kode Barang</p>
                    <p id="detailKodeBarang" class="text-sm font-bold text-gray-900 font-mono"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 col-span-1 md:col-span-2">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Nama</p>
                    <p id="detailNama" class="text-sm font-semibold text-gray-900"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Customer</p>
                    <p id="detailCust" class="text-sm text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Model</p>
                    <p id="detailModel" class="text-sm text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Supplier</p>
                    <p id="detailSupplier" class="text-sm text-gray-800"></p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4 col-span-2 md:col-span-3">
                    <p class="text-xs text-gray-500 font-semibold uppercase mb-1">Address / Location</p>
                    <p id="detailAddress" class="text-sm text-gray-800"></p>
                </div>
            </div>

            <!-- Dies Details Table -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Dies Details (Child Parts)</span>
                </h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Child Part Code</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Part Name</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cust</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Model</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Proses Name</th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Proses No</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="detailDiesTableBody">
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Parts Table -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Parts Required</span>
                </h3>
                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Part</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Part</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="detailPartsTableBody">
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t border-gray-200 px-6 py-4 flex justify-end">
            <button onclick="closeDetailModal()"
                class="px-6 py-3 rounded-lg bg-gray-900 text-white font-semibold hover:bg-gray-700 transition">
                Tutup
            </button>
        </div>
    </div>
</div>