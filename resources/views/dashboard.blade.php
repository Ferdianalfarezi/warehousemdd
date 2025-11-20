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
                    <p class="text-sm font-medium text-gray-600">Total Barangs</p>
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

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Low Stock Parts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Low Stock Alert</h2>
                <p class="text-sm text-gray-600">Parts below minimum stock level</p>
            </div>
            <div class="p-6">
                @if($lowStockParts->count() > 0)
                    <div class="space-y-3">
                        @foreach($lowStockParts as $part)
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $part->nama }}</p>
                                <p class="text-sm text-gray-600">{{ $part->kode_barang }}</p>
                                <p class="text-xs text-gray-500 mt-1">Supplier: {{ $part->supplier->nama }}</p>
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

        <!-- Recent Barangs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Recent Barangs</h2>
                <p class="text-sm text-gray-600">Latest added items</p>
            </div>
            <div class="p-6">
                @if($recentBarangs->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentBarangs as $barang)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-black transition">
                            <div class="flex items-center space-x-3 flex-1">
                                @if($barang->gambar)
                                    <img src="{{ Storage::url('barangs/'.$barang->gambar) }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $barang->nama }}</p>
                                    <p class="text-sm text-gray-600">{{ $barang->kode_barang }}</p>
                                    <p class="text-xs text-gray-500">
                                        Part: 
                                        {{ $barang->parts->pluck('nama')->join(', ') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-block px-3 py-1 text-xs font-semibold bg-black text-white rounded-full">
                                    {{ $barang->line ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-600 mt-4">No barangs yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection