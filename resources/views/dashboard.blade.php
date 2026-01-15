@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->username }}!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-sm text-green-600 mt-2">
                        <span class="font-semibold">{{ $stats['active_users'] }}</span> Active
                    </p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Suppliers -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Suppliers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_suppliers'] }}</p>
                    <p class="text-sm text-gray-500 mt-2">Registered</p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Parts -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Parts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_parts'] }}</p>
                    <p class="text-sm {{ $stats['low_stock_parts'] > 0 ? 'text-red-600' : 'text-gray-500' }} mt-2">
                        <span class="font-semibold">{{ $stats['low_stock_parts'] }}</span> Low Stock
                    </p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Barangs -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Dies</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_barangs'] }}</p>
                    <p class="text-sm text-gray-500 mt-2">Items</p>
                </div>
                <div class="bg-black rounded-lg p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout - Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Low Stock Parts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">LOW STOCK ALERT</h2>
                <p class="text-sm text-gray-600">Part yang berada dibawah qty minimum</p>
            </div>
            <div class="p-6">
                @if($lowStockParts->count() > 0)
                    <div class="space-y-3">
                        @foreach($lowStockParts as $part)
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $part->nama }}</p>
                                <p class="text-sm text-gray-600">{{ $part->kode_part }}</p>
                                <p class="text-xs text-gray-500 mt-1">Supplier: {{ $part->supplier->nama ?? '-' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Stock</p>
                                <p class="text-lg font-bold text-red-600">{{ $part->stock }} {{ $part->satuan }}</p>
                                <p class="text-xs text-gray-500">Min: {{ $part->min_stock }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-600 mt-4">All parts are well stocked!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- General Condition Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">GENERAL CONDITION</h2>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">TOTAL SPARE PART</p>
                        <p class="text-xl font-bold text-gray-900">{{ $partCondition['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Status Bars -->
                <div class="flex gap-2 mb-6">
                    <div class="flex-1 text-center">
                        <div class="bg-green-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            In stock
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $partCondition['in_stock'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-yellow-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            Low stock
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $partCondition['low_stock'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-red-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            Out Stock
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $partCondition['out_of_stock'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-blue-500 text-white text-xs font-semibold py-2 px-2 rounded-md border-2 border-blue-600">
                            Restock
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $partCondition['on_request'] }}</p>
                    </div>
                </div>

                <!-- Chart Container -->
                <div class="flex justify-center items-center">
                    <div class="relative">
                        <canvas id="generalConditionChart" width="350" height="350"></canvas>
                        <!-- Center Text -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <p class="text-xs text-gray-500 font-medium">RESTOCK</p>
                            <p class="text-xs text-gray-500">CONDITION</p>
                            <p class="text-3xl font-bold text-blue-500 italic" id="restockPercentage">0%</p>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex justify-center gap-4 mt-6 flex-wrap">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-blue-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">Restock</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-green-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">In stock</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-yellow-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">Low stock</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-red-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">Out Stock</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restock Status & Top Requested Parts - Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Restock Status Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">RE STOCK STATUS</h2>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">TOTAL RESTOCK</p>
                        <p class="text-xl font-bold text-gray-900">{{ $restockStatus['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <!-- Status Bars - Horizontal -->
                <div class="flex gap-2 mb-4">
                    <div class="flex-1 text-center">
                        <div class="bg-green-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            1 WEEK
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $restockStatus['one_week'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-yellow-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            2 WEEKS
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $restockStatus['two_weeks'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-orange-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            4 WEEKS
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $restockStatus['four_weeks'] }}</p>
                    </div>
                    <div class="flex-1 text-center">
                        <div class="bg-red-500 text-white text-xs font-semibold py-2 px-2 rounded-md">
                            OVER 4 WEEKS
                        </div>
                        <p class="text-gray-700 mt-1 font-semibold text-sm">{{ $restockStatus['over_four_weeks'] }}</p>
                    </div>
                </div>

                <!-- Donut Chart - Centered -->
                <div class="flex justify-center" style="margin-top: 40px;">
                    <canvas id="restockStatusChart" width="280" height="280"></canvas>
                </div>


                <!-- Legend -->
                <div class="flex justify-center gap-4 mt-6 flex-wrap" style="margin-top: 50px;">
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">1 Week</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-yellow-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">2 Weeks</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-orange-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">4 Weeks</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-2.5 h-2.5 bg-red-500 rounded-sm"></span>
                        <span class="text-xs text-gray-600">Over 4 Weeks</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 20 Most Requested Parts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col" style="height: 580px;">
            <div class="px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg font-bold text-gray-900">LIST 20 PEMAKAIAN TERBANYAK</h2>
                        <select id="topPartsMonthFilter" class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="all">All Time</option>
                            @foreach($monthOptions as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">TOTAL ITEMS</p>
                        <p class="text-xl font-bold text-gray-900" id="topPartsCount">{{ $topRequestedParts->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="p-4 overflow-y-auto flex-1 relative" id="topPartsContainer">
                <!-- Loading Indicator -->
                <div id="topPartsLoading" class="hidden absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <div id="topPartsList">
                    @if($topRequestedParts->count() > 0)
                        <div class="space-y-2">
                            @foreach($topRequestedParts as $index => $item)
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <!-- Ranking Number -->
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full 
                                    {{ $index < 3 ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-700' }} 
                                    font-bold text-sm">
                                    {{ $index + 1 }}
                                </div>
                                
                                <!-- Part Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $item->part_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->part_code }}</p>
                                </div>
                                
                                <!-- Stats -->
                                <div class="flex-shrink-0 text-right">
                                    <p class="font-bold text-gray-900 text-sm">{{ number_format($item->total_requested) }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->request_count }}x request</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-600 mt-4">Belum ada data request</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari backend untuk General Condition
    const inStock = {{ $partCondition['in_stock'] }};
    const lowStock = {{ $partCondition['low_stock'] }};
    const outStock = {{ $partCondition['out_of_stock'] }};
    const onRequest = {{ $partCondition['on_request'] }};
    
    // Hitung persentase restock berdasarkan low + out stock
    const lowOutTotal = lowStock + outStock;
    const restockPercentage = lowOutTotal > 0 ? ((onRequest / lowOutTotal) * 100).toFixed(1) : 0;
    
    // Update percentage text
    document.getElementById('restockPercentage').textContent = restockPercentage + '%';

    const innerTotal = inStock + lowStock + outStock;

    // General Condition Chart
    const ctx1 = document.getElementById('generalConditionChart').getContext('2d');
    
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out Stock'],
            datasets: [
                // Outer ring - Restock
                {
                    data: (function() {
                        const inStockPart = inStock;
                        const restockPart = onRequest;
                        const remainingPart = Math.max(0, lowOutTotal - onRequest);
                        return [inStockPart, restockPart, remainingPart];
                    })(),
                    backgroundColor: [
                        'transparent',
                        '#3b82f6',
                        'rgba(200,200,200,0.3)'
                    ],
                    borderWidth: 0,
                    weight: 0.8
                },
                // Inner ring - Main stock condition
                {
                    data: [inStock, lowStock, outStock],
                    backgroundColor: [
                        '#22c55e',
                        '#facc15',
                        '#ef4444'
                    ],
                    borderWidth: 0,
                    weight: 1
                }
            ]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
            cutout: '45%',
            radius: '90%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    filter: function(tooltipItem) {
                        return tooltipItem.dataset.backgroundColor[tooltipItem.dataIndex] !== 'transparent';
                    },
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                if (context.dataIndex === 1) {
                                    return `Restock: ${onRequest} (${restockPercentage}% of needed)`;
                                }
                                return null;
                            } else {
                                const labels = ['In Stock', 'Low Stock', 'Out Stock'];
                                const total = innerTotal;
                                const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                return `${labels[context.dataIndex]}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    });

    // Restock Status Chart
    const ctx2 = document.getElementById('restockStatusChart').getContext('2d');
    
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['1 Week', '2 Weeks', '4 Weeks', 'Over 4 Weeks'],
            datasets: [{
                data: [
                    {{ $restockStatus['one_week'] }},
                    {{ $restockStatus['two_weeks'] }},
                    {{ $restockStatus['four_weeks'] }},
                    {{ $restockStatus['over_four_weeks'] }}
                ],
                backgroundColor: [
                    '#22c55e',
                    '#eab308',
                    '#f97316',
                    '#ef4444'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Month filter for Top Requested Parts
    document.getElementById('topPartsMonthFilter').addEventListener('change', function() {
        const month = this.value;
        const loading = document.getElementById('topPartsLoading');
        const container = document.getElementById('topPartsList');
        
        loading.classList.remove('hidden');
        
        fetch(`{{ route('dashboard.top-parts') }}?month=${month}`)
            .then(response => response.json())
            .then(response => {
                document.getElementById('topPartsCount').textContent = response.count;
                
                if (response.data.length > 0) {
                    let html = '<div class="space-y-2">';
                    response.data.forEach((item, index) => {
                        const rankClass = index < 3 ? 'bg-yellow-500 text-white' : 'bg-gray-300 text-gray-700';
                        html += `
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full ${rankClass} font-bold text-sm">
                                    ${index + 1}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate">${item.part_name}</p>
                                    <p class="text-xs text-gray-500">${item.part_code}</p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="font-bold text-gray-900 text-sm">${Number(item.total_requested).toLocaleString()}</p>
                                    <p class="text-xs text-gray-500">${item.request_count}x request</p>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-gray-600 mt-4">Tidak ada data untuk bulan ini</p>
                        </div>
                    `;
                }
                
                loading.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                loading.classList.add('hidden');
            });
    });
});
</script>
@endpush