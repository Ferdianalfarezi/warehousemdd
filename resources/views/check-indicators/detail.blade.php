<!-- View Details Modal -->
<div id="viewDetailsModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900">Check Indicator Details</h2>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="viewDetailsContent" class="p-6">
            <!-- Content akan di-load secara dinamis -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function viewDetails(id) {
        try {
            const response = await fetch(`/check-indicators/${id}`);
            const data = await response.json();

            if (data.success) {
                const indicator = data.data;

                // Gabungkan Bagian + Standards dalam satu tabel
                let standardsHTML = '';
                indicator.standards.forEach((std, index) => {
                    standardsHTML += `
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">${index + 1}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">${indicator.nama_bagian}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">${std.poin}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${std.metode}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${std.standar}</td>
                        </tr>
                    `;
                });

                // Parts badge
                const partsHTML = indicator.barang.parts.map(part =>
                    `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mr-2 mb-2">
                        ${part.nama} (${part.pivot.quantity})
                    </span>`
                ).join('');

                document.getElementById('viewDetailsContent').innerHTML = `
                    <!-- Barang Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Barang Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 flex items-start space-x-4">
                            ${indicator.barang.gambar ?
                                `<img src="/storage/barangs/${indicator.barang.gambar}" class="w-24 h-24 rounded-lg object-cover border border-gray-200">` :
                                `<div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>`
                            }
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Kode Barang</p>
                                <p class="font-semibold text-gray-900">${indicator.barang.kode_barang}</p>

                                <p class="text-sm text-gray-500 mt-2">Nama Barang</p>
                                <p class="font-semibold text-gray-900">${indicator.barang.nama}</p>

                                <p class="text-sm text-gray-500 mt-2">Supplier</p>
                                <p class="font-semibold text-gray-900">${indicator.barang.supplier.nama}</p>

                                <p class="text-sm text-gray-500 mt-2">Parts Used</p>
                                <div class="mt-1">${partsHTML || '<span class="text-sm text-gray-500">No parts used</span>'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Bagian + Poin Pemeriksaan (digabung) -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Poin Pemeriksaan per Bagian</h3>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bagian</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Poin Pemeriksaan</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Metode</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Standar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${standardsHTML || '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Tidak ada poin pemeriksaan</td></tr>'}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                // Buka modal
                const modal = document.getElementById('viewDetailsModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.add('modal-fade-in'), 10);
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal memuat detail indicator', 'error');
        }
    }

    function closeViewModal() {
        const modal = document.getElementById('viewDetailsModal');
        modal.classList.remove('modal-fade-in');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>

<style>
    /* Modal Background Overlay */
    #viewDetailsModal,
    #createModal,
    #editModal {
        background-color: rgba(0, 0, 0, 0);
        backdrop-filter: blur(0px);
        transition: background-color 0.3s ease, backdrop-filter 0.3s ease;
    }

    #viewDetailsModal.modal-fade-in,
    #createModal.modal-fade-in,
    #editModal.modal-fade-in {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    /* Modal Content Animation */
    #viewDetailsModal > div,
    #createModal > div,
    #editModal > div {
        transform: scale(0.95) translateY(-20px);
        opacity: 0;
        transition: all 0.3s ease;
    }

    #viewDetailsModal.modal-fade-in > div,
    #createModal.modal-fade-in > div,
    #editModal.modal-fade-in > div {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
</style>
@endpush