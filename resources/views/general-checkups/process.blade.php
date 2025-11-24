@extends('layouts.app')

@section('title', 'Process Checkup - ' . $checkup->nama)

@section('content')
<div class="space-y-6">
    
    <!-- Back Button -->
    <div>
        <a href="{{ route('general-checkups.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Checkup
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start space-x-6">
            <!-- Image -->
            <div class="flex-shrink-0">
                @if($checkup->gambar)
                    <img src="{{ asset('storage/barangs/'.$checkup->gambar) }}" 
                        class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200">
                @else
                    <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $checkup->nama }}</h1>
                <p class="text-lg text-gray-600 mt-1">{{ $checkup->kode_barang }}</p>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Jadwal</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            📅 {{ $checkup->tanggal_terjadwal->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Checkup</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            ✅ {{ $checkup->tanggal_checkup ? $checkup->tanggal_checkup->format('d M Y') : '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Line</p>
                        <p class="text-sm font-medium text-gray-900 mt-1">
                            📍 {{ $checkup->line ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Status</p>
                        <p class="mt-1">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $checkup->status_badge_class }}">
                                {{ $checkup->status_display }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Time Info -->
                @if($checkup->mulai_perbaikan)
                <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-blue-700 uppercase">Mulai Perbaikan</p>
                            <p class="text-sm font-medium text-blue-900 mt-1">
                                ⏰ {{ $checkup->mulai_perbaikan->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-blue-700 uppercase">Durasi</p>
                            <p id="durationDisplay" class="text-sm font-bold text-blue-900 mt-1">
                                ⏱️ -
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Checklist Section -->
    <form id="checkupForm">
        @csrf
        <input type="hidden" name="general_checkup_id" value="{{ $checkup->id }}">

        @foreach($indicators as $indicator)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <span class="bg-black text-white w-8 h-8 rounded-full flex items-center justify-center text-sm mr-3">
                    {{ $loop->iteration }}
                </span>
                {{ $indicator->nama_bagian }}
            </h3>

            <div class="space-y-4">
                @foreach($indicator->standards as $standard)
                    @php
                        $existingDetail = $checkup->details->where('check_indicator_standard_id', $standard->id)->first();
                        $currentStatus = $existingDetail ? $existingDetail->status : null;
                        $currentCatatan = $existingDetail ? $existingDetail->catatan : '';
                        $existingActionType = $existingDetail ? $existingDetail->ng_action_type : null;
                        $existingActionStatus = $existingDetail ? $existingDetail->ng_action_status : null;
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-4" data-standard-id="{{ $standard->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                                        {{ $standard->poin }}
                                    </span>
                                    <p class="text-sm text-gray-600">{{ $standard->metode }}</p>
                                </div>
                                <p class="text-sm text-gray-500 italic">Visual dan check: {{ $standard->standar }}</p>
                            </div>

                            <!-- Status Buttons -->
                            <div class="flex items-center space-x-2 ml-4">
                                <button 
                                    type="button"
                                    onclick="setStatus({{ $standard->id }}, 'ok')"
                                    class="status-btn px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'ok' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-green-100' }}"
                                    data-standard-id="{{ $standard->id }}"
                                    data-status="ok"
                                >
                                    ✓ OK
                                </button>
                                <button 
                                    type="button"
                                    onclick="setStatus({{ $standard->id }}, 'ng')"
                                    class="status-btn px-4 py-2 rounded-lg font-medium transition {{ $currentStatus === 'ng' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-red-100' }}"
                                    data-standard-id="{{ $standard->id }}"
                                    data-status="ng"
                                >
                                    ✗ NG
                                </button>
                            </div>
                        </div>

                        <!-- Hidden Input for Status -->
                        <input 
                            type="hidden" 
                            name="details[{{ $standard->id }}][check_indicator_standard_id]" 
                            value="{{ $standard->id }}"
                        >
                        <input 
                            type="hidden" 
                            name="details[{{ $standard->id }}][status]" 
                            value="{{ $currentStatus }}"
                            data-status-input="{{ $standard->id }}"
                        >

                        <!-- Catatan -->
                        <div class="mt-3">
                            <textarea 
                                name="details[{{ $standard->id }}][catatan]"
                                placeholder="Catatan (opsional)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-black focus:border-black resize-none"
                                rows="2"
                            >{{ $currentCatatan }}</textarea>
                        </div>

                        <!-- NG Actions -->
                        <div class="ng-actions mt-3 {{ $currentStatus === 'ng' ? '' : 'hidden' }}" data-ng-section="{{ $standard->id }}">
                            
                            @if(!$existingActionType)
                                <!-- Dropdown untuk pilih action type -->
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tindakan:</label>
                                    <select 
                                        id="actionTypeSelect-{{ $standard->id }}"
                                        onchange="showActionForm({{ $standard->id }})"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
                                    >
                                        <option value="">-- Pilih Tindakan --</option>
                                        <option value="part">Ganti Part</option>
                                        <option value="inhouse">Proses Inhouse</option>
                                        <option value="outhouse">Proses Outhouse</option>
                                    </select>
                                </div>

                                <!-- Form Ganti Part -->
                                <div id="partForm-{{ $standard->id }}" class="hidden">
                                    <button 
                                        type="button"
                                        onclick="openAddPartModal({{ $checkup->id }}, {{ $standard->id }})"
                                        class="inline-flex items-center px-3 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Part
                                    </button>

                                    <!-- Part List -->
                                    <div class="mt-3 space-y-2" id="partList-{{ $standard->id }}">
                                        <!-- Will be populated dynamically -->
                                    </div>
                                </div>

                                <!-- Form Inhouse -->
                                <div id="inhouseForm-{{ $standard->id }}" class="hidden space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Problem:</label>
                                        <textarea 
                                            id="inhouseProblem-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
                                            rows="2"
                                            placeholder="Jelaskan masalah yang terjadi..."
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Proses yang Dilakukan:</label>
                                        <textarea 
                                            id="inhouseProses-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
                                            rows="2"
                                            placeholder="Jelaskan proses perbaikan yang akan dilakukan..."
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesin:</label>
                                        <input 
                                            type="text"
                                            id="inhouseMesin-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
                                            placeholder="Nama mesin yang akan diperbaiki..."
                                        >
                                    </div>
                                    <button 
                                        type="button"
                                        onclick="submitInhouseRequest({{ $checkup->id }}, {{ $standard->id }})"
                                        class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 transition"
                                    >
                                        Submit Permintaan Inhouse
                                    </button>
                                </div>

                                <!-- Form Outhouse -->
                                <div id="outhouseForm-{{ $standard->id }}" class="hidden space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Problem:</label>
                                        <textarea 
                                            id="outhouseProblem-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
                                            rows="2"
                                            placeholder="Jelaskan masalah yang terjadi..."
                                        ></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Mesin:</label>
                                        <input 
                                            type="text"
                                            id="outhouseMesin-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
                                            placeholder="Nama mesin yang akan diperbaiki..."
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                                        <input 
                                            type="text"
                                            id="outhouseSupplier-{{ $standard->id }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black"
                                            placeholder="Nama supplier..."
                                        >
                                    </div>
                                    <button 
                                        type="button"
                                        onclick="submitOuthouseRequest({{ $checkup->id }}, {{ $standard->id }})"
                                        class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg font-medium hover:bg-purple-600 transition"
                                    >
                                        Submit Permintaan Outhouse
                                    </button>
                                </div>
                            @else
                                <!-- Show existing action status -->
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900">
                                                Tindakan: 
                                                @if($existingActionType === 'part')
                                                    <span class="px-2 py-1 bg-orange-500 text-white rounded text-xs">Ganti Part</span>
                                                @elseif($existingActionType === 'inhouse')
                                                    <span class="px-2 py-1 bg-blue-500 text-white rounded text-xs">Inhouse</span>
                                                @elseif($existingActionType === 'outhouse')
                                                    <span class="px-2 py-1 bg-purple-500 text-white rounded text-xs">Outhouse</span>
                                                @endif
                                            </p>
                                            
                                            <!-- Status text untuk inhouse dan outhouse saja -->
                                            @if(in_array($existingActionType, ['inhouse', 'outhouse']))
                                                <p class="text-xs text-blue-700 mt-1">
                                                    Status: 
                                                    <span data-action-status="{{ $existingActionStatus }}">
                                                        @if($existingActionStatus === 'waiting_pdd_confirm')
                                                            Menunggu Konfirmasi PDD
                                                        @elseif($existingActionStatus === 'inhouse_on_process')
                                                            Inhouse Sedang Dikerjakan
                                                        @elseif($existingActionStatus === 'inhouse_completed')
                                                            Inhouse Selesai
                                                        @elseif($existingActionStatus === 'waiting_subcont_confirm')
                                                            Menunggu Konfirmasi Subcont
                                                        @elseif($existingActionStatus === 'outhouse_on_process')
                                                            Outhouse Sedang Dikerjakan
                                                        @elseif($existingActionStatus === 'outhouse_completed')
                                                            Outhouse Selesai
                                                        @elseif($existingActionStatus === 'closed')
                                                            ✅ Selesai
                                                        @endif
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Hidden input untuk track action status -->
                                    <input 
                                        type="hidden" 
                                        data-ng-action-status="{{ $standard->id }}" 
                                        value="{{ $existingActionStatus }}"
                                    >

                                    <!-- Show part list if action type is part -->
                                    @if($existingActionType === 'part')
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between">
                                                <p class="text-xs font-semibold text-blue-900">Part yang digunakan:</p>
                                                <div class="flex items-center space-x-2">
                                                    <!-- Button Close All (muncul jika ada part yang belum installed) -->
                                                    @if($existingDetail->partReplacements->where('is_installed', false)->count() > 0)
                                                        <button 
                                                            type="button"
                                                            onclick="closeAllParts({{ $existingDetail->id }}, {{ $standard->id }})"
                                                            class="inline-flex items-center px-2 py-1 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700 transition"
                                                        >
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                            Close All ({{ $existingDetail->partReplacements->where('is_installed', false)->count() }})
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Button tambah part -->
                                                    <button 
                                                        type="button"
                                                        onclick="openAddPartModal({{ $checkup->id }}, {{ $standard->id }})"
                                                        class="inline-flex items-center px-2 py-1 bg-orange-500 text-white rounded text-xs font-medium hover:bg-orange-600 transition"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        Tambah Part
                                                    </button>
                                                </div>
                                            </div>

                                            @foreach($existingDetail->partReplacements as $partReplacement)
                                                <div class="bg-white p-3 rounded border border-blue-200" data-part-item="{{ $partReplacement->id }}">
                                                    <div class="flex items-start justify-between">
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-900">{{ $partReplacement->part->nama }}</p>
                                                            <p class="text-xs text-gray-600">{{ $partReplacement->part->kode_part }} - Qty: {{ $partReplacement->quantity_used }}</p>
                                                            @if($partReplacement->catatan)
                                                                <p class="text-xs text-gray-500 mt-1 italic">{{ $partReplacement->catatan }}</p>
                                                            @endif
                                                            
                                                            <!-- Status Badge -->
                                                            <div class="mt-2">
                                                                @if($partReplacement->is_installed)
                                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">
                                                                        ✓ Terpasang
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">
                                                                        ⏳ Menunggu Pemasangan
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="flex flex-col space-y-1 ml-3">
                                                            @if(!$partReplacement->is_installed)
                                                                <!-- Button Close untuk part ini -->
                                                                <button 
                                                                    type="button"
                                                                    onclick="closeIndividualPart({{ $partReplacement->id }}, {{ $standard->id }})"
                                                                    class="px-3 py-1 bg-green-500 text-white rounded text-xs font-medium hover:bg-green-600 transition whitespace-nowrap"
                                                                >
                                                                    Close
                                                                </button>
                                                            @endif
                                                            
                                                            @if(!$partReplacement->is_committed)
                                                                <!-- Button Delete hanya jika belum committed -->
                                                                <button 
                                                                    type="button"
                                                                    onclick="removePart({{ $partReplacement->id }}, {{ $standard->id }})"
                                                                    class="text-red-500 hover:text-red-700 text-xs"
                                                                >
                                                                    Hapus
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Show existing action for inhouse/outhouse -->
                                    @if(in_array($existingActionType, ['inhouse', 'outhouse']) && in_array($existingActionStatus, ['inhouse_completed', 'outhouse_completed']))
                                        <button 
                                            type="button"
                                            onclick="closeAction('{{ $existingActionType }}', {{ $existingDetail->id }}, {{ $standard->id }})"
                                            class="mt-3 w-full px-4 py-2 bg-green-500 text-white rounded-lg font-medium hover:bg-green-600 transition"
                                        >
                                            Close
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Catatan Umum -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Umum (Opsional)</label>
            <textarea 
                name="catatan_umum"
                placeholder="Tambahkan catatan umum untuk checkup ini..."
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black resize-none"
                rows="4"
            >{{ $checkup->catatan_umum }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <a 
                    href="{{ route('general-checkups.index') }}"
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition"
                >
                    Batal
                </a>

                <div class="flex space-x-3">
                    <button 
                        type="button"
                        id="saveTempBtn"
                        onclick="saveTemporary()"
                        class="px-6 py-3 bg-orange-500 text-white rounded-lg font-semibold hover:bg-orange-600 transition"
                    >
                        Simpan Sementara
                    </button>
                    <button 
                        type="button"
                        id="finishBtn"
                        onclick="finishCheckup()"
                        class="px-6 py-3 bg-green-500 text-white rounded-lg font-semibold hover:bg-green-600 transition hidden"
                    >
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Add Part Modal -->
@include('general-checkups.add-part-modal')

@endsection

@push('scripts')
<script src="{{ asset('js/general-checkups/process.js') }}"></script>
<script>
    // Set global checkup ID
    window.checkupId = {{ $checkup->id }};
    
    // Initialize duration update if mulai_perbaikan exists
    @if($checkup->mulai_perbaikan)
        initializeDurationUpdate('{{ $checkup->mulai_perbaikan->format('Y-m-d H:i:s') }}');
    @endif
</script>
@endpush
