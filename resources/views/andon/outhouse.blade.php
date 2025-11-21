{{-- resources/views/andon/outhouse.blade.php --}}
@extends('layouts.app')

@section('title', 'Andon Outhouse')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Andon Outhouse</h1>
            <p class="text-gray-600 mt-1">Monitor dan manage perbaikan outhouse/subcont</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- On Process -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On Process</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['on_process'] }}</p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex space-x-2">
            <button 
                onclick="filterStatus('all')"
                id="filterAll"
                class="px-4 py-2 rounded-lg font-medium transition bg-black text-white"
            >
                All
            </button>
            <button 
                onclick="filterStatus('on_process')"
                id="filterOnProcess"
                class="px-4 py-2 rounded-lg font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200"
            >
                On Process
            </button>
            <button 
                onclick="filterStatus('completed')"
                id="filterCompleted"
                class="px-4 py-2 rounded-lg font-medium transition bg-gray-100 text-gray-700 hover:bg-gray-200"
            >
                Completed
            </button>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mesin</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Problem</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Dikonfirmasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Oleh</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tableBody">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 transition" data-status="{{ $request->status }}">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $request->generalCheckup->nama }}</p>
                                    <p class="text-xs text-gray-600">{{ $request->generalCheckup->kode_barang }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $request->mesin }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $request->supplier }}</td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600 truncate max-w-xs">{{ Str::limit($request->problem, 50) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $request->confirmed_at ? $request->confirmed_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $request->confirmedBy ? $request->confirmedBy->name : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $request->status_badge_class }}">
                                    {{ $request->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button 
                                        onclick="openDetailModal({{ $request->id }})"
                                        class="bg-gray-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-gray-600 transition"
                                    >
                                        Detail
                                    </button>
                                    @if($request->status === 'on_process')
                                        <button 
                                            onclick="completeRequest({{ $request->id }})"
                                            class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition"
                                        >
                                            Selesai
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No outhouse requests</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Detail Perbaikan Outhouse</h2>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-4">
            <div>
                <p class="text-xs text-gray-500">Barang</p>
                <p id="detailBarang" class="text-sm font-semibold text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Kode Barang</p>
                <p id="detailKode" class="text-sm font-semibold text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Mesin</p>
                <p id="detailMesin" class="text-sm font-semibold text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Supplier</p>
                <p id="detailSupplier" class="text-sm font-semibold text-gray-900">-</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Problem</p>
                <p id="detailProblem" class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">-</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Dikonfirmasi Oleh</p>
                    <p id="detailConfirmedBy" class="text-sm font-semibold text-gray-900">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tanggal Konfirmasi</p>
                    <p id="detailConfirmedAt" class="text-sm font-semibold text-gray-900">-</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4" id="completedSection" style="display: none;">
                <div>
                    <p class="text-xs text-gray-500">Diselesaikan Oleh</p>
                    <p id="detailCompletedBy" class="text-sm font-semibold text-gray-900">-</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tanggal Selesai</p>
                    <p id="detailCompletedAt" class="text-sm font-semibold text-gray-900">-</p>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <p id="detailStatus" class="text-sm font-semibold text-gray-900">-</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function filterStatus(status) {
    const rows = document.querySelectorAll('#tableBody tr');
    const buttons = ['filterAll', 'filterOnProcess', 'filterCompleted'];
    
    // Reset all buttons
    buttons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        btn.classList.remove('bg-black', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    // Highlight active button
    const activeBtn = document.getElementById(`filter${status.charAt(0).toUpperCase() + status.slice(1)}`);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
        activeBtn.classList.add('bg-black', 'text-white');
    }
    
    // Filter rows
    rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip empty state row
        
        const rowStatus = row.getAttribute('data-status');
        if (status === 'all' || rowStatus === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

async function openDetailModal(requestId) {
    try {
        const response = await fetch(`/andon/outhouse/${requestId}`);
        const data = await response.json();

        if (data.success) {
            const request = data.data;
            
            document.getElementById('detailBarang').textContent = request.general_checkup.nama;
            document.getElementById('detailKode').textContent = request.general_checkup.kode_barang;
            document.getElementById('detailMesin').textContent = request.mesin;
            document.getElementById('detailSupplier').textContent = request.supplier;
            document.getElementById('detailProblem').textContent = request.problem;
            document.getElementById('detailConfirmedBy').textContent = request.confirmed_by ? request.confirmed_by.name : '-';
            document.getElementById('detailConfirmedAt').textContent = request.confirmed_at ? new Date(request.confirmed_at).toLocaleString('id-ID') : '-';
            document.getElementById('detailStatus').textContent = request.status_display;

            // Show completed section if completed
            if (request.status === 'completed') {
                document.getElementById('completedSection').style.display = 'grid';
                document.getElementById('detailCompletedBy').textContent = request.completed_by ? request.completed_by.name : '-';
                document.getElementById('detailCompletedAt').textContent = request.completed_at ? new Date(request.completed_at).toLocaleString('id-ID') : '-';
            } else {
                document.getElementById('completedSection').style.display = 'none';
            }

            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail', 'error');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

async function completeRequest(requestId) {
    const result = await Swal.fire({
        title: 'Tandai Selesai?',
        text: "Perbaikan akan ditandai selesai",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Selesai!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/andon/outhouse/${requestId}/complete`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
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
            Swal.fire('Error!', data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal menyelesaikan perbaikan!', 'error');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endpush