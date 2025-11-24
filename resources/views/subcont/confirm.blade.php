@extends('layouts.app')

@section('title', 'Konfirmasi Outhouse - Subcont')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Konfirmasi Permintaan Outhouse</h1>
            <p class="text-gray-600 mt-1">Review dan approve permintaan perbaikan outhouse/subcont</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- On Process -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">On Process</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['on_process'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mesin</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Problem</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 transition">
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
                                {{ $request->created_at->format('d/m/Y H:i') }}
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
                                    @if($request->status === 'pending')
                                        <button 
                                            onclick="approveRequest({{ $request->id }})"
                                            class="bg-blue-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-600 transition"
                                        >
                                            Approve
                                        </button>
                                    @elseif($request->status === 'on_process')
                                        <button 
                                            onclick="completeRequest({{ $request->id }})"
                                            class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition"
                                        >
                                            Complete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-4 text-gray-600 font-semibold">No pending requests</p>
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
                <h2 class="text-2xl font-bold text-gray-900">Detail Permintaan Outhouse</h2>
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
            <div>
                <p class="text-xs text-gray-500">Tanggal Permintaan</p>
                <p id="detailTanggal" class="text-sm font-semibold text-gray-900">-</p>
            </div>
            <div id="confirmedSection" style="display: none;">
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
            </div>
            <div id="completedSection" style="display: none;">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Diselesaikan Oleh</p>
                        <p id="detailCompletedBy" class="text-sm font-semibold text-gray-900">-</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tanggal Selesai</p>
                        <p id="detailCompletedAt" class="text-sm font-semibold text-gray-900">-</p>
                    </div>
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
async function openDetailModal(requestId) {
    try {
        const response = await fetch(`/subcont/confirm/${requestId}`);
        const data = await response.json();

        if (data.success) {
            const request = data.data;
            
            document.getElementById('detailBarang').textContent = request.general_checkup.nama;
            document.getElementById('detailKode').textContent = request.general_checkup.kode_barang;
            document.getElementById('detailMesin').textContent = request.mesin;
            document.getElementById('detailSupplier').textContent = request.supplier;
            document.getElementById('detailProblem').textContent = request.problem;
            document.getElementById('detailTanggal').textContent = new Date(request.created_at).toLocaleString('id-ID');
            document.getElementById('detailStatus').textContent = request.status_display;

            // Show confirmed section if on_process or completed
            if (request.status === 'on_process' || request.status === 'completed') {
                document.getElementById('confirmedSection').style.display = 'block';
                document.getElementById('detailConfirmedBy').textContent = request.confirmed_by ? request.confirmed_by.name : '-';
                document.getElementById('detailConfirmedAt').textContent = request.confirmed_at ? new Date(request.confirmed_at).toLocaleString('id-ID') : '-';
            } else {
                document.getElementById('confirmedSection').style.display = 'none';
            }

            // Show completed section if completed
            if (request.status === 'completed') {
                document.getElementById('completedSection').style.display = 'block';
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

async function approveRequest(requestId) {
    const result = await Swal.fire({
        title: 'Approve Permintaan?',
        text: "Permintaan akan masuk ke status On Process",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/subcont/confirm/${requestId}/approve`, {
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
        Swal.fire('Error!', 'Gagal approve permintaan!', 'error');
    }
}

async function completeRequest(requestId) {
    const result = await Swal.fire({
        title: 'Complete Perbaikan?',
        text: "Perbaikan akan ditandai selesai",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Complete!',
        cancelButtonText: 'Batal'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/subcont/confirm/${requestId}/complete`, {
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
        Swal.fire('Error!', 'Gagal complete perbaikan!', 'error');
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
    }
});
</script>
@endpush