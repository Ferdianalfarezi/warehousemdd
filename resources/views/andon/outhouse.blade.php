@extends('layouts.andon')

@section('page-title', 'OUTHOUSE REPAIR MONITORING')

@section('content')
<style>
.status-badge {
    display: inline-block;
    padding: 0.75rem 1.25rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-on_process {
    background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(147, 51, 234, 0.3);
}

.status-completed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}

.status-badge {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<div style="overflow-x: auto; margin-top:10px;">
    <table class="table-bordered theme-table table-dark-custom" style="width: 100%">
        <thead>
            <tr class="text-center" style="font-size: 1.25rem;">
                <th>No</th>
                <th>Barang</th>
                <th>Kode</th>
                <th>Mesin</th>
                <th>Supplier</th>
                <th>Problem</th>
                <th>Confirmed At</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="table-dark-custom text-center" id="tableBody" style="font-size: 1.25rem;">
            @forelse($requests as $request)
                <tr data-status="{{ $request->status }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $request->generalCheckup->nama }}</td>
                    <td>{{ $request->generalCheckup->kode_barang }}</td>
                    <td>{{ $request->mesin }}</td>
                    <td>{{ $request->supplier }}</td>
                    <td>{{ Str::limit($request->problem, 50) }}</td>
                    <td>{{ $request->confirmed_at ? $request->confirmed_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="status">
                        @php
                            $statusStyles = [
                                'on_process' => 'background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%); color: white; box-shadow: 0 4px 6px -1px rgba(147, 51, 234, 0.3);',
                                'completed' => 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);',
                            ];
                            $style = $statusStyles[$request->status] ?? '';
                        @endphp
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 700; font-size: 0.875rem; text-align: center; min-width: 100px; {{ $style }}">
                            {{ strtoupper(str_replace('_', ' ', $request->status)) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state" style="text-align: center; padding: 4rem 2rem; color: #666;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 120px; height: 120px; margin: 0 auto 1rem; opacity: 0.3;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p style="font-size: 1.5rem; font-weight: bold;">No Data Available</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
// Auto refresh setiap 30 detik untuk monitoring real-time
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endpush