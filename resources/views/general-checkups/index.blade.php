@extends('layouts.app')

@section('title', 'General Checkups')

@section('content')
    <div class="space-y-6">
        
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Preventive Checkups</h1>
                <p class="text-gray-600 mt-1">Manage maintenance checkups for scheduled items</p>
            </div>
            <button 
                onclick="autoPopulate()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Auto Populate</span>
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Checkups</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Pending</p>
                        <p class="text-2xl font-bold text-orange-900 mt-1">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- On Process -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">On Process</p>
                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $stats['on_process'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search (server-side, GET) -->
        <form id="filterForm" method="GET" action="{{ route('general-checkups.index') }}"
              class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
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
                        name="search"
                        value="{{ request('search') }}"
                        autocomplete="off"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                        placeholder="Search by kode, nama barang..."
                        oninput="debounceSubmit()"
                    >
                </div>

                <!-- Status Filter -->
                <div class="flex-shrink-0">
                    <select 
                        name="status"
                        onchange="submitFilter()"
                        class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="">All Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                        <option value="on_process" @selected(request('status') === 'on_process')>On Process</option>
                    </select>
                </div>

                <!-- Line Filter -->
                <div class="flex-shrink-0">
                    <select 
                        name="line"
                        onchange="submitFilter()"
                        class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="">All Lines</option>
                        @foreach($lines as $line)
                            <option value="{{ $line }}" @selected(request('line') == $line)>Line {{ $line }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Filter -->
                <div class="flex-shrink-0">
                    <select
                        name="customer"
                        onchange="submitFilter()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        <option value="">Semua Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust }}" @selected(request('customer') === $cust)>{{ $cust }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Per Page Selector -->
                <div class="flex-shrink-0">
                    <select 
                        name="per_page"
                        onchange="submitFilter()"
                        class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    >
                        @foreach([10, 20, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', 20) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                @if(request()->hasAny(['search', 'status', 'line', 'customer']))
                    <div class="flex-shrink-0">
                        <a href="{{ route('general-checkups.index') }}"
                           class="text-sm text-gray-600 hover:text-black underline">
                            Reset
                        </a>
                    </div>
                @endif

            </div>
        </form>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode Barang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Line</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tgl Terjadwal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mulai Perbaikan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($checkups as $checkup)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $checkups->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-4">
                                    @if($checkup->gambar)
                                        <img src="{{ asset('storage/barangs/'.$checkup->gambar) }}" 
                                             loading="lazy"
                                             class="w-12 h-12 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $checkup->kode_barang }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $checkup->nama }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $checkup->line ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ optional($checkup->tanggal_terjadwal)->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $checkup->mulai_perbaikan ? $checkup->mulai_perbaikan->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap {{ $checkup->status_badge_class }}">
                                        {{ $checkup->status_display }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // Style tombol ikon, disamakan dengan Request Repair index (inline, tidak tergantung build Tailwind)
                                        $btnStyle = 'width:32px;height:32px;border-radius:8px;transition:background-color .15s;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:none;cursor:pointer;';
                                    @endphp
                                    <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                        <!-- Detail -->
                                        <button type="button" onclick="openDetailModal({{ $checkup->id }})" title="Detail"
                                                style="{{ $btnStyle }}background-color:#eab308;"
                                                onmouseover="this.style.backgroundColor='#ca8a04'" onmouseout="this.style.backgroundColor='#eab308'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        @if($checkup->status === 'pending')
                                            <!-- Process -->
                                            <button type="button" onclick="startRepair({{ $checkup->id }})" title="Process"
                                                    style="{{ $btnStyle }}background-color:#0048e8;"
                                                    onmouseover="this.style.backgroundColor='#4f46e5'" onmouseout="this.style.backgroundColor='#0048e8'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        @elseif($checkup->status === 'on_process')
                                            <!-- Lanjutkan -->
                                            <a href="{{ route('general-checkups.process', $checkup->id) }}" title="Lanjutkan"
                                               style="{{ $btnStyle }}background-color:#16a34a;"
                                               onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        @endif

                                        <!-- Edit -->
                                        <button type="button" title="Edit"
                                                onclick="openEditModal(this)"
                                                data-id="{{ $checkup->id }}"
                                                data-nama="{{ $checkup->kode_barang }} - {{ $checkup->nama }}"
                                                data-status="{{ $checkup->status }}"
                                                data-tanggal-terjadwal="{{ optional($checkup->tanggal_terjadwal)->format('Y-m-d') }}"
                                                data-mulai-perbaikan="{{ optional($checkup->mulai_perbaikan)->format('Y-m-d\TH:i') }}"
                                                data-started-by="{{ $checkup->started_by }}"
                                                data-started-by-name="{{ $checkup->starter->nama ?? '' }}"
                                                style="{{ $btnStyle }}background-color:#f97316;"
                                                onmouseover="this.style.backgroundColor='#ea580c'" onmouseout="this.style.backgroundColor='#f97316'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>

                                        <!-- Hapus -->
                                        <button type="button" onclick="deleteCheckup({{ $checkup->id }})" title="Hapus"
                                                style="{{ $btnStyle }}background-color:#ef4444;"
                                                onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    @if(request()->hasAny(['search', 'status', 'line', 'customer']))
                                        <p class="mt-4 text-gray-600 font-semibold">No checkups match your filters</p>
                                    @else
                                        <p class="mt-4 text-gray-600 font-semibold">No checkups found</p>
                                        <p class="text-gray-500 text-sm">Click "Auto Populate" to load checkups from schedules</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Footer: Showing Entries + Pagination -->
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-medium">{{ $checkups->firstItem() ?? 0 }}</span> to 
                        <span class="font-medium">{{ $checkups->lastItem() ?? 0 }}</span> of 
                        <span class="font-medium">{{ $checkups->total() }}</span> entries
                    </div>

                    @if($checkups->hasPages())
                        @php
                            $current = $checkups->currentPage();
                            $last    = $checkups->lastPage();
                            $start   = max(1, $current - 2);
                            $end     = min($last, $current + 2);
                            $btn     = 'px-3 py-1.5 rounded-lg text-sm border transition';
                            $normal  = 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100';
                            $active  = 'bg-black border-black text-white';
                            $disabled = 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed';
                        @endphp

                        <div class="flex items-center gap-1 flex-wrap">
                            {{-- Prev --}}
                            @if($checkups->onFirstPage())
                                <span class="{{ $btn }} {{ $disabled }}">&laquo;</span>
                            @else
                                <a href="{{ $checkups->previousPageUrl() }}" class="{{ $btn }} {{ $normal }}">&laquo;</a>
                            @endif

                            {{-- First page --}}
                            @if($start > 1)
                                <a href="{{ $checkups->url(1) }}" class="{{ $btn }} {{ $normal }}">1</a>
                                @if($start > 2)
                                    <span class="px-2 text-gray-400">...</span>
                                @endif
                            @endif

                            {{-- Window --}}
                            @foreach($checkups->getUrlRange($start, $end) as $page => $url)
                                @if($page == $current)
                                    <span class="{{ $btn }} {{ $active }}">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="{{ $btn }} {{ $normal }}">{{ $page }}</a>
                                @endif
                            @endforeach

                            {{-- Last page --}}
                            @if($end < $last)
                                @if($end < $last - 1)
                                    <span class="px-2 text-gray-400">...</span>
                                @endif
                                <a href="{{ $checkups->url($last) }}" class="{{ $btn }} {{ $normal }}">{{ $last }}</a>
                            @endif

                            {{-- Next --}}
                            @if($checkups->hasMorePages())
                                <a href="{{ $checkups->nextPageUrl() }}" class="{{ $btn }} {{ $normal }}">&raquo;</a>
                            @else
                                <span class="{{ $btn }} {{ $disabled }}">&raquo;</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<!-- Include Detail Modal -->
@include('general-checkups.detail-modal')

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Edit Checkup</h3>
                <p id="editNama" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="editForm" onsubmit="event.preventDefault(); submitEdit();" class="p-6 space-y-4">
            <input type="hidden" id="editId">

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tgl Terjadwal <span class="text-red-500">*</span>
                </label>
                <input type="date" id="editTanggalTerjadwal" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
            </div>

            <!-- Hanya untuk checkup yang sudah On Process -->
            <div id="editOnProcessFields" class="space-y-4 hidden">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Mulai Perbaikan <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="editMulaiPerbaikan"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        PIC <span class="text-red-500">*</span>
                    </label>
                    <select id="editStartedBy"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition">
                        <option value="">Pilih PIC</option>
                        @foreach($pics as $pic)
                            <option value="{{ $pic->id }}">{{ $pic->nama }}</option>
                        @endforeach
                    </select>
                    <p id="editPicInfo" class="text-xs text-gray-500 mt-1"></p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                        class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" id="editSubmitBtn"
                        class="px-5 py-2.5 bg-black text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===== Filter (server-side) =====
let searchTimer = null;

function submitFilter() {
    document.getElementById('filterForm').submit();
}

function debounceSubmit() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(submitFilter, 500);
}

// Fokus balik ke search box setelah reload (cursor di akhir teks)
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    if (input && input.value) {
        input.focus();
        const len = input.value.length;
        input.setSelectionRange(len, len);
    }
});

// Auto Populate
async function autoPopulate() {
    const result = await Swal.fire({
        title: 'Auto Populate Checkups?',
        text: "Sistem akan mengambil data dari schedule yang sudah jatuh tempo (hari ini / terlambat). Ini juga berjalan otomatis setiap halaman dibuka.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Populate!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('/general-checkups/auto-populate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
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
            Swal.fire('Error!', 'Gagal melakukan auto populate!', 'error');
        }
    }
}

// Start Repair
async function startRepair(checkupId) {
    const result = await Swal.fire({
        title: 'Mulai Perbaikan?',
        text: "Waktu perbaikan akan dimulai saat ini",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Mulai!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/general-checkups/${checkupId}/start-repair`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Perbaikan Dimulai!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1000
                });
                window.location.href = data.redirect;
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error!', 'Gagal memulai perbaikan!', 'error');
        }
    }
}

// Open Detail Modal
async function openDetailModal(checkupId) {
    try {
        const response = await fetch(`/general-checkups/${checkupId}`);
        const data = await response.json();

        if (data.success) {
            const checkup = data.data;
            
            document.getElementById('detailImage').src = checkup.gambar 
                ? `/storage/barangs/${checkup.gambar}` 
                : '/images/no-image.png';
            document.getElementById('detailKode').textContent = checkup.kode_barang;
            document.getElementById('detailNama').textContent = checkup.nama;
            document.getElementById('detailLine').textContent = checkup.line || '-';
            document.getElementById('detailTerjadwal').textContent = new Date(checkup.tanggal_terjadwal).toLocaleDateString('id-ID');
            document.getElementById('detailCheckup').textContent = checkup.tanggal_checkup 
                ? new Date(checkup.tanggal_checkup).toLocaleDateString('id-ID') 
                : '-';
            document.getElementById('detailMulai').textContent = checkup.mulai_perbaikan 
                ? new Date(checkup.mulai_perbaikan).toLocaleString('id-ID') 
                : '-';
            document.getElementById('detailSelesai').textContent = checkup.waktu_selesai 
                ? new Date(checkup.waktu_selesai).toLocaleString('id-ID') 
                : '-';
            document.getElementById('detailStatus').textContent = checkup.status_display;
            document.getElementById('detailCatatan').textContent = checkup.catatan_umum || '-';

            const modal = document.getElementById('detailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            Swal.fire('Error!', 'Gagal memuat detail checkup', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat detail checkup', 'error');
    }
}

function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Delete Checkup
async function deleteCheckup(checkupId) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "Checkup akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/general-checkups/${checkupId}`, {
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
            Swal.fire('Error!', 'Gagal menghapus checkup!', 'error');
        }
    }
}

// ===== Edit Checkup =====
function openEditModal(btn) {
    const d = btn.dataset;
    const isOnProcess = d.status === 'on_process';

    document.getElementById('editId').value = d.id;
    document.getElementById('editNama').textContent = d.nama;
    document.getElementById('editTanggalTerjadwal').value = d.tanggalTerjadwal;

    // Mulai perbaikan & PIC hanya untuk yang sudah On Process
    document.getElementById('editOnProcessFields').classList.toggle('hidden', !isOnProcess);
    document.getElementById('editMulaiPerbaikan').required = isOnProcess;
    document.getElementById('editStartedBy').required = isOnProcess;
    document.getElementById('editMulaiPerbaikan').value = d.mulaiPerbaikan;
    setCurrentPic(d.startedBy, d.startedByName);

    // #editModal punya style global di layouts/app (opacity 0 sampai ada class modal-fade-in)
    const modal = document.getElementById('editModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => requestAnimationFrame(() => modal.classList.add('modal-fade-in')));
}

// Pilih PIC yang sudah tercatat. Kalau user-nya tidak ada di daftar (mis. sudah nonaktif),
// tambahkan sebagai opsi sementara supaya tetap tampil & terpilih.
function setCurrentPic(startedBy, startedByName) {
    const select = document.getElementById('editStartedBy');
    const info = document.getElementById('editPicInfo');

    select.querySelectorAll('option[data-current-pic]').forEach(opt => opt.remove());

    if (!startedBy) {
        select.value = '';
        info.textContent = 'PIC belum tercatat untuk checkup ini, silakan pilih PIC.';
        return;
    }

    if (!select.querySelector(`option[value="${startedBy}"]`)) {
        const opt = new Option(`${startedByName || 'User #' + startedBy} (nonaktif)`, startedBy);
        opt.dataset.currentPic = '1';
        select.add(opt, 1);
    }

    select.value = startedBy;
    info.textContent = `PIC saat ini: ${startedByName || 'User #' + startedBy}`;
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    if (modal.classList.contains('hidden')) return;

    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function submitEdit() {
    const id = document.getElementById('editId').value;
    const payload = {
        tanggal_terjadwal: document.getElementById('editTanggalTerjadwal').value,
    };

    if (!document.getElementById('editOnProcessFields').classList.contains('hidden')) {
        payload.mulai_perbaikan = document.getElementById('editMulaiPerbaikan').value;
        payload.started_by = document.getElementById('editStartedBy').value;
    }

    const submitBtn = document.getElementById('editSubmitBtn');
    submitBtn.disabled = true;

    try {
        const response = await fetch(`/general-checkups/${id}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (data.success) {
            closeEditModal();
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
        Swal.fire('Error!', 'Gagal mengupdate checkup!', 'error');
    } finally {
        submitBtn.disabled = false;
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDetailModal();
        closeEditModal();
    }
});
</script>
@endpush