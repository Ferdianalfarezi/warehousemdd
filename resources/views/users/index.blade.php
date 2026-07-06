@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Users</h1>
            <p class="text-gray-600 mt-1">Manage system users and access control</p>
        </div>
        <button 
            onclick="openCreateModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add User</span>
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
                    placeholder="Search by nama, NIK, role..."
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Avatar</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">NIK</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jabatan / Line</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Last Login</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="usersTableBody">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/users/'.$user->avatar) }}" 
                                        onclick="showImagePreview('{{ asset('storage/users/'.$user->avatar) }}', '{{ $user->nama }}')"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition">
                                @else
                                    <div class="w-12 h-12 bg-black rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $user->nama }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->nik }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->role->nama === 'superadmin' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $user->role->nama === 'admin' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ !in_array($user->role->nama, ['superadmin', 'admin']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($user->role->nama) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($user->jabatan)
                                    <p class="font-medium text-gray-900">{{ $user->jabatan }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->lines->pluck('nama_line')->join(', ') ?: '-' }}</p>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $user->last_login ? $user->last_login->format('d M Y H:i') : 'Never' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openEditModal({{ $user->id }})"
                                            class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    @if(!$user->isSuperAdmin())
                                    <button onclick="deleteUser({{ $user->id }})"
                                            class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No users found</p>
                                <p class="text-gray-500 text-sm">Click "Add User" to create one</p>
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
@include('users.create')

<!-- Include Edit Modal -->
@include('users.edit')

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
const OPERATOR_ROLE_ID = 4;
const JABATAN_MAX = { 'Leader': 3, 'Asst Leader': 1 };

let allUsers = [];
let filteredUsers = [];
let currentPerPage = 20;

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded');
    
    // Initialize table data
    const rows = document.querySelectorAll('#usersTableBody tr');
    rows.forEach((row, index) => {
        if (!row.querySelector('td[colspan]')) {
            allUsers.push({
                element: row.cloneNode(true),
                searchText: row.textContent.toLowerCase()
            });
        }
    });
    
    filteredUsers = [...allUsers];
    updateTable();
    
    // Initialize forms
    initializeCreateForm();
    initializeEditForm();
    bindOperatorFieldEvents();

    // Klik di luar dropdown Line -> tutup
    document.addEventListener('click', function (e) {
        ['create', 'edit'].forEach(prefix => {
            const wrapper = document.getElementById(`${prefix}LinesWrapper`);
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById(`${prefix}LinesDropdown`).classList.add('hidden');
            }
        });
    });
});

// ==================== JABATAN / LINE TOGGLE (operator only) ====================
function bindOperatorFieldEvents() {
    document.getElementById('createRoleId').addEventListener('change', () => toggleOperatorFields('create'));
    document.getElementById('editRoleId').addEventListener('change', () => toggleOperatorFields('edit'));
    document.getElementById('createJabatan').addEventListener('change', () => handleJabatanChange('create'));
    document.getElementById('editJabatan').addEventListener('change', () => handleJabatanChange('edit'));
}

function toggleOperatorFields(prefix) {
    const roleId     = document.getElementById(`${prefix}RoleId`).value;
    const isOperator = parseInt(roleId) === OPERATOR_ROLE_ID;
    const wrapper    = document.getElementById(`${prefix}OperatorFields`);
    const jabatanEl  = document.getElementById(`${prefix}Jabatan`);

    wrapper.classList.toggle('hidden', !isOperator);
    jabatanEl.required = isOperator;

    if (!isOperator) {
        jabatanEl.value = '';
        uncheckAllLines(prefix);
        document.getElementById(`${prefix}LineHint`).textContent = '';
    }
}

function handleJabatanChange(prefix) {
    const jabatan = document.getElementById(`${prefix}Jabatan`).value;
    const max     = JABATAN_MAX[jabatan] || null;
    const hintEl  = document.getElementById(`${prefix}LineHint`);

    hintEl.textContent = max ? `(maks. ${max} line)` : '';

    // Kalau jabatan berubah ke batas yang lebih kecil, uncheck kelebihannya
    if (max) {
        const checked = Array.from(document.querySelectorAll(`.${prefix}-line-checkbox:checked`));
        checked.slice(max).forEach(cb => cb.checked = false);
    }

    enforceLineMax(prefix);
    updateLineLabel(prefix);
}

