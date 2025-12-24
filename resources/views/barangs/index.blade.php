@extends('layouts.app')

@section('title', 'Barangs')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Items</h1>
            <p class="text-gray-600 mt-1">Manage warehouse goods and products</p>
        </div>
        <button 
            onclick="openCreateModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add Barang</span>
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">

            <!-- Search Box -->
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="searchInput"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Search by kode, nama, supplier..."
                    onkeyup="searchTable()"
                >
            </div>

            <!-- Per Page Selector -->
            <div class="flex-shrink-0">
                <select 
                    id="perPageSelect" 
                    onchange="changePerPage()"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    style="line-height:1.5;"
                >
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>

        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="barangsTableBody">
                    @forelse($barangs as $index => $barang)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                @if($barang->gambar)
                                    <img src="{{ asset('storage/barangs/'.$barang->gambar) }}" 
                                        onclick="showImagePreview('{{ asset('storage/barangs/'.$barang->gambar) }}', '{{ $barang->nama }}')"
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $barang->kode_barang }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $barang->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $barang->line ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"> {{ $barang->supplier?->nama ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $barang->address ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openDetailModal({{ $barang->id }})"
                                            class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-orange-600 transition">
                                        Detail
                                    </button>
                                    <button onclick="openEditModal({{ $barang->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    <button onclick="deleteBarang({{ $barang->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No barangs found</p>
                                <p class="text-gray-500 text-sm">Click "Add Barang" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Showing Entries -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span id="showingFrom" class="font-medium">1</span> to 
                    <span id="showingTo" class="font-medium">0</span> of 
                    <span id="totalEntries" class="font-medium">0</span> entries
                    <span id="filteredInfo" class="hidden">
                        (filtered from <span id="totalEntriesOriginal" class="font-medium">0</span> total entries)
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Create Modal -->
@include('barangs.create')

<!-- Include Edit Modal -->
@include('barangs.edit')

<!-- Include Detail Modal -->
@include('barangs.detail')

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-75" onclick="closeImagePreview()">
    <div class="relative max-w-4xl max-h-[90vh] w-full" onclick="event.stopPropagation()">
        <button onclick="closeImagePreview()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <div class="text-white text-center mb-4">
            <h3 id="previewImageTitle" class="text-xl font-bold"></h3>
        </div>
        
        <div class="bg-white rounded-lg p-4 flex items-center justify-center">
            <img id="previewImageSrc" src="" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
        </div>
        
        <div class="text-center mt-4">
            <a id="downloadImageLink" href="" download class="inline-flex items-center space-x-2 bg-white text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Download Image</span>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let allBarangs = [];
let filteredBarangs = [];
let currentPerPage = 20;
let partCounter = 0;
let partsData = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded');
    
    // Store parts data from Blade
    partsData = [
        @foreach($parts as $part)
        {
            id: {{ $part->id }},
            nama: "{{ $part->nama }}",
            kode: "{{ $part->kode_part }}"
        },
        @endforeach
    ];
    
    console.log('Parts Data:', partsData);
    
    // Initialize table data
    const rows = document.querySelectorAll('#barangsTableBody tr');
    rows.forEach((row, index) => {
        if (!row.querySelector('td[colspan]')) {
            allBarangs.push({
                element: row.cloneNode(true),
                searchText: row.textContent.toLowerCase()
            });
        }
    });
    
    filteredBarangs = [...allBarangs];
    updateTable();
});

// ==================== CREATE FORM ====================
function initializeCreateForm() {
    const form = document.getElementById('createForm');
    if (!form) {
        console.error('Create form not found!');
        return;
    }
    
    form.removeEventListener('submit', handleCreateSubmit);
    form.addEventListener('submit', handleCreateSubmit);
}

async function handleCreateSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('=== CREATE FORM SUBMITTED ===');
    clearErrors();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/barangs', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
        } else {
            if (data.errors) {
                displayErrors(data.errors, 'create');
            } else {
                Swal.fire('Error!', data.message || 'Unknown error', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    }
}

// ==================== EDIT FORM ====================
function initializeEditForm() {
    const form = document.getElementById('editForm');
    if (!form) {
        console.error('Edit form not found!');
        return;
    }
    
    form.removeEventListener('submit', handleEditSubmit);
    form.addEventListener('submit', handleEditSubmit);
}

async function handleEditSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('=== EDIT FORM SUBMITTED ===');
    clearErrors();
    
    const formData = new FormData(e.target);
    const id = document.getElementById('editBarangId').value;
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`/barangs/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
        } else {
            if (data.errors) {
                displayErrors(data.errors, 'edit');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan!', 'error');
    }
}

// ==================== PARTS MANAGEMENT ====================
function addPartRow(partId = '', quantity = 1) {
    console.log('Adding part row, partId:', partId, 'quantity:', quantity);
    
    const container = document.getElementById('partsContainer');
    if (!container) {
        console.error('Parts container not found!');
        return;
    }
    
    const index = partCounter;
    
    const row = document.createElement('div');
    row.className = 'part-row flex items-center space-x-3 p-3 border border-gray-200 rounded-lg';
    
    // Build options HTML
    let optionsHtml = '<option value="">Select Part</option>';
    partsData.forEach(part => {
        const selected = partId == part.id ? 'selected' : '';
        optionsHtml += `<option value="${part.id}" ${selected}>${part.nama} (${part.kode})</option>`;
    });
    
    row.innerHTML = `
        <div class="flex-1">
            <select name="parts[${index}][part_id]" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black part-select">
                ${optionsHtml}
            </select>
        </div>
        <div class="w-32">
            <input type="number" name="parts[${index}][quantity]" value="${quantity}" min="1" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
        </div>
        <button type="button" onclick="removePartRow(this)" 
                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(row);
    
    // Initialize Select2 for the new part select
    $(row.querySelector('.part-select')).select2({
        placeholder: "Select Part",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#createModal'),
        language: {
            noResults: function() {
                return "Part tidak ditemukan";
            },
            searching: function() {
                return "Mencari part...";
            }
        }
    });
    
    partCounter++;
}

