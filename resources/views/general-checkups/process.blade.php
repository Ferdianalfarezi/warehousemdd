@extends('layouts.app')

@section('title', 'Process Checkup - ' . $checkup->nama)

@section('content')
<div class="space-y-6">
    
    <!-- Back Button -->
    <div>
        <a href="{{ route('general-checkups.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Checkup
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start space-x-6">
            <!-- Image -->
            <div class="flex-shrink-0">
                @if($checkup->gambar)
                    <img src="{{ asset('storage/barangs/'.$checkup->gambar) }}" 
                        class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200">
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $checkup->nama }}</h1>
                <p class="text-lg text-gray-600 mt-1">{{ $checkup->kode_barang }}</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Jadwal</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            📅 {{ $checkup->tanggal_terjadwal->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Checkup</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            ✅ {{ $checkup->tanggal_checkup ? $checkup->tanggal_checkup->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Line</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            📍 {{ $checkup->line ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                        <p class="mt-1">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $checkup->status_badge_class }}">
                                {{ $checkup->status_display }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Time Info -->
                @if($checkup->mulai_perbaikan)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-blue-700 uppercase">Mulai Perbaikan</p>
                            <p class="text-sm font-medium text-blue-900 mt-1">
                                ⏰ {{ $checkup->mulai_perbaikan->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-700 uppercase">Durasi</p>
                            <p id="durationDisplay" class="text-sm font-bold text-blue-900 mt-1">
                                ⏱️ -
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Checklist Section -->
    <form id="checkupForm">
        @csrf
        <input type="hidden" name="general_checkup_id" value="{{ $checkup->id }}">

        @foreach($indicators as $indicator)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-black text-white w-8 h-8 rounded-full flex items-center justify-center text-sm mr-3">
                    {{ $loop->iteration }}
                </span>
                {{ $indicator->nama_bagian }}
            </h3>

            <div class="space-y-4">
                @foreach($indicator->standards as $standard)
                    @php
                        $existingDetail = $checkup->details->where('check_indicator_standard_id', $standard->id)->first();
                        $currentStatus = $existingDetail ? $existingDetail->status : null;
                        $currentCatatan = $existingDetail ? $existingDetail->catatan : '';
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-4" data-standard-id="{{ $standard->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ $standard->poin }}
                                    </span>
                                    <p class="text-sm text-gray-600">{{ $standard->metode }}</p>
                                </div>
                                <p class="text-sm text-gray-500 italic">Visual dan check: {{ $standard->standar }}</p>
                            </div>

                            <!-- Status Buttons -->
                            <div class="flex items-center space-x-2 ml-4">
                                <button 
                                    type="button"
                                    onclick="setStatus({{ $standard->id }}, 'ok')"
                                    class="status-btn px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'ok' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-green-100' }}"
                                    data-standard-id="{{ $standard->id }}"
                                    data-status="ok"
                                >
                                    ✓ OK
                                </button>
                                <button 
                                    type="button"
                                    onclick="setStatus({{ $standard->id }}, 'ng')"
                                    class="status-btn px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'ng' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-red-100' }}"
                                    data-standard-id="{{ $standard->id }}"
                                    data-status="ng"
                                >
                                    ✗ NG
                                </button>
                            </div>
                        </div>

                        <!-- Hidden Input for Status -->
                        <input 
                            type="hidden" 
                            name="details[{{ $standard->id }}][check_indicator_standard_id]" 
                            value="{{ $standard->id }}"
                        >
                        <input 
                            type="hidden" 
                            name="details[{{ $standard->id }}][status]" 
                            value="{{ $currentStatus }}"
                            data-status-input="{{ $standard->id }}"
                        >

                        <!-- Catatan -->
                        <div class="mt-3">
                            <textarea 
                                name="details[{{ $standard->id }}][catatan]"
                                placeholder="Catatan (opsional)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black focus:border-black resize-none"
                                rows="2"
                            >{{ $currentCatatan }}</textarea>
                        </div>

                        <!-- NG Actions (Add Part Button) -->
                        <div class="ng-actions mt-3 {{ $currentStatus === 'ng' ? '' : 'hidden' }}" data-ng-section="{{ $standard->id }}">
                            <button 
                                type="button"
                                onclick="openAddPartModal({{ $checkup->id }}, {{ $standard->id }})"
                                class="inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Part
                            </button>

                            <!-- Part List -->
                            <div class="mt-3 space-y-2" id="partList-{{ $standard->id }}">
                                @if($existingDetail && $existingDetail->partReplacements)
                                    @foreach($existingDetail->partReplacements as $partReplacement)
                                        <div class="flex items-center justify-between bg-gray-50 p-2 rounded border border-gray-200" data-part-id="{{ $partReplacement->id }}">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $partReplacement->part->nama }}</p>
                                                <p class="text-xs text-gray-600">{{ $partReplacement->part->kode_part }} - Qty: {{ $partReplacement->quantity_used }}</p>
                                            </div>
                                            <button 
                                                type="button"
                                                onclick="removePart({{ $partReplacement->id }}, {{ $standard->id }})"
                                                class="text-red-500 hover:text-red-700"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Catatan Umum -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Umum (Opsional)</label>
            <textarea 
                name="catatan_umum"
                placeholder="Tambahkan catatan umum untuk checkup ini..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
                rows="4"
            >{{ $checkup->catatan_umum }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <a 
                    href="{{ route('general-checkups.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Batal
                </a>

                <div class="flex space-x-3">
                    <button 
                        type="button"
                        id="saveTempBtn"
                        onclick="saveTemporary()"
                        class="px-6 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition"
                    >
                        Simpan Sementara
                    </button>
                    <button 
                        type="button"
                        id="finishBtn"
                        onclick="finishCheckup()"
                        class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition hidden"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Part Modal -->
@include('general-checkups.add-part-modal')

@endsection

@push('scripts')
<script>
const checkupId = {{ $checkup->id }};
let selectedStandardId = null;

// Update duration display
@if($checkup->mulai_perbaikan)
function updateDuration() {
    const mulaiPerbaikan = new Date('{{ $checkup->mulai_perbaikan->format('Y-m-d H:i:s') }}');
    const now = new Date();
    const diffMs = now - mulaiPerbaikan;
    const diffMins = Math.floor(diffMs / 60000);
    
    const hours = Math.floor(diffMins / 60);
    const mins = diffMins % 60;
    
    document.getElementById('durationDisplay').textContent = 
        hours > 0 ? `⏱️ ${hours} jam ${mins} menit` : `⏱️ ${mins} menit`;
}

updateDuration();
setInterval(updateDuration, 60000); // Update every minute
@endif

// Set Status
function setStatus(standardId, status) {
    // Update hidden input
    const statusInput = document.querySelector(`input[data-status-input="${standardId}"]`);
    statusInput.value = status;

    // Update button styles
    const buttons = document.querySelectorAll(`button[data-standard-id="${standardId}"]`);
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        if (btnStatus === status) {
            btn.classList.remove('bg-gray-100', 'text-gray-600');
            if (status === 'ok') {
                btn.classList.add('bg-green-500', 'text-white');
            } else {
                btn.classList.add('bg-red-500', 'text-white');
            }
        } else {
            btn.classList.add('bg-gray-100', 'text-gray-600');
            btn.classList.remove('bg-green-500', 'bg-red-500', 'text-white');
        }
    });

    // Show/hide NG actions
    const ngSection = document.querySelector(`div[data-ng-section="${standardId}"]`);
    if (status === 'ng') {
        ngSection.classList.remove('hidden');
    } else {
        ngSection.classList.add('hidden');
    }

    // Update action buttons
    updateActionButtons();
}

// Update action buttons based on status
function updateActionButtons() {
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    let hasNG = false;
    let allFilled = true;

    statusInputs.forEach(input => {
        if (input.value === 'ng') {
            hasNG = true;
        }
        if (!input.value) {
            allFilled = false;
        }
    });

    const saveTempBtn = document.getElementById('saveTempBtn');
    const finishBtn = document.getElementById('finishBtn');

    if (hasNG) {
        saveTempBtn.classList.remove('hidden');
        finishBtn.classList.add('hidden');
    } else if (allFilled) {
        saveTempBtn.classList.add('hidden');
        finishBtn.classList.remove('hidden');
    } else {
        saveTempBtn.classList.remove('hidden');
        finishBtn.classList.add('hidden');
    }
}

// Save Temporary
async function saveTemporary() {
    const formData = new FormData(document.getElementById('checkupForm'));
    const data = {};
    data.details = [];
    data.catatan_umum = formData.get('catatan_umum');

    // Collect all details
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    statusInputs.forEach(input => {
        const standardId = input.getAttribute('data-status-input');
        const status = input.value;
        
        if (status) {
            const catatan = formData.get(`details[${standardId}][catatan]`);
            data.details.push({
                check_indicator_standard_id: standardId,
                status: status,
                catatan: catatan
            });
        }
    });

    if (data.details.length === 0) {
        Swal.fire('Peringatan!', 'Minimal isi 1 checklist!', 'warning');
        return;
    }

    try {
        const response = await fetch(`/general-checkups/${checkupId}/save-checkup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menyimpan checkup!', 'error');
    }
}

// Finish Checkup
async function finishCheckup() {
    const result = await Swal.fire({
        title: 'Selesaikan Checkup?',
        text: "Checkup akan diselesaikan dan dipindahkan ke history",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    const formData = new FormData(document.getElementById('checkupForm'));
    const data = {};
    data.details = [];
    data.catatan_umum = formData.get('catatan_umum');

    // Collect all details
    const statusInputs = document.querySelectorAll('input[data-status-input]');
    statusInputs.forEach(input => {
        const standardId = input.getAttribute('data-status-input');
        const status = input.value;
        
        if (status) {
            const catatan = formData.get(`details[${standardId}][catatan]`);
            data.details.push({
                check_indicator_standard_id: standardId,
                status: status,
                catatan: catatan
            });
        }
    });

    try {
        const response = await fetch(`/general-checkups/${checkupId}/finish-checkup`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Selesai!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });
            window.location.href = result.redirect;
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menyelesaikan checkup!', 'error');
    }
}

// Open Add Part Modal
function openAddPartModal(checkupId, standardId) {
    selectedStandardId = standardId;
    loadAvailableParts();
    
    const modal = document.getElementById('addPartModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAddPartModal() {
    selectedStandardId = null;
    document.getElementById('addPartForm').reset();
    
    const modal = document.getElementById('addPartModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Load Available Parts
async function loadAvailableParts() {
    try {
        const response = await fetch('/checkup-parts/available');
        const result = await response.json();

        if (result.success) {
            const select = document.getElementById('partSelect');
            select.innerHTML = '<option value="">Pilih Part</option>';
            
            result.data.forEach(part => {
                const option = document.createElement('option');
                option.value = part.id;
                option.textContent = `${part.kode_part} - ${part.nama} (Stock: ${part.stock} ${part.satuan})`;
                option.dataset.stock = part.stock;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading parts:', error);
    }
}

// Add Part
async function addPart() {
    const partId = document.getElementById('partSelect').value;
    const quantity = document.getElementById('partQuantity').value;
    const catatan = document.getElementById('partCatatan').value;

    if (!partId || !quantity) {
        Swal.fire('Peringatan!', 'Part dan quantity harus diisi!', 'warning');
        return;
    }

    const selectedOption = document.getElementById('partSelect').selectedOptions[0];
    const availableStock = parseInt(selectedOption.dataset.stock);

    if (parseInt(quantity) > availableStock) {
        Swal.fire('Error!', `Stock tidak mencukupi! Available: ${availableStock}`, 'error');
        return;
    }

    try {
        const response = await fetch('/checkup-parts/add', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                general_checkup_id: checkupId,
                checkup_detail_id: null,
                part_id: partId,
                quantity_used: quantity,
                catatan: catatan
            })
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });

            // Add to part list
            addPartToList(selectedStandardId, result.data);
            closeAddPartModal();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menambahkan part!', 'error');
    }
}

// Add part to visual list
function addPartToList(standardId, partData) {
    const partList = document.getElementById(`partList-${standardId}`);
    const partItem = document.createElement('div');
    partItem.className = 'flex items-center justify-between bg-gray-50 p-2 rounded border border-gray-200';
    partItem.dataset.partId = partData.id;
    partItem.innerHTML = `
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">${partData.part.nama}</p>
            <p class="text-xs text-gray-600">${partData.part.kode_part} - Qty: ${partData.quantity_used}</p>
        </div>
        <button 
            type="button"
            onclick="removePart(${partData.id}, ${standardId})"
            class="text-red-500 hover:text-red-700"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    partList.appendChild(partItem);
}

// Remove Part
async function removePart(partReplacementId, standardId) {
    const result = await Swal.fire({
        title: 'Hapus Part?',
        text: "Stock akan dikembalikan",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/checkup-parts/${partReplacementId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        });

        const result = await response.json();

        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: result.message,
                showConfirmButton: false,
                timer: 1500
            });

            // Remove from visual list
            const partItem = document.querySelector(`div[data-part-id="${partReplacementId}"]`);
            if (partItem) partItem.remove();
        } else {
            Swal.fire('Error!', result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menghapus part!', 'error');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateActionButtons();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddPartModal();
    }
});
</script>
@endpush