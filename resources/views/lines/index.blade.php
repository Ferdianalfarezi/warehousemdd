@extends('layouts.app')

@section('title', 'Line & Mesin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Line & Mesin</h1>
            <p class="text-gray-600 mt-1">Manage Master Data Line & Mesin</p>
        </div>
        <button onclick="openCreateLineModal()"
            class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Add Line</span>
        </button>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Line</th>
                        <th class="px-4 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mesin</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($lines as $i => $line)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 text-sm text-gray-900">{{ $i + 1 }}</td>
                            <td class="px-4 py-4 text-sm font-semibold text-gray-900">{{ $line->nama_line }}</td>
                            <td class="px-4 py-4 text-sm text-gray-600">{{ $line->mesin ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button"
                                        onclick="openEditLineModal({{ $line->id }}, '{{ addslashes($line->nama_line) }}', '{{ addslashes($line->mesin) }}')"
                                        class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">Edit</button>
                                    <button type="button" onclick="deleteLine({{ $line->id }})"
                                        class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <p class="mt-4 text-gray-600 font-semibold">Belum ada data Line</p>
                                <p class="text-gray-500 text-sm">Klik "Add Line" untuk memulai</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- CREATE / EDIT MODAL (dipakai bareng, di-switch mode via JS) --}}
<div id="lineModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900" id="lineModalTitle">Add New Line</h2>
            <button type="button" onclick="closeLineModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="lineForm" class="p-6 space-y-3">
            <input type="hidden" id="lineId">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Line</label>
                <input type="text" id="lineNamaLine" placeholder="e.g. Line 1" required
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                <span class="text-red-500 text-xs" id="error-nama_line"></span>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Mesin</label>
                <input type="text" id="lineMesin" placeholder="e.g. PT91, PT92, PT93"
                    class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:border-black focus:ring-1 focus:ring-black transition">
                <span class="text-red-500 text-xs" id="error-mesin"></span>
                <p class="text-xs text-gray-400 mt-1">Bisa isi lebih dari 1 mesin, pisahkan dengan koma.</p>
            </div>
            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="closeLineModal()"
                    class="px-4 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-lg bg-black text-white font-semibold hover:bg-gray-800 transition">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
let lineMode = 'create'; // 'create' | 'edit'

function openCreateLineModal() {
    lineMode = 'create';
    document.getElementById('lineModalTitle').textContent = 'Add New Line';
    document.getElementById('lineForm').reset();
    document.getElementById('lineId').value = '';
    clearLineErrors();
    showLineModal();
}

function openEditLineModal(id, namaLine, mesin) {
    lineMode = 'edit';
    document.getElementById('lineModalTitle').textContent = 'Edit Line';
    document.getElementById('lineId').value        = id;
    document.getElementById('lineNamaLine').value  = namaLine;
    document.getElementById('lineMesin').value     = mesin;
    clearLineErrors();
    showLineModal();
}

function showLineModal() {
    const el = document.getElementById('lineModal');
    el.style.display = 'flex';
}
function closeLineModal() {
    document.getElementById('lineModal').style.display = 'none';
}

document.getElementById('lineForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    clearLineErrors();

    const id       = document.getElementById('lineId').value;
    const payload  = {
        nama_line: document.getElementById('lineNamaLine').value,
        mesin:     document.getElementById('lineMesin').value,
    };
    const url    = lineMode === 'edit' ? `/lines/${id}` : '/lines';
    const method = lineMode === 'edit' ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, showConfirmButton: false, timer: 1200 });
            location.reload();
        } else if (data.errors) {
            Object.keys(data.errors).forEach(key => {
                const el = document.getElementById(`error-${key}`);
                if (el) el.textContent = data.errors[key][0];
            });
        } else {
            Swal.fire('Error!', data.message || 'Unknown error', 'error');
        }
    } catch (err) {
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    }
});

async function deleteLine(id) {
    const result = await Swal.fire({
        title: 'Yakin hapus?', text: 'Data Line akan dihapus permanent!', icon: 'warning',
        showCancelButton: true, confirmButtonColor: '#000', cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
    });
    if (!result.isConfirmed) return;

    try {
        const res  = await fetch(`/lines/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            await Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, showConfirmButton: false, timer: 1200 });
            location.reload();
        } else {
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (e) {
        Swal.fire('Error!', 'Gagal menghapus!', 'error');
    }
}

function clearLineErrors() {
    document.getElementById('error-nama_line').textContent = '';
    document.getElementById('error-mesin').textContent     = '';
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeLineModal();
});
</script>
@endpush