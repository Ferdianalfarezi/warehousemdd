<!-- IMPORT Modal -->
<div id="importModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Import Check Indicator (Excel)</h2>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="importForm" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">File Excel (.xlsx)</label>
                <input type="file" id="importFile" name="excel_file" accept=".xlsx,.xls" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                <p class="text-xs text-gray-500 mt-2">
                    Part No harus sudah terdaftar di menu Barang. Kalau part ini sudah punya Check Indicator sebelumnya, data lama akan ditimpa total oleh isi file ini.
                </p>
                <span class="text-red-500 text-sm" id="error-import-excel_file"></span>
            </div>

            <div class="flex space-x-3 pt-2">
                <button type="button" onclick="closeImportModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" id="importSubmitBtn"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Upload & Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openImportModal() {
        document.getElementById('importForm').reset();
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

    document.getElementById('importForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        document.getElementById('error-import-excel_file').textContent = '';

        const btn = document.getElementById('importSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Mengupload...';

        const formData = new FormData(this);

        try {
            const response = await fetch('/check-indicators/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Import Berhasil!',
                    text: result.message,
                    showConfirmButton: false,
                    timer: 2000,
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