function removePartRow(button) {
    const row = button.closest('.part-row');
    const select = row.querySelector('.part-select');
    
    // Destroy Select2 instance before removing
    if (select) {
        $(select).select2('destroy');
    }
    
    const container = document.getElementById('partsContainer');
    const rows = container.querySelectorAll('.part-row');
    
    if (rows.length > 1) {
        row.remove();
    } else {
        Swal.fire('Warning!', 'Minimal 1 part harus ada!', 'warning');
    }
}

function addEditPartRow(partId = '', quantity = 1) {
    const container = document.getElementById('editPartsContainer');
    const index = partCounter;
    
    const row = document.createElement('div');
    row.className = 'part-row flex items-center space-x-3 p-3 border border-gray-200 rounded-lg';
    
    // Build options HTML
    let optionsHtml = '<option value="">Select Part</option>';
    partsData.forEach(part => {
        const selected = partId == part.id ? 'selected' : '';
        optionsHtml += `<option value="${part.id}" ${selected}>${part.nama} (${part.kode})</option>`;
    });
    
    row.innerHTML = `
        <div class="flex-1">
            <select name="parts[${index}][part_id]" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black part-select">
                ${optionsHtml}
            </select>
        </div>
        <div class="w-32">
            <input type="number" name="parts[${index}][quantity]" value="${quantity}" min="1" required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black">
        </div>
        <button type="button" onclick="removeEditPartRow(this)" 
                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    
    container.appendChild(row);
    
    // Initialize Select2 for the new part select
    $(row.querySelector('.part-select')).select2({
        placeholder: "Select Part",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#editModal'),
        language: {
            noResults: function() {
                return "Part tidak ditemukan";
            },
            searching: function() {
                return "Mencari part...";
            }
        }
    });
    
    partCounter++;
}

function removeEditPartRow(button) {
    const row = button.closest('.part-row');
    const select = row.querySelector('.part-select');
    
    // Destroy Select2 instance before removing
    if (select) {
        $(select).select2('destroy');
    }
    
    const container = document.getElementById('editPartsContainer');
    const rows = container.querySelectorAll('.part-row');
    
    if (rows.length > 1) {
        row.remove();
    } else {
        Swal.fire('Warning!', 'Minimal 1 part harus ada!', 'warning');
    }
}

// ==================== MODAL FUNCTIONS ====================
function openCreateModal() {
    console.log('Opening create modal...');
    
    const modal = document.getElementById('createModal');
    const form = document.getElementById('createForm');
    
    if (!modal || !form) {
        console.error('Create modal or form not found!');
        return;
    }
    
    form.reset();
    document.getElementById('createPreviewContainer').classList.add('hidden');
    
    const container = document.getElementById('partsContainer');
    container.innerHTML = '';
    partCounter = 0;
    
    // Initialize Select2 for supplier
    $('#createSupplierId').select2({
        placeholder: "Select Supplier",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#createModal'),
        language: {
            noResults: function() {
                return "Supplier tidak ditemukan";
            },
            searching: function() {
                return "Mencari supplier...";
            }
        }
    });
    
    // Initialize form submission
    initializeCreateForm();
    
    // Add first part row
    addPartRow();
    
    clearErrors();
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => modal.classList.add('modal-fade-in'), 10);
}

function closeCreateModal() {
    // Destroy Select2 instances
    $('#createSupplierId').select2('destroy');
    $('.part-select').select2('destroy');
    
    const modal = document.getElementById('createModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function openEditModal(id) {
    try {
        const response = await fetch(`/barangs/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const barang = data.data;
            
            document.getElementById('editBarangId').value = barang.id;
            document.getElementById('editKodeBarang').value = barang.kode_barang;
            document.getElementById('editNama').value = barang.nama;
            document.getElementById('editAddress').value = barang.address || '';
            document.getElementById('editLine').value = barang.line || '';
            
            // Initialize Select2 for supplier and set value
            $('#editSupplierId').select2({
                placeholder: "Select Supplier",
                allowClear: false,
                width: '100%',
                dropdownParent: $('#editModal'),
                language: {
                    noResults: function() {
                        return "Supplier tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari supplier...";
                    }
                }
            }).val(barang.supplier_id).trigger('change');
            
            const container = document.getElementById('editPartsContainer');
            container.innerHTML = '';
            partCounter = 0;
            
            // Add parts with Select2
            if (barang.parts && barang.parts.length > 0) {
                barang.parts.forEach(part => {
                    addEditPartRow(part.id, part.pivot.quantity);
                });
            } else {
                addEditPartRow();
            }
            
            const previewImg = document.getElementById('editPreview');
            const noImageText = document.getElementById('noImageText');
            
            if (barang.gambar) {
                previewImg.src = `/storage/barangs/${barang.gambar}`;
                previewImg.style.display = 'block';
                noImageText.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                noImageText.style.display = 'block';
            }
            
            // Initialize form submission
            initializeEditForm();
            
            clearErrors();
            
            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => modal.classList.add('modal-fade-in'), 10);
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat data', 'error');
    }
}

