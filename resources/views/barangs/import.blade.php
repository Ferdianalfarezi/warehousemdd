<!-- IMPORT Modal -->
<div id="importModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">

        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Import Barang dari Excel</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Upload file MASTER_Dies.xlsx untuk import data barang dan proses</p>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800 space-y-1">
                    <p class="font-semibold">Format Excel yang didukung:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-blue-700">
                        <li>Header ada di <strong>baris ke-5</strong>, data mulai dari <strong>baris ke-7</strong></li>
                        <li>Kolom <strong>DELIVERY PART CODE</strong> → kode barang</li>
                        <li>Kolom <strong>CHILD PART CODE</strong> → kode part (harus sudah ada di tabel Parts)</li>
                        <li>Kolom <strong>PROSES NAME</strong> &amp; <strong>PROSES NO</strong> → disimpan di detail barang</li>
                        <li>Satu barang bisa memiliki <strong>multiple baris proses</strong> (misal: 1/5, 2/5, dst.)</li>
                    </ul>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-5">

                <!-- Left: Upload Area -->
                <div class="md:col-span-2 space-y-4">

                    <!-- Drop Zone -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">File Excel</label>
                        <div id="importDropZone"
                             class="border-2 border-dashed border-gray-300 rounded-xl text-center p-6 cursor-pointer transition hover:border-black hover:bg-gray-50"
                             ondragover="event.preventDefault(); document.getElementById('importDropZone').classList.add('border-black','bg-gray-50')"
                             ondragleave="document.getElementById('importDropZone').classList.remove('border-black','bg-gray-50')"
                             ondrop="handleImportDrop(event)"
                             onclick="document.getElementById('importFileInput').click()">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <p class="text-sm text-gray-500 mb-1">Drag &amp; drop file di sini</p>
                            <p class="text-xs text-gray-400">atau klik untuk memilih file</p>
                            <p id="importFileName" class="mt-3 text-sm font-semibold text-green-600 hidden"></p>
                        </div>
                        <input type="file" id="importFileInput" accept=".xlsx,.xls" class="hidden" onchange="handleImportFileSelect(this)">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button id="importBtnPreview" onclick="importPreviewFile()" disabled
                                class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </button>
                        <button id="importBtnImport" onclick="runImport()" disabled
                                class="flex-1 px-4 py-2.5 rounded-lg bg-green-600 text-white font-semibold text-sm hover:bg-green-700 transition disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Import
                        </button>
                    </div>

                    <!-- Progress Bar -->
                    <div id="importProgressArea" class="hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="importProgressBar" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p id="importProgressText" class="text-xs text-gray-500 mt-1.5">Memproses...</p>
                    </div>

                    <!-- Result Box -->
                    <div id="importResultBox" class="hidden rounded-xl border p-4 space-y-3">
                        <div id="importResultMsg" class="flex items-center gap-2 text-sm font-semibold"></div>
                        <div id="importResultStats" class="grid grid-cols-3 gap-2 text-center"></div>
                        <div id="importResultNotFound" class="hidden">
                            <p class="text-xs text-gray-500 font-semibold mb-1">Part Code tidak ditemukan di DB:</p>
                            <div id="importNotFoundList" class="bg-gray-50 rounded-lg p-2 max-h-28 overflow-y-auto text-xs font-mono space-y-0.5"></div>
                        </div>
                    </div>
                </div>

                <!-- Right: Preview Table -->
                <div class="md:col-span-3">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700">Preview Data</label>
                        <span id="importTotalRows" class="text-xs text-gray-400"></span>
                    </div>
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto max-h-80">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Delivery Code</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Child Code</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Part Name</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Proses</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                    </tr>
                                </thead>
                                <tbody id="importPreviewBody" class="divide-y divide-gray-100">
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center">
                                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm text-gray-400">Upload file untuk melihat preview</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">* Menampilkan 10 baris pertama</p>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-gray-200 px-6 py-4 flex justify-end">
            <button onclick="closeImportModal()"
                class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                Tutup
            </button>
        </div>
    </div>
</div>