// ==================== LINE DROPDOWN (custom, tanpa select2) ====================
function toggleLineDropdown(prefix) {
    const dropdown = document.getElementById(`${prefix}LinesDropdown`);
    const willOpen = dropdown.classList.contains('hidden');
    dropdown.classList.toggle('hidden');

    if (willOpen) {
        const searchInput = dropdown.querySelector('input[type="text"]');
        if (searchInput) {
            searchInput.value = '';
            filterLineOptions(prefix, '');
            setTimeout(() => searchInput.focus(), 50);
        }
    }
}

function filterLineOptions(prefix, keyword) {
    const kw = keyword.trim().toLowerCase();
    const options = document.querySelectorAll(`.${prefix}-line-option`);
    let anyVisible = false;

    options.forEach(opt => {
        const match = opt.dataset.search.includes(kw);
        opt.classList.toggle('hidden', !match);
        if (match) anyVisible = true;
    });

    const noMatchEl = document.getElementById(`${prefix}LinesNoMatch`);
    if (noMatchEl) noMatchEl.classList.toggle('hidden', anyVisible || options.length === 0);
}

function handleLineCheckboxChange(prefix, checkbox) {
    const jabatan = document.getElementById(`${prefix}Jabatan`).value;
    const max     = JABATAN_MAX[jabatan] || null;
    const checkedCount = document.querySelectorAll(`.${prefix}-line-checkbox:checked`).length;

    if (max && checkedCount > max) {
        checkbox.checked = false;
        Swal.fire({ icon: 'warning', title: 'Batas Tercapai', text: `Jabatan ini maksimal ${max} line.`, timer: 1800, showConfirmButton: false });
        return;
    }

    updateLineLabel(prefix);
    enforceLineMax(prefix);
}

function enforceLineMax(prefix) {
    const jabatan = document.getElementById(`${prefix}Jabatan`).value;
    const max     = JABATAN_MAX[jabatan] || null;
    const checkboxes  = document.querySelectorAll(`.${prefix}-line-checkbox`);
    const checkedCount = document.querySelectorAll(`.${prefix}-line-checkbox:checked`).length;

    checkboxes.forEach(cb => {
        cb.disabled = !!(max && !cb.checked && checkedCount >= max);
    });
}

function updateLineLabel(prefix) {
    const checked = Array.from(document.querySelectorAll(`.${prefix}-line-checkbox:checked`));
    const label   = document.getElementById(`${prefix}LinesLabel`);

    if (checked.length === 0) {
        label.textContent = 'Select Line';
        label.classList.add('text-gray-400');
    } else {
        label.textContent = checked.map(cb => cb.closest('label').querySelector('span').textContent.trim()).join(', ');
        label.classList.remove('text-gray-400');
    }
}

function uncheckAllLines(prefix) {
    document.querySelectorAll(`.${prefix}-line-checkbox`).forEach(cb => {
        cb.checked  = false;
        cb.disabled = false;
    });
    updateLineLabel(prefix);
}

function setCheckedLines(prefix, ids) {
    const idSet = new Set(ids.map(String));
    document.querySelectorAll(`.${prefix}-line-checkbox`).forEach(cb => {
        cb.checked = idSet.has(cb.value);
    });
    updateLineLabel(prefix);
    enforceLineMax(prefix);
}

// ==================== CREATE FORM ====================
function initializeCreateForm() {
    const form = document.getElementById('createForm');
    if (!form) {
        console.error('Create form not found!');
        return;
    }
    
    form.removeEventListener('submit', handleCreateSubmit);
    form.addEventListener('submit', handleCreateSubmit);
    console.log('Create form event listener attached');
}

