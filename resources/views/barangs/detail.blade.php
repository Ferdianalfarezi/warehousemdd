<!-- DETAIL Modal -->
<div id="detailModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50 backdrop-blur-sm transition-all duration-300">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 opacity-0" id="detailModalContent">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Barang Details</h2>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-6">
            <!-- Barang Information -->
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Barang Information</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Kode Barang</p>
                        <p class="text-base font-semibold text-gray-900" id="detailKodeBarang">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Nama</p>
                        <p class="text-base font-semibold text-gray-900" id="detailNama">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Supplier</p>
                        <p class="text-base font-semibold text-gray-900" id="detailSupplier">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Address</p>
                        <p class="text-base font-semibold text-gray-900" id="detailAddress">-</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Line</p>
                        <p class="text-base font-semibold text-gray-900" id="detailLine">-</p>
                    </div>
                </div>
            </div>

            <!-- Parts Table -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Parts Used</h3>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Part</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Part</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="detailPartsTableBody">
                            <!-- Parts will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 rounded-b-2xl">
            <div class="flex items-center justify-end">
                <button type="button" onclick="closeDetailModal()"
                    class="px-6 py-3 rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>