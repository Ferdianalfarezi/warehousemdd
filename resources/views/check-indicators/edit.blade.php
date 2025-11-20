<!-- EDIT Modal -->
<div id="editModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900">Edit Check Indicator</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editForm" class="p-6 space-y-6">
            @csrf
            <input type="hidden" id="editCheckIndicatorId" name="check_indicator_id">

            <!-- Select Barang -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Barang *</label>
                <select id="editBarangId" name="barang_id" required onchange="loadEditBarangDetails(this.value)"
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                    <option value="">-- Select Barang --</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->kode_barang }} - {{ $barang->nama }}</option>
                    @endforeach
                </select>
                <span class="text-red-500 text-sm error-message" id="error-edit-barang_id"></span>
            </div>

            <!-- Barang Details -->
            <div id="editBarangDetailsSection" class="hidden">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700 mb-3">Barang Details</h3>
                    <div class="flex items-start space-x-4">
                        <img id="editDetailGambar" src="" alt="Barang Image" class="w-20 h-20 rounded-lg object-cover border border-gray-300 hidden">
                        <div id="editNoDetailGambar" class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500">Kode Barang</p>
                                    <p id="editDetailKodeBarang" class="font-semibold text-gray-900">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Supplier</p>
                                    <p id="editDetailSupplier" class="font-semibold text-gray-900">-</p>
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500">Nama</p>
                                <p id="editDetailNama" class="font-semibold text-gray-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parts Used List -->
            <div id="editPartsUsedSection" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Parts Used in This Barang</label>
                <div id="editPartsUsedList" class="space-y-2 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <!-- Parts will be loaded here as list -->
                </div>
            </div>

            <!-- Bagian & Standards Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Bagian & Standards *</h3>
                </div>

                <div id="editBagianContainer">
                    <!-- Single Bagian card -->
                    <div class="bagian-card bg-gray-50 border-2 border-gray-200 rounded-xl p-5">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-black text-white rounded-lg flex items-center justify-center font-bold text-sm">
                                    1
                                </div>
                                <h4 class="font-bold text-gray-900">Bagian</h4>
                            </div>
                        </div>

                        <!-- Nama Bagian -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Bagian *</label>
                            <input type="text" id="editNamaBagian" name="bagian[1][nama_bagian]" required
                                placeholder="Contoh: Motor Utama, Belt Conveyor, Bearing, dll"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-black focus:ring-2 focus:ring-black transition">
                        </div>

                        <!-- Standards Header -->
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-semibold text-gray-700">Standards *</label>
                            <button type="button" onclick="addEditStandard()"
                                class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition flex items-center space-x-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>Tambah Standard</span>
                            </button>
                        </div>

                        <!-- Standards Container -->
                        <div class="standards-container space-y-3" id="editStandardsContainer">
                            <!-- Standards will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 bg-black text-white px-4 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
                    Update Check Indicator
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let editStandardCounter = 0;

    async function openEditModal(id) {
        try {
            const response = await fetch(`/check-indicators/${id}/edit`);
            const data = await response.json();
            
            if (data.success) {
                const indicator = data.data;
                
                // Set ID
                document.getElementById('editCheckIndicatorId').value = indicator.id;
                
                // Set Barang
                document.getElementById('editBarangId').value = indicator.barang_id;
                
                // Load barang details
                await loadEditBarangDetails(indicator.barang_id);
                
                // Set Nama Bagian
                document.getElementById('editNamaBagian').value = indicator.nama_bagian;
                
                // Clear and load standards
                const standardsContainer = document.getElementById('editStandardsContainer');
                standardsContainer.innerHTML = '';
                editStandardCounter = 0;
                
                indicator.standards.forEach((standard, index) => {
                    editStandardCounter++;
                    const standardHTML = `
                        <div class="standard-row bg-white border border-gray-300 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-500">Standard #${editStandardCounter}</span>
                                <button type="button" onclick="this.closest('.standard-row').remove()"
                                    class="text-red-500 hover:text-red-700 text-xs">
                                    Remove
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Poin</label>
                                    <input type="text" name="bagian[1][standards][${editStandardCounter}][poin]" 
                                        value="${standard.poin}" required
                                        placeholder="1, 2, A, B, dll"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Metode</label>
                                    <input type="text" name="bagian[1][standards][${editStandardCounter}][metode]" 
                                        value="${standard.metode}" required
                                        placeholder="Visual Check, Pengukuran, dll"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Standar</label>
                                    <input type="text" name="bagian[1][standards][${editStandardCounter}][standar]" 
                                        value="${standard.standar}" required
                                        placeholder="Tidak ada kerusakan, dll"
                                        class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                                </div>
                            </div>
                        </div>
                    `;
                    standardsContainer.innerHTML += standardHTML;
                });
                
                clearErrors();
                
                const modal = document.getElementById('editModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.add('modal-fade-in'), 10);
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load check indicator data', 'error');
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('modal-fade-in');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            clearErrors();
        }, 300);
    }

    async function loadEditBarangDetails(barangId) {
        if (!barangId) {
            document.getElementById('editBarangDetailsSection').classList.add('hidden');
            document.getElementById('editPartsUsedSection').classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`/barangs/${barangId}/details`);
            const data = await response.json();
            
            if (data.success) {
                const barang = data.data;
                
                // Update details
                document.getElementById('editDetailKodeBarang').textContent = barang.kode_barang;
                document.getElementById('editDetailNama').textContent = barang.nama;
                document.getElementById('editDetailSupplier').textContent = barang.supplier.nama;
                
                // Update image
                if (barang.gambar) {
                    document.getElementById('editDetailGambar').src = `/storage/barangs/${barang.gambar}`;
                    document.getElementById('editDetailGambar').classList.remove('hidden');
                    document.getElementById('editNoDetailGambar').classList.add('hidden');
                } else {
                    document.getElementById('editDetailGambar').classList.add('hidden');
                    document.getElementById('editNoDetailGambar').classList.remove('hidden');
                }
                
                // Update Parts Used List
                const partsUsedList = document.getElementById('editPartsUsedList');
                partsUsedList.innerHTML = '';
                
                if (barang.parts && barang.parts.length > 0) {
                    barang.parts.forEach((part, index) => {
                        partsUsedList.innerHTML += `
                            <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-4 py-3 hover:border-blue-300 transition">
                                <div class="flex items-center space-x-3">
                                    <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        ${index + 1}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">${part.nama}</p>
                                        <p class="text-xs text-gray-500">${part.kode_part}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Quantity</p>
                                    <p class="text-sm font-bold text-blue-600">${part.pivot.quantity}</p>
                                </div>
                            </div>
                        `;
                    });
                    
                    document.getElementById('editPartsUsedSection').classList.remove('hidden');
                } else {
                    partsUsedList.innerHTML = '<p class="text-sm text-gray-500 italic text-center py-4">No parts used in this barang</p>';
                    document.getElementById('editPartsUsedSection').classList.remove('hidden');
                }
                
                document.getElementById('editBarangDetailsSection').classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load barang details', 'error');
        }
    }

    function addEditStandard() {
        const container = document.getElementById('editStandardsContainer');
        editStandardCounter++;
        
        const standardHTML = `
            <div class="standard-row bg-white border border-gray-300 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500">Standard #${editStandardCounter}</span>
                    <button type="button" onclick="this.closest('.standard-row').remove()"
                        class="text-red-500 hover:text-red-700 text-xs">
                        Remove
                    </button>
                </div>
                
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Poin</label>
                        <input type="text" name="bagian[1][standards][${editStandardCounter}][poin]" required
                            placeholder="1, 2, A, B, dll"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Metode</label>
                        <input type="text" name="bagian[1][standards][${editStandardCounter}][metode]" required
                            placeholder="Visual Check, Pengukuran, dll"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Standar</label>
                        <input type="text" name="bagian[1][standards][${editStandardCounter}][standar]" required
                            placeholder="Tidak ada kerusakan, dll"
                            class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML += standardHTML;
    }

    // Submit Edit Form
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editCheckIndicatorId').value;
        
        // Convert FormData to JSON structure
        const data = {
            barang_id: formData.get('barang_id'),
            part_id: null,
            bagian: []
        };

        // Parse bagian data
        const bagianData = {};
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('bagian[')) {
                const matches = key.match(/bagian\[(\d+)\]\[(.+?)\](?:\[(\d+)\]\[(.+?)\])?/);
                if (matches) {
                    const bagianId = matches[1];
                    const field = matches[2];
                    
                    if (!bagianData[bagianId]) {
                        bagianData[bagianId] = { standards: [] };
                    }
                    
                    if (field === 'nama_bagian') {
                        bagianData[bagianId].nama_bagian = value;
                    } else if (field === 'standards') {
                        const standardId = matches[3];
                        const standardField = matches[4];
                        
                        if (!bagianData[bagianId].standards[standardId]) {
                            bagianData[bagianId].standards[standardId] = {};
                        }
                        
                        bagianData[bagianId].standards[standardId][standardField] = value;
                    }
                }
            }
        }

        // Convert to array and clean up
        data.bagian = Object.values(bagianData).map(b => ({
            nama_bagian: b.nama_bagian,
            standards: Object.values(b.standards).filter(s => s.poin && s.metode && s.standar)
        }));

        try {
            const response = await fetch(`/check-indicators/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: result.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                if (result.errors) {
                    Object.keys(result.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-edit-${key}`);
                        if (errorElement) {
                            errorElement.textContent = result.errors[key][0];
                        }
                    });
                }
                Swal.fire('Error!', 'Please check the form for errors', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });
</script>
@endpush