async function handleCreateSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('=== CREATE FORM SUBMITTED ===');
    clearErrors();
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/users', {
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
                const firstError = Object.values(data.errors)[0][0];
                Swal.fire('Error!', firstError, 'error');
            } else {
                Swal.fire('Error!', data.message || 'Unknown error', 'error');
            }
        }
    } catch (error) {
        console.error('Fetch error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan: ' + error.message, 'error');
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
    console.log('Edit form initialized');
}

async function handleEditSubmit(e) {
    e.preventDefault();
    e.stopPropagation();
    
    console.log('=== EDIT FORM SUBMITTED ===');
    clearErrors();
    
    const formData = new FormData(e.target);
    const id = document.getElementById('editUserId').value;
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`/users/${id}`, {
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

// ==================== MODAL FUNCTIONS ====================
function openCreateModal() {
    const modal = document.getElementById('createModal');
    const form = document.getElementById('createForm');
    
    if (!modal || !form) return;
    
    form.reset();
    document.getElementById('createPreviewContainer').classList.add('hidden');
    document.getElementById('createOperatorFields').classList.add('hidden');
    document.getElementById('createJabatan').required = false;
    document.getElementById('createLineHint').textContent = '';
    document.getElementById('createLinesDropdown').classList.add('hidden');
    filterLineOptions('create', '');
    uncheckAllLines('create');
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
    }, 300);
}

async function openEditModal(id) {
    try {
        const response = await fetch(`/users/${id}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.data;
            
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editNama').value = user.nama;
            document.getElementById('editNik').value = user.nik;
            document.getElementById('editRoleId').value = user.role_id;
            document.getElementById('editStatus').value = user.status;
            
            const previewImg = document.getElementById('editPreview');
            const noImageText = document.getElementById('noImageText');
            
            if (user.avatar) {
                previewImg.src = `/storage/users/${user.avatar}`;
                previewImg.style.display = 'block';
                noImageText.style.display = 'none';
            } else {
                previewImg.style.display = 'none';
                noImageText.style.display = 'block';
            }

            // Jabatan & Line
            const isOperator = parseInt(user.role_id) === OPERATOR_ROLE_ID;
            document.getElementById('editOperatorFields').classList.toggle('hidden', !isOperator);
            document.getElementById('editJabatan').value = user.jabatan || '';
            document.getElementById('editJabatan').required = isOperator;
            document.getElementById('editLinesDropdown').classList.add('hidden');
            filterLineOptions('edit', '');

            const max = JABATAN_MAX[user.jabatan] || null;
            document.getElementById('editLineHint').textContent = (isOperator && max) ? `(maks. ${max} line)` : '';

            const selectedLineIds = (user.lines || []).map(l => l.id);
            setCheckedLines('edit', selectedLineIds);
            
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
    const modal = document.getElementById('editModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// ==================== DELETE ====================
async function deleteUser(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "User akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/users/${id}`, {
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
        filteredUsers = [...allUsers];
        document.getElementById('filteredInfo').classList.add('hidden');
    } else {
        filteredUsers = allUsers.filter(u => u.searchText.includes(search));
        document.getElementById('filteredInfo').classList.remove('hidden');
        document.getElementById('totalEntriesOriginal').textContent = allUsers.length;
    }
    
    updateTable();
}

function changePerPage() {
    const val = document.getElementById('perPageSelect').value;
    currentPerPage = val === 'all' ? filteredUsers.length : parseInt(val);
    updateTable();
}

function updateTable() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = '';
    
    const total = filteredUsers.length;
    const display = currentPerPage > total ? total : currentPerPage;
    
    document.getElementById('showingFrom').textContent = total > 0 ? '1' : '0';
    document.getElementById('showingTo').textContent = display;
    document.getElementById('totalEntries').textContent = total;
    
    if (total === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                    No results found
                </td>
            </tr>
        `;
        return;
    }
    
    filteredUsers.slice(0, display).forEach((user, i) => {
        const row = user.element.cloneNode(true);
        row.querySelector('td:first-child').textContent = i + 1;
        tbody.appendChild(row);
    });
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