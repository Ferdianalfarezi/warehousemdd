@extends('layouts.app')

@section('title', 'Schedules')

@section('content')
@php
    $isFiltered = $filters['search'] !== '' || $filters['status'] !== 'all' || $filters['periode'] !== 'all';

    $statusOptions = [
        'all' => 'All Status',
        'terjadwal' => 'Terjadwal',
        'segera' => 'Segera',
        'hari_ini' => 'Hari Ini',
        'terlambat' => 'Terlambat',
    ];

    $periodeOptions = [
        'all' => 'All Periode',
        'harian' => 'Harian',
        'mingguan' => 'Mingguan',
        'bulanan' => 'Bulanan',
        'tahunan' => 'Tahunan',
        'custom' => 'Custom',
    ];

    $perPageOptions = ['10' => '10', '20' => '20', '50' => '50', '100' => '100', 'all' => 'All'];
@endphp

<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Service Schedules</h1>
            <p class="text-gray-600 mt-1">Manage maintenance schedules for items</p>
        </div>
        <div class="flex items-center space-x-3">
            <button
                onclick="openImportModal()"
                class="bg-white border-2 border-black text-black px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span>Import Excel</span>
            </button>

            <button
                onclick="openCreateModal()"
                class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition transform hover:scale-105 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Schedule</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Segera -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Segera</p>
                    <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $stats['segera'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Hari Ini -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">Hari Ini</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $stats['hari_ini'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-red-600 uppercase tracking-wider">Terlambat</p>
                    <p class="text-2xl font-bold text-red-900 mt-1">{{ $stats['terlambat'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search (GET form, server-side) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('schedules.index') }}" id="filterForm"
              class="flex flex-col md:flex-row md:items-center md:justify-start md:space-x-4 space-y-3 md:space-y-0">

            <!-- Search Box (tekan Enter untuk cari) -->
            <div class="w-full md:w-1/2 lg:w-1/3 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    name="search"
                    id="searchInput"
                    value="{{ $filters['search'] }}"
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                    placeholder="Search kode, nama barang... (Enter)"
                >
            </div>

            <!-- Status Filter -->
            <div class="flex-shrink-0">
                <select
                    name="status"
                    onchange="this.form.submit()"
                    class="px-7 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Periode Filter -->
            <div class="flex-shrink-0">
                <select
                    name="periode"
                    onchange="this.form.submit()"
                    class="px-8 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    @foreach($periodeOptions as $value => $label)
                        <option value="{{ $value }}" {{ $filters['periode'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page Selector -->
            <div class="flex-shrink-0">
                <select
                    name="per_page"
                    onchange="this.form.submit()"
                    class="px-5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition"
                >
                    @foreach($perPageOptions as $value => $label)
                        <option value="{{ $value }}" {{ (string) $filters['per_page'] === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if($isFiltered)
                <div class="flex-shrink-0">
                    <a href="{{ route('schedules.index', ['per_page' => $filters['per_page']]) }}"
                       class="text-sm text-gray-600 hover:text-black underline">
                        Reset filter
                    </a>
                </div>
            @endif
        </form>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mulai Service</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Service Selanjutnya</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="schedulesTableBody">
                    @forelse($schedules as $schedule)
                        <tr class="hover:bg-gray-50 transition" data-schedule-id="{{ $schedule->id }}">
                            @php
                                // Pakai data master barang terbaru; salinan di schedule cuma cadangan
                                $kodeBarang = $schedule->barang->kode_barang ?? $schedule->kode_barang;
                                $namaBarang = $schedule->barang->nama ?? $schedule->nama;
                                $gambarBarang = $schedule->barang ? $schedule->barang->gambar : $schedule->gambar;
                            @endphp
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $schedules->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-4">
                                @if($gambarBarang)
                                    <img src="{{ asset('storage/barangs/'.$gambarBarang) }}"
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
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $kodeBarang }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $namaBarang }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($schedule->mulai_service)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $schedule->periode_display }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($schedule->service_berikutnya)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClass = [
                                        'terjadwal' => 'bg-green-100 text-green-800',
                                        'segera' => 'bg-yellow-100 text-yellow-800',
                                        'hari_ini' => 'bg-blue-100 text-blue-800',
                                        'terlambat' => 'bg-red-100 text-red-800'
                                    ][$schedule->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $schedule->status_display }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button
                                        data-action="edit"
                                        class="bg-orange-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-yellow-600 transition">
                                        Edit
                                    </button>
                                    <button
                                        data-action="delete"
                                        class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                @if($isFiltered)
                                    <p class="mt-4 text-gray-600 font-semibold">No schedules match your filters</p>
                                    <p class="text-gray-500 text-sm">Coba ubah kata kunci atau reset filter</p>
                                @else
                                    <p class="mt-4 text-gray-600 font-semibold">No schedules found</p>
                                    <p class="text-gray-500 text-sm">Click "Add Schedule" or "Import Excel" to create maintenance schedule</p>
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
                    Showing <span class="font-medium">{{ $schedules->firstItem() ?? 0 }}</span> to
                    <span class="font-medium">{{ $schedules->lastItem() ?? 0 }}</span> of
                    <span class="font-medium">{{ $schedules->total() }}</span> entries
                    @if($isFiltered)
                        (filtered from <span class="font-medium">{{ $stats['total'] }}</span> total entries)
                    @endif
                </div>

                @if($schedules->hasPages())
                    <nav class="flex flex-wrap items-center gap-1">
                        @foreach($schedules->onEachSide(1)->linkCollection() as $link)
                            @if($link['url'] === null)
                                <span class="px-3 py-1.5 text-sm rounded-lg text-gray-400 cursor-not-allowed">
                                    {!! $link['label'] !!}
                                </span>
                            @elseif($link['active'])
                                <span class="px-3 py-1.5 text-sm rounded-lg bg-black text-white font-semibold">
                                    {!! $link['label'] !!}
                                </span>
                            @else
                                <a href="{{ $link['url'] }}"
                                   class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 transition">
                                    {!! $link['label'] !!}
                                </a>
                            @endif
                        @endforeach
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include Create Modal -->
@include('schedules.create')

<!-- Include Edit Modal -->
@include('schedules.edit')

<!-- Include Import Modal -->
@include('schedules.import')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeSelect2();
    updateIntervalLabel();

    // Event delegation untuk button Edit & Delete
    document.getElementById('schedulesTableBody').addEventListener('click', function(e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const row = btn.closest('tr');
        const scheduleId = row.getAttribute('data-schedule-id');

        if (btn.getAttribute('data-action') === 'edit') {
            openEditModal(parseInt(scheduleId));
        } else if (btn.getAttribute('data-action') === 'delete') {
            deleteSchedule(parseInt(scheduleId));
        }
    });
});

function select2Options() {
    return {
        placeholder: "Pilih Barang",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#createModal'),
        // Cari per kata & abaikan jumlah spasi (nama barang sering mengandung spasi ganda)
        matcher: function (params, data) {
            const term = (params.term || '').trim().toLowerCase();
            if (!term) return data;
            const text = (data.text || '').toLowerCase();
            return term.split(/\s+/).every(word => text.includes(word)) ? data : null;
        },
        language: {
            noResults: function() {
                return "Barang tidak ditemukan";
            },
            searching: function() {
                return "Mencari...";
            }
        }
    };
}

function initializeSelect2() {
    if ($('#createBarangId').length) {
        $('#createBarangId').select2(select2Options());
    }
}

// Modal Functions
async function openCreateModal() {
    try {
        // Server sudah hanya mengirim barang yang belum punya schedule
        const response = await fetch('/barangs-for-schedule');

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const barangs = await response.json();

        const select = $('#createBarangId');
        select.empty();

        if (barangs.length === 0) {
            select.append('<option value="">Semua barang sudah memiliki schedule</option>');
            select.prop('disabled', true);
        } else {
            select.append('<option value="">Pilih Barang</option>');
            barangs.forEach(barang => {
                select.append(new Option(`${barang.kode_barang} - ${barang.nama}`, barang.id));
            });
            select.prop('disabled', false);
        }

        // Re-initialize Select2
        if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
        }
        select.select2(select2Options());

        const modal = document.getElementById('createModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => modal.classList.add('modal-fade-in'), 10);

    } catch (error) {
        console.error('Error fetching barangs:', error);
        Swal.fire('Error!', 'Gagal memuat data barang', 'error');
    }
}

function closeCreateModal() {
    $('#createBarangId').val('').trigger('change');

    const modal = document.getElementById('createModal');
    modal.classList.remove('modal-fade-in');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

async function openEditModal(scheduleId) {
    try {
        const response = await fetch(`/schedules/${scheduleId}`);
        const data = await response.json();

        if (data.success) {
            const schedule = data.data;

            document.getElementById('editScheduleId').value = schedule.id;
            document.getElementById('editBarangInfo').textContent = `${schedule.kode_barang} - ${schedule.nama}`;
            document.getElementById('editMulaiService').value = schedule.mulai_service;
            document.getElementById('editPeriode').value = schedule.periode;
            document.getElementById('editIntervalValue').value = schedule.interval_value;

            const nextServiceDate = new Date(schedule.service_berikutnya);
            document.getElementById('editNextServiceInfo').textContent = nextServiceDate.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

            updateEditIntervalLabel();
            clearEditErrors();

            const modal = document.getElementById('editModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => modal.classList.add('modal-fade-in'), 10);
        } else {
            Swal.fire('Error!', data.message || 'Data tidak ditemukan', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Gagal memuat data schedule', 'error');
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

const intervalLabels = {
    'harian': 'hari',
    'mingguan': 'minggu',
    'bulanan': 'bulan',
    'tahunan': 'tahun',
    'custom': 'hari'
};

function updateIntervalLabel() {
    const periode = document.querySelector('#createScheduleForm [name="periode"]');
    const label = document.getElementById('intervalLabel');
    const preview = document.getElementById('previewInterval');

    if (!periode || !label || !preview) return;

    const intervalValue = document.querySelector('#createScheduleForm [name="interval_value"]').value || 1;

    label.textContent = `Interval (${intervalLabels[periode.value]})`;
    preview.textContent = `${intervalValue} ${intervalLabels[periode.value]}`;
}

function updateEditIntervalLabel() {
    const periode = document.getElementById('editPeriode').value;
    const label = document.getElementById('editIntervalLabel');
    const preview = document.getElementById('editPreviewInterval');

    const intervalValue = document.getElementById('editIntervalValue').value || 1;

    label.textContent = `Interval (${intervalLabels[periode]})`;
    preview.textContent = `${intervalValue} ${intervalLabels[periode]}`;
}

// Handle Create Form Submit
document.getElementById('createScheduleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    e.stopPropagation();

    clearErrors();

    const formData = new FormData(this);

    try {
        const response = await fetch('/schedules', {
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
});

// Handle Edit Form Submit
document.getElementById('editScheduleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    e.stopPropagation();

    clearEditErrors();

    const scheduleId = document.getElementById('editScheduleId').value;
    const formData = new FormData(this);

    try {
        const response = await fetch(`/schedules/${scheduleId}`, {
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
                displayEditErrors(data.errors);
            } else {
                Swal.fire('Error!', data.message || 'Unknown error', 'error');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
    }
});

// Delete Schedule
async function deleteSchedule(scheduleId) {
    const result = await Swal.fire({
        title: 'Yakin hapus?',
        text: "Schedule akan dihapus permanent!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#000',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(`/schedules/${scheduleId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
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
                location.reload(); // query string (page & filter) tetap terbawa
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error!', 'Gagal menghapus schedule!', 'error');
        }
    }
}

// Utility Functions
function displayErrors(errors, prefix) {
    Object.keys(errors).forEach(key => {
        const el = document.getElementById(`error-${prefix}-${key}`);
        if (el) el.textContent = errors[key][0];
    });
    document.getElementById(`${prefix}Errors`).classList.remove('hidden');
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    const createErrors = document.getElementById('createErrors');
    if (createErrors) createErrors.classList.add('hidden');
}

function displayEditErrors(errors) {
    Object.keys(errors).forEach(key => {
        const el = document.getElementById(`error-edit-${key}`);
        if (el) el.textContent = errors[key][0];
    });
    document.getElementById('editErrors').classList.remove('hidden');
}

function clearEditErrors() {
    document.querySelectorAll('#editScheduleForm .error-message').forEach(el => el.textContent = '');
    document.getElementById('editErrors').classList.add('hidden');
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeImportModal();
    }
});
</script>
@endpush