{{-- resources/views/suppliers/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
            <p class="text-gray-600 mt-1">Manage your supplier data</p>
        </div>
        <button 
            onclick="openCreateModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add Supplier</span>
        </button>
    </div>

    <!-- Search & Per Page Bar -->
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
                    placeholder="Search by nama, alamat..."
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="suppliersTableBody">
                    @forelse($suppliers as $index => $supplier)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $supplier->nama }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->alamat }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openEditModal({{ $supplier->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    <button onclick="deleteSupplier({{ $supplier->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No suppliers found</p>
                                <p class="text-gray-500 text-sm">Click "Add Supplier" to create one</p>
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
@include('suppliers.create')

<!-- Include Edit Modal -->
@include('suppliers.edit')

{{-- Image Preview Modal (sama persis dengan parts) --}}
<div id="imagePreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" onclick="closeImagePreview()">
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
    // === DATA & STATE ===
    let allSuppliers   = [];
    let filteredSuppliers = [];
    let currentPerPage  = 20;

    // === INIT ===
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('#suppliersTableBody tr');
        rows.forEach(row => {
            if (!row.querySelector('td[colspan]')) {
                allSuppliers.push({
                    element: row.cloneNode(true),
                    searchText: row.textContent.toLowerCase()
                });
            }
        });

        filteredSuppliers = [...allSuppliers];
        updateTable();
    });

    // === SEARCH ===
    function searchTable() {
        const term = document.getElementById('searchInput').value.toLowerCase();

        if (term === '') {
            filteredSuppliers = [...allSuppliers];
            document.getElementById('filteredInfo').classList.add('hidden');
        } else {
            filteredSuppliers = allSuppliers.filter(s => s.searchText.includes(term));
            document.getElementById('filteredInfo').classList.remove('hidden');
            document.getElementById('totalEntriesOriginal').textContent = allSuppliers.length;
        }
        updateTable();
    }

    // === PER PAGE ===
    function changePerPage() {
        const val = document.getElementById('perPageSelect').value;
        currentPerPage = val === 'all' ? filteredSuppliers.length : parseInt(val);
        updateTable();
    }

    // === RENDER TABLE ===
    function updateTable() {
        const tbody      = document.getElementById('suppliersTableBody');
        const total      = filteredSuppliers.length;
        const toShow     = currentPerPage > total ? total : currentPerPage;

        // update footer info
        document.getElementById('showingFrom').textContent = total > 0 ? 1 : 0;
        document.getElementById('showingTo').textContent   = toShow;
        document.getElementById('totalEntries').textContent = total;

        tbody.innerHTML = '';

        if (total === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <p class="text-gray-600 mt-4 font-semibold">No results found</p>
                        <p class="text-gray-500 text-sm mt-1">Try adjusting your search</p>
                    </td>
                </tr>`;
            return;
        }

        filteredSuppliers.slice(0, toShow).forEach((item, idx) => {
            const row = item.element.cloneNode(true);
            row.querySelector('td:first-child').textContent = idx + 1;
            tbody.appendChild(row);
        });
    }

    // === IMAGE PREVIEW (sama persis dengan parts) ===
    function showImagePreview(src, title) {
        document.getElementById('previewImageSrc').src = src;
        document.getElementById('previewImageTitle').textContent = title;
        document.getElementById('downloadImageLink').href = src;

        const modal = document.getElementById('imagePreviewModal');
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

    // === MODAL CREATE ===
    function openCreateModal() {
        const modal = document.getElementById('createModal');
        document.getElementById('createForm').reset();
        clearErrors();

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modal.classList.add('modal-fade-in'), 10);
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        modal.classList.remove('modal-fade-in');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('createForm').reset();
            clearErrors();
        }, 300);
    }

    // === MODAL EDIT ===
    async function openEditModal(id) {
        try {
            const res = await fetch(`/suppliers/${id}`);
            const json = await res.json();

            if (json.success) {
                const s = json.data;
                document.getElementById('editSupplierId').value = s.id;
                document.getElementById('editNama').value      = s.nama;
                document.getElementById('editAlamat').value    = s.alamat || '';

                clearErrors();
                const modal = document.getElementById('editModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.add('modal-fade-in'), 10);
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Error!', 'Failed to load supplier data', 'error');
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

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    }

    // === FORM SUBMIT (Create & Edit) – sama seperti parts ===
    document.getElementById('createForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        const fd = new FormData(this);

        try {
            const res = await fetch('/suppliers', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({icon:'success', title:'Success!', text:data.message, timer:1500, showConfirmButton:false})
                    .then(() => location.reload());
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(k => {
                        const el = document.getElementById(`error-create-${k}`);
                        if (el) el.textContent = data.errors[k][0];
                    });
                }
            }
        } catch (err) { console.error(err); Swal.fire('Error!','Something went wrong','error'); }
    });

    document.getElementById('editForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();
        const fd = new FormData(this);
        fd.append('_method', 'PUT');
        const id = document.getElementById('editSupplierId').value;

        try {
            const res = await fetch(`/suppliers/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: fd
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({icon:'success', title:'Success!', text:data.message, timer:1500, showConfirmButton:false})
                    .then(() => location.reload());
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(k => {
                        const el = document.getElementById(`error-edit-${k}`);
                        if (el) el.textContent = data.errors[k][0];
                    });
                }
            }
        } catch (err) { console.error(err); Swal.fire('Error!','Something went wrong','error'); }
    });

    // === DELETE ===
    async function deleteSupplier(id) {
        const c = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#000',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        });

        if (c.isConfirmed) {
            try {
                const res = await fetch(`/suppliers/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({icon:'success', title:'Deleted!', text:data.message, timer:1500, showConfirmButton:false})
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Failed', 'error');
                }
            } catch (err) {
                Swal.fire('Error!', 'Failed to delete supplier', 'error');
            }
        }
    }

    // ESC to close all modals
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeImagePreview();
        }
    });
</script>
@endpush