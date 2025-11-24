@extends('layouts.app')

@section('title', 'Parts')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Parts</h1>
            <p class="text-gray-600 mt-1">Manage warehouse parts and inventory</p>
        </div>
        <div class="flex space-x-3">
            <!-- Button Import Excel -->
            <button 
                onclick="openImportModal()"
                class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span>Import Excel</span>
            </button>
            
            <!-- Button Add Part -->
            <button 
                onclick="openCreateModal()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Part</span>
            </button>
        </div>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Part</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="partsTableBody">
                    @forelse($parts as $index => $part)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                @if($part->gambar)
                                    <img src="{{ $part->image_path }}" 
                                        onclick="showImagePreview('{{ $part->image_path }}', '{{ $part->nama }}')"
                                        class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2248%22 height=%2248%22%3E%3Crect width=%2248%22 height=%2248%22 fill=%22%23e5e7eb%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22monospace%22 font-size=%2212%22 fill=%22%239ca3af%22%3ENo Image%3C/text%3E%3C/svg%3E';">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $part->kode_part }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $part->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $part->line ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold {{ $part->isBelowMinStock() ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $part->stock }} {{ $part->satuan }}
                                </span>
                                <p class="text-xs text-gray-500">Min: {{ $part->min_stock }} | Max: {{ $part->max_stock }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $part->supplier->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $part->address ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openEditModal({{ $part->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    <button onclick="deletePart({{ $part->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No parts found</p>
                                <p class="text-gray-500 text-sm">Click "Add Part" to create one</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Showing Entries -->
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Showing Info -->
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
@include('parts.create')

<!-- Include Edit Modal -->
@include('parts.edit')

<!-- Include Import Modal -->
@include('parts.import')

<!-- Image Preview Modal -->
<div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" onclick="closeImagePreview()">
    <div class="relative max-w-4xl max-h-[90vh] w-full" onclick="event.stopPropagation()">
        <!-- Close Button -->
        <button onclick="closeImagePreview()" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <!-- Image Title -->
        <div class="text-white text-center mb-4">
            <h3 id="previewImageTitle" class="text-xl font-bold"></h3>
        </div>
        
        <!-- Image Container -->
        <div class="bg-white rounded-lg p-4 flex items-center justify-center">
            <img id="previewImageSrc" src="" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
        </div>
        
        <!-- Download Button -->
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
    // Store all parts data
    let allParts = [];
    let filteredParts = [];
    let currentPerPage = 20;

    // Initialize data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Get all parts data from table
        const rows = document.querySelectorAll('#partsTableBody tr');
        rows.forEach((row, index) => {
            if (!row.querySelector('td[colspan]')) { // Skip empty state row
                allParts.push({
                    element: row.cloneNode(true),
                    searchText: row.textContent.toLowerCase()
                });
            }
        });
        
        filteredParts = [...allParts];
        updateTable();
        
        // Initialize Select2 untuk supplier
        initializeSelect2();
    });

    // Initialize Select2 untuk supplier dropdown
    function initializeSelect2() {
        // Untuk create modal
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
        
        // Untuk edit modal
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
        });
    }

    // Search function
    function searchTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        
        if (searchInput === '') {
            filteredParts = [...allParts];
            document.getElementById('filteredInfo').classList.add('hidden');
        } else {
            filteredParts = allParts.filter(part => part.searchText.includes(searchInput));
            document.getElementById('filteredInfo').classList.remove('hidden');
            document.getElementById('totalEntriesOriginal').textContent = allParts.length;
        }
        
        updateTable();
    }

    // Change per page
    function changePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        currentPerPage = perPage === 'all' ? filteredParts.length : parseInt(perPage);
        updateTable();
    }

    // Update table display
    function updateTable() {
        const tbody = document.getElementById('partsTableBody');
        tbody.innerHTML = '';
        
        const totalEntries = filteredParts.length;
        const displayCount = currentPerPage > totalEntries ? totalEntries : currentPerPage;
        
        // Update info
        document.getElementById('showingFrom').textContent = totalEntries > 0 ? '1' : '0';
        document.getElementById('showingTo').textContent = displayCount;
        document.getElementById('totalEntries').textContent = totalEntries;
        
        if (totalEntries === 0) {
            // Show empty state
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-600 mt-4 font-semibold">No results found</p>
                        <p class="text-gray-500 text-sm mt-1">Try adjusting your search</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        // Display parts
        filteredParts.slice(0, displayCount).forEach((part, index) => {
            const row = part.element.cloneNode(true);
            // Update row number
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                firstCell.textContent = index + 1;
            }
            tbody.appendChild(row);
        });
    }

    // Show Image Preview Modal with fade animation
    function showImagePreview(imageSrc, imageTitle) {
        const modal = document.getElementById('imagePreviewModal');
        document.getElementById('previewImageSrc').src = imageSrc;
        document.getElementById('previewImageTitle').textContent = imageTitle;
        document.getElementById('downloadImageLink').href = imageSrc;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger fade in animation
        setTimeout(() => {
            modal.classList.add('modal-fade-in');
        }, 10);
    }

    // Close Image Preview Modal with fade animation
    function closeImagePreview() {
        const modal = document.getElementById('imagePreviewModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Preview for CREATE modal
    function previewImage(event, previewId) {
        const preview = document.getElementById(previewId);
        const previewContainer = document.getElementById(previewId + 'Container');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    // Preview for EDIT modal (separate function)
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

    function openCreateModal() {
        const modal = document.getElementById('createModal');
        document.getElementById('createForm').reset();
        document.getElementById('createPreviewContainer').classList.add('hidden');
        clearErrors();
        
        // Reset Select2
        $('#createSupplierId').val('').trigger('change');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger fade in animation
        setTimeout(() => {
            modal.classList.add('modal-fade-in');
        }, 10);
    }

    async function openEditModal(id) {
        try {
            const response = await fetch(`/parts/${id}`);
            const data = await response.json();
            
            console.log('API Response:', data); // DEBUG
            
            if (data.success) {
                const part = data.data;
                document.getElementById('editPartId').value = part.id;
                document.getElementById('editKodePart').value = part.kode_part;
                document.getElementById('editNama').value = part.nama;
                document.getElementById('editStock').value = part.stock;
                document.getElementById('editMinStock').value = part.min_stock;
                document.getElementById('editMaxStock').value = part.max_stock;
                document.getElementById('editSatuan').value = part.satuan;
                document.getElementById('editAddress').value = part.address || '';
                document.getElementById('editLine').value = part.line || '';
                
                // Set supplier value untuk Select2
                $('#editSupplierId').val(part.supplier_id).trigger('change');
                
                // Show existing image menggunakan accessor image_path
                const previewImg = document.getElementById('editPreview');
                const noImageText = document.getElementById('noImageText');
                
                console.log('Part gambar:', part.gambar); // DEBUG
                console.log('Part gambar_source:', part.gambar_source); // DEBUG
                console.log('Part image_path:', part.image_path); // DEBUG
                
                if (part.gambar && part.image_path) {
                    previewImg.src = part.image_path;
                    previewImg.style.display = 'block';
                    noImageText.style.display = 'none';
                    
                    // Check if image loads
                    previewImg.onerror = function() {
                        console.error('Image failed to load:', part.image_path);
                        previewImg.style.display = 'none';
                        noImageText.style.display = 'block';
                        noImageText.textContent = 'Image file not found';
                    };
                } else {
                    console.log('No image found'); // DEBUG
                    previewImg.style.display = 'none';
                    noImageText.style.display = 'block';
                    noImageText.textContent = 'No image uploaded';
                }
                
                clearErrors();
                
                const modal = document.getElementById('editModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Trigger fade in animation
                setTimeout(() => {
                    modal.classList.add('modal-fade-in');
                }, 10);
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Failed to load part data', 'error');
        }
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('createForm').reset();
            document.getElementById('createPreviewContainer').classList.add('hidden');
            clearErrors();
            
            // Reset Select2
            $('#createSupplierId').val('').trigger('change');
        }, 300);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.remove('modal-fade-in');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('editForm').reset();
            clearErrors();
            
            // Reset Select2
            $('#editSupplierId').val('').trigger('change');
        }, 300);
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // Submit Create Form
    document.getElementById('createForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);

        try {
            const response = await fetch('/parts', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-create-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    // Submit Edit Form
    document.getElementById('editForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const formData = new FormData(this);
        const id = document.getElementById('editPartId').value;
        formData.append('_method', 'PUT');

        try {
            const response = await fetch(`/parts/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const errorElement = document.getElementById(`error-edit-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[key][0];
                        }
                    });
                }
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Something went wrong!', 'error');
        }
    });

    async function deletePart(id) {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This part will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`/parts/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Failed to delete part!', 'error');
            }
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeImagePreview();
        }
    });
</script>
@endpush