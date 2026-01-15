{{-- resources/views/request-parts/detail-modal.blade.php --}}

<!-- REQUEST DETAIL Modal -->
<div id="requestDetailModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Detail Request Part
            </h2>
            <button onclick="closeRequestDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
            <div class="p-6 space-y-6">
                
                <!-- Request Info Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-5 border border-blue-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Request Number</p>
                            <p id="detailRequestNumber" class="text-lg font-bold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Status</p>
                            <div id="detailStatus"></div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Requester</p>
                            <p id="detailRequester" class="text-sm font-semibold text-gray-900"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Department</p>
                            <p id="detailDepartment" class="text-sm text-gray-700"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Tanggal Request</p>
                            <p id="detailTanggal" class="text-sm text-gray-700"></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Keterangan</p>
                            <p id="detailKeterangan" class="text-sm text-gray-700"></p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-blue-200">
                        <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Catatan</p>
                        <p id="detailCatatan" class="text-sm text-gray-700"></p>
                    </div>
                </div>

                <!-- Items Table -->
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        List Part yang Direquest
                    </h3>
                    
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kode Part</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Part</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="detailItemsBody" class="divide-y divide-gray-200 bg-white">
                                    <!-- Items will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
            <div class="flex justify-end">
                <button 
                    onclick="closeRequestDetailModal()"
                    class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>