function closeEditModal() {
    // Destroy Select2 instances
    $('#editSupplierId').select2('destroy');
    $('.part-select').select2('destroy');
    
    const modal = document.getElementById('editModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function openDetailModal(id) {
    try {
        const response = await fetch(`/barangs/${id}`);
        const res = await response.json();

        if (!res.success) throw new Error('API error');

        const barang = res.data;

        document.getElementById('detailKodeBarang').textContent = barang.kode_barang;
        document.getElementById('detailNama').textContent = barang.nama;
        document.getElementById('detailAddress').textContent = barang.address ?? '-';

        document.getElementById('detailSupplier').textContent =
            barang.supplier?.nama ?? '-';

        const tbody = document.getElementById('detailPartsTableBody');
        tbody.innerHTML = '';

        if (barang.parts?.length) {
            barang.parts.forEach((part, index) => {
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">${index + 1}</td>
                        <td class="px-4 py-3 text-sm font-medium">${part.kode_part}</td>
                        <td class="px-4 py-3 text-sm">${part.nama}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                ${part.pivot.quantity} ${part.satuan}
                            </span>
                        </td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                        No parts found
                    </td>
                </tr>
            `;
        }

        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('detailModalContent');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

    } catch (error) {
        console.error(error);
        Swal.fire('Error!', 'Gagal memuat detail', 'error');
    }
}


function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    const modalContent = document.getElementById('detailModalContent');
    
    // Animate out
    modal.classList.remove('bg-opacity-50');
    modal.classList.add('bg-opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// ==================== DELETE ====================
async function deleteBarang(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "Data akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/barangs/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Gagal menghapus!', 'error');
        }
    }
}

// ==================== UTILITIES ====================
function displayErrors(errors, prefix) {
    Object.keys(errors).forEach(key => {
        const el = document.getElementById(`error-${prefix}-${key}`);
        if (el) el.textContent = errors[key][0];
    });
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

function previewImage(event, previewId) {
    const preview = document.getElementById(previewId);
    const container = document.getElementById(previewId + 'Container');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            container.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function previewEditImage(event) {
    const preview = document.getElementById('editPreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('noImageText').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

function showImagePreview(src, title) {
    const modal = document.getElementById('imagePreviewModal');
    document.getElementById('previewImageSrc').src = src;
    document.getElementById('previewImageTitle').textContent = title;
    document.getElementById('downloadImageLink').href = src;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => modal.classList.add('modal-fade-in'), 10);
}

function closeImagePreview() {
    const modal = document.getElementById('imagePreviewModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

function searchTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    
    if (search === '') {
        filteredBarangs = [...allBarangs];
        document.getElementById('filteredInfo').classList.add('hidden');
    } else {
        filteredBarangs = allBarangs.filter(b => b.searchText.includes(search));
        document.getElementById('filteredInfo').classList.remove('hidden');
        document.getElementById('totalEntriesOriginal').textContent = allBarangs.length;
    }
    
    updateTable();
}

function changePerPage() {
    const val = document.getElementById('perPageSelect').value;
    currentPerPage = val === 'all' ? filteredBarangs.length : parseInt(val);
    updateTable();
}

function updateTable() {
    const tbody = document.getElementById('barangsTableBody');
    tbody.innerHTML = '';
    
    const total = filteredBarangs.length;
    const display = currentPerPage > total ? total : currentPerPage;
    
    document.getElementById('showingFrom').textContent = total > 0 ? '1' : '0';
    document.getElementById('showingTo').textContent = display;
    document.getElementById('totalEntries').textContent = total;
    
    if (total === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No results found
                </td>
            </tr>
        `;
        return;
    }
    
    filteredBarangs.slice(0, display).forEach((barang, i) => {
        const row = barang.element.cloneNode(true);
        row.querySelector('td:first-child').textContent = i + 1;
        tbody.appendChild(row);
    });
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeDetailModal();
        closeImagePreview();
    }
});
</script>
@endpush