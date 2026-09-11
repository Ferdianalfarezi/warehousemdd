<!-- Import Schedule Modal -->
<div id="importModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Import Schedule (Excel)</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="importScheduleForm" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">File Excel Master Preventive (.xlsx)</label>
                <input type="file" id="importScheduleFile" name="excel_file" accept=".xlsx,.xls" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                <p class="text-xs text-gray-500 mt-2">
                    Data dibaca dari sheet <b>MASTER</b>. Part harus sudah terdaftar di menu Barang. Kalau part sudah punya Schedule sebelumnya, akan ditimpa dengan hasil hitungan dari file ini.
                </p>
                <span class="text-red-500 text-sm" id="error-import-excel_file"></span>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeImportModal()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" id="importScheduleSubmitBtn"
                    class="px-6 py-3 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importScheduleForm').reset();
        document.getElementById('error-import-excel_file').textContent = '';

        const modal = document.getElementById('importModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modal.classList.add('modal-fade-in'), 10);
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.remove('modal-fade-in');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    document.getElementById('importScheduleForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        document.getElementById('error-import-excel_file').textContent = '';

        const btn = document.getElementById('importScheduleSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Mengupload & memproses...';

        const formData = new FormData(this);

        try {
            const response = await fetch('/schedules/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                const s = result.summary;

                const skippedBarangHtml = s.skipped_no_barang_count > 0
                    ? `<details class="mt-2 text-left">
                         <summary class="cursor-pointer text-sm text-gray-600">Lihat ${s.skipped_no_barang_count} part (barang belum terdaftar)</summary>
                         <div class="max-h-32 overflow-y-auto text-xs text-gray-500 mt-1 border rounded p-2">${s.skipped_no_barang_list.join(', ')}</div>
                       </details>`
                    : '';

                const skippedMarksHtml = s.skipped_no_marks_count > 0
                    ? `<details class="mt-2 text-left">
                         <summary class="cursor-pointer text-sm text-gray-600">Lihat ${s.skipped_no_marks_count} part (tidak ada tanda jadwal)</summary>
                         <div class="max-h-32 overflow-y-auto text-xs text-gray-500 mt-1 border rounded p-2">${s.skipped_no_marks_list.join(', ')}</div>
                       </details>`
                    : '';

                await Swal.fire({
                    icon: 'success',
                    title: 'Import Selesai!',
                    html: `
                        <div class="text-left text-sm space-y-1">
                            <p>✅ <b>${s.imported_count}</b> schedule berhasil diimport/diupdate</p>
                            <p>⚠️ <b>${s.skipped_no_barang_count}</b> part di-skip karena barang belum terdaftar</p>
                            <p>⚠️ <b>${s.skipped_no_marks_count}</b> part di-skip karena tidak ada tanda jadwal</p>
                        </div>
                        ${skippedBarangHtml}
                        ${skippedMarksHtml}
                    `,
                    confirmButtonText: 'OK',
                }).then(() => location.reload());
            } else {
                if (result.errors && result.errors.excel_file) {
                    document.getElementById('error-import-excel_file').textContent = result.errors.excel_file[0];
                }
                Swal.fire('Gagal!', result.message || 'Terjadi kesalahan saat import', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal mengupload file!', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Upload & Import';
        }
    });
</script>
@endpush