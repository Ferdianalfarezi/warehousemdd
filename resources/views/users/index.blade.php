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
                    placeholder="Search by username, role..."
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
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
                                        onclick="showImagePreview('{{ asset('storage/users/'.$user->avatar) }}', '{{ $user->username }}')"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 cursor-pointer hover:opacity-80 hover:scale-110 transition">
                                @else
                                    <div class="w-12 h-12 bg-black rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $user->username }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->role->nama === 'superadmin' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $user->role->nama === 'admin' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ !in_array($user->role->nama, ['superadmin', 'admin']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($user->role->nama) }}
                                </span>
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
                            <td colspan="7" class="px-6 py-16 text-center">
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
            document.getElementById('editUsername').value = user.username;
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

function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
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
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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