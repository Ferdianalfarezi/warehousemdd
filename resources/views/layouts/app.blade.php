<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - WarehousMDD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <!-- Select2 CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Favicon default (light mode) -->
    <link rel="icon" type="image/png" href="{{ asset('images/logomdddark.png') }}" media="(prefers-color-scheme: light)">

    <!-- Favicon untuk dark mode -->
    <link rel="icon" type="image/png" href="{{ asset('images/logomddwhite.png') }}" media="(prefers-color-scheme: dark)">
</head>
<style>

    
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar-nav-container {
        height: calc(100vh - 80px - 80px); /* Total height - header - footer */
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.05);
    }

    /* Modal backdrop blur animation */
    #createModal, #editModal {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        background-color: rgba(0, 0, 0, 0);
        transition: backdrop-filter 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }

    #createModal.modal-fade-in, 
    #editModal.modal-fade-in {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background-color: rgba(0, 0, 0, 0.3);
    }

    /* Image preview modal - darker blur */
    #imagePreviewModal {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        background-color: rgba(0, 0, 0, 0);
        transition: backdrop-filter 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }

    #imagePreviewModal.modal-fade-in {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        background-color: rgba(0, 0, 0, 0.75);
    }

    /* Modal content animation */
    #createModal > div, 
    #editModal > div {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #createModal.modal-fade-in > div, 
    #editModal.modal-fade-in > div {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Image preview modal animation */
    #imagePreviewModal > div {
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #imagePreviewModal.modal-fade-in > div {
        opacity: 1;
        transform: scale(1);
    }

    .modal-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Style untuk Select2 di dalam modal */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        padding: 0.5rem 0.75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #000;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Sidebar Modern Styles */
    .sidebar-gradient {
        background: linear-gradient(180deg, #000000 0%, #1a1a1a 100%);
    }

    /* Logo Styles */
    .logo-container {
        position: relative;
        overflow: hidden;
    }

    .logo-image {
        width: 75px;
        height: 50px;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .logo-container:hover .logo-image {
        transform: scale(1.05);
    }

    /* Menu Group Header */
    .menu-group-header {
        position: relative;
        padding: 0.875rem 1rem;
        margin: 0.5rem 0;
        cursor: pointer;
        border-radius: 0.75rem;
        transition: background 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-group-header:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .menu-group-header.active {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Submenu Container - FIXED VERSION */
    .submenu-container {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden; /* Pindahkan overflow ke parent */
    }

    .submenu-container.open {
        grid-template-rows: 1fr;
    }

    .submenu-container > div {
        min-height: 0; /* Important untuk grid layout */
        overflow: visible; /* Ganti dari hidden ke visible */
    }

    /* Menu Item Styles */
    .menu-item {
        position: relative;
        margin: 0.5rem 0;
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: white;
        transform: scaleY(0);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-item.active::before {
        transform: scaleY(1);
    }

    .menu-item.active {
        background: white;
        color: black;
    }

    .menu-item:not(.active):hover {
        background: rgba(255, 255, 255, 0.08);
    }

    /* Submenu Items */
    .submenu-item {
        position: relative;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .submenu-item::before {
        content: '';
        position: absolute;
        left: -1rem;
        top: 50%;
        width: 0.5rem;
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .submenu-item:hover::before {
        width: 0.75rem;
        background: rgba(255, 255, 255, 0.5);
    }

    .submenu-item.active {
        background: rgba(255, 255, 255, 0.15);
        color: white;
        font-weight: 500;
    }

    .submenu-item.active::before {
        width: 0.75rem;
        background: white;
    }

    .submenu-item:not(.active):hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateX(2px);
    }

    /* Icon Animation */
    .menu-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .menu-group-header:hover .menu-icon,
    .menu-item:hover .menu-icon {
        transform: scale(1.1);
    }

    /* Chevron Animation */
    .chevron-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .chevron-icon.open {
        transform: rotate(180deg);
    }

    /* Logout Button */
    .logout-btn {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1) 0%, rgba(153, 27, 27, 0.1) 100%);
        border: 1px solid rgba(220, 38, 38, 0.2);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .logout-btn:hover {
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.2) 0%, rgba(153, 27, 27, 0.2) 100%);
        border-color: rgba(220, 38, 38, 0.4);
        transform: translateY(-1px);
    }

    /* Sidebar transition */
    aside {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Main content transition */
    .flex-1.flex.flex-col {
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* User menu dropdown */
    .relative > div[x-show] {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    /* Button hover effects */
    button {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Link hover effects */
    a {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }

    .animate-slideIn {
        animation: slideIn 0.3s ease-out;
    }

    /* Tambahkan di file CSS atau dalam <style> tag di layout */
    .notification-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
    }

    .notification-toast {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 16px;
        display: flex;
        align-items: start;
        gap: 12px;
        animation: slideInRight 0.3s ease-out;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .notification-toast:hover {
        transform: translateX(-5px);
    }

    .notification-toast.fade-out {
        animation: slideOutRight 0.3s ease-out forwards;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-icon.inhouse {
        background: #3b82f6;
    }

    .notification-icon.outhouse {
        background: #9333ea;
    }

    .notification-content {
        flex: 1;
    }

    .notification-title {
        font-weight: 600;
        color: #111827;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .notification-message {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.4;
    }

    .notification-time {
        color: #9ca3af;
        font-size: 11px;
        margin-top: 4px;
    }

    .notification-badge {
        background: #ef4444;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 12px;
        min-width: 20px;
        text-align: center;
    }
    
</style>
<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden" x-data="menuState()">
        
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient text-white transform transition-transform duration-300 ease-in-out shadow-2xl"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="logo-container flex items-center justify-center h-20 border-b border-gray-800 border-opacity-50 px-4">
                <img src="{{ asset('images/logomdd.png') }}" alt="Logo" class="logo-image">
                <h1 class="text-xl tracking-wider mt-2"><i>Warehouse</i></h1>
            </div>

            <!-- Navigation - UPDATE BAGIAN INI -->
            <div class="sidebar-nav-container px-3 space-y-1">
                <nav class="mt-4 space-y-1 pb-4">
                    
                    <!-- Dashboard - Direct Link (No Dropdown) -->
                    <a href="{{ route('dashboard') }}" 
                    class="menu-item flex items-center px-4 py-2.5 text-gray-300 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-semibold text-sm">Dashboard</span>
                    </a>

                    <!-- Data Master Group -->
                    <div>
                        <button @click="toggleMenu('master')" 
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.master ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                                </svg>
                                <span class="font-semibold text-sm">Data Master</span>
                            </div>
                            <svg class="w-4 h-4 chevron-icon" :class="openMenus.master ? 'open' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div class="submenu-container" :class="openMenus.master ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">
                                <a href="{{ route('parts.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('parts.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Parts
                                </a>
                                
                                <a href="{{ route('barangs.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('barangs.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    Items
                                </a>
                                
                                <a href="{{ route('schedules.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Schedules
                                </a>
                                
                                <a href="{{ route('check-indicators.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('check-indicators.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    Check Indicators
                                </a>
                                
                                <a href="{{ route('suppliers.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Suppliers
                                </a>
                                
                                @can('manage-users')
                                <a href="{{ route('users.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Users
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div>
                        <button @click="toggleMenu('transaction')" 
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.transaction ? 'active' : ''">

                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>

                                <span class="font-semibold text-sm">Data Transaksi</span>

                                <span id="totalTransactionBadge"
                                    class="ml-2 bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                    style="display: none;">
                                    0
                                </span>
                            </div>

                            <svg class="w-4 h-4 chevron-icon"
                                :class="openMenus.transaction ? 'open' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div class="submenu-container" :class="openMenus.transaction ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">

                                {{-- REQUEST PART --}}
                                <a href="{{ route('request-parts.index') }}"
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300
                                {{ request()->routeIs('request-parts.*') ? 'active' : '' }}">

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-width="2"
                                                d="M12 6v12m6-6H6"/>
                                        </svg>
                                        Request Part
                                    </div>
                                    <span id="requestPartBadge"
                                        class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                        style="display: none;">
                                        0
                                    </span>
                                </a>

                                {{-- GENERAL CHECKUPS --}}
                                <a href="{{ route('general-checkups.index') }}"
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300
                                {{ request()->routeIs('general-checkups.*') ? 'active' : '' }}">

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2
                                                    M9 5a2 2 0 002 2h2a2 2 0 002-2
                                                    M9 5a2 2 0 012-2h2a2 2 0 012 2
                                                    m-6 9l2 2 4-4"/>
                                        </svg>
                                        General Checkups
                                    </div>

                                    <span id="checkupBadge"
                                        class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                        style="display: none;">
                                        0
                                    </span>
                                </a>

                            </div>
                        </div>
                    </div>


                    <!-- Approval Group -->
                    <div>
                        <button @click="toggleMenu('approval')" 
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.approval ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-sm">Approval</span>
                                <span id="totalApprovalBadge" class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display: none;">
                                    0
                                </span>
                            </div>
                            <svg class="w-4 h-4 chevron-icon" :class="openMenus.approval ? 'open' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div class="submenu-container" :class="openMenus.approval ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">
                                <a href="{{ route('pdd.confirm.index') }}" 
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('pdd.confirm.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Inhouse
                                    </div>
                                    <span id="inhouseBadge" class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display: none;">
                                        0
                                    </span>
                                </a>

                                <a href="{{ route('subcont.confirm.index') }}" 
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('subcont.confirm.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Outhouse
                                    </div>
                                    <span id="outhouseBadge" class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display: none;">
                                        0
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Andon Group -->
                    <div>
                        <button @click="toggleMenu('andon')" 
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.andon ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span class="font-semibold text-sm">Andon</span>
                            </div>
                            <svg class="w-4 h-4 chevron-icon" :class="openMenus.andon ? 'open' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div class="submenu-container" :class="openMenus.andon ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">

                                <!-- Inhouse -->
                                <a href="{{ route('andon.inhouse.index') }}" 
                                target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('andon.inhouse.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Inhouse
                                </a>

                                <!-- Outhouse -->
                                <a href="{{ route('andon.outhouse.index') }}" 
                                target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('andon.outhouse.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Outhouse
                                </a>

                                <!-- General Checkup -->
                                <a href="{{ route('andon.general-checkup.index') }}" 
                                target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('andon.general-checkup.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    General Checkup
                                </a>

                            </div>
                        </div>
                    </div>


                    <!-- Data History Group -->
                    <div>
                        <button @click="toggleMenu('history')" 
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.history ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-sm">Data History</span>
                            </div>
                            <svg class="w-4 h-4 chevron-icon" :class="openMenus.history ? 'open' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div class="submenu-container" :class="openMenus.history ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">
                                <a href="{{ route('history-request-parts.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('history-request-parts.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Request Part
                                </a>
                            </div>
                        </div>
                        
                        <div class="submenu-container" :class="openMenus.history ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">
                                <a href="{{ route('history-checkups.index') }}" 
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('history-checkups.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    History Checkups
                                </a>
                            </div>
                        </div>
                    </div>

                </nav>
            </div>

            <!-- Logout Button - Fixed at bottom -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-800 border-opacity-50 bg-gradient-to-t from-black to-transparent">
                <button onclick="confirmLogout()" 
                        class="logout-btn flex items-center w-full px-4 py-3 rounded-lg text-gray-300 transition-all duration-300">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-semibold text-sm">Logout</span>
                </button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
            <header class="bg-white shadow-sm z-40 sticky top-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <!-- Menu Toggle -->
                    <button 
                        @click="sidebarOpen = !sidebarOpen" 
                        class="text-gray-600 hover:text-black focus:outline-none transition-all duration-200 hover:scale-110"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <!-- Right Side: Date/Time & User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Date & Time Display -->
                        <div class="flex items-center space-x-2 text-gray-600">
                            <div class="text-sm">
                                <span id="current-date" class="font-medium"></span>
                                <span class="mx-2">|</span>
                                <span id="current-time" class="font-semibold"></span>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button 
                                @click="open = !open"
                                class="flex items-center space-x-3 hover:bg-gray-50 px-4 py-2 rounded-xl transition-all duration-200"
                            >
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/users/'.auth()->user()->avatar) }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-200">
                                @else
                                    <div class="w-9 h-9 bg-gradient-to-br from-gray-800 to-black text-white rounded-full flex items-center justify-center font-bold ring-2 ring-gray-200">
                                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->username }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->role->nama ?? 'User' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200" 
                                    :class="open ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div 
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-100"
                                x-cloak
                            >
                                
                                
                                <button onclick="confirmLogout()" class="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>

            <footer style="background: #ffffff00; border-top: 1px solid #ffffffa2; padding: 1.5rem 2rem; text-align: center; margin-left:0px; transition: all 0.3s ease;">
                <p style="margin: 0; font-size: 0.875rem; color: #888888;">
                    &copy; {{ date('Y') }} <strong style="color: #000000;"><i>STEP IT DEPT</i></strong> - All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    
</div>

    <!-- Notification Container -->
    <div id="notificationContainer" class="notification-container"></div>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
    // 🔥 PRELOAD STATE SEBELUM ALPINE INIT (Critical!)
    (function() {
        const savedState = localStorage.getItem('sidebar_menu_state');
        if (savedState) {
            window.__PRELOADED_MENU_STATE__ = JSON.parse(savedState);
        }
    })();

    // 🔥 GLOBAL MENU STATE HANDLER
    function menuState() {
        // Get current path untuk auto-detect menu yang harus dibuka
        const currentPath = window.location.pathname;
        
        // Default state berdasarkan route
        let defaultState = {
            master: false,
            transaction: false,
            approval: false,
            andon: false,
            history: false
        };
        
        // Auto-detect menu berdasarkan current path
        if (currentPath.includes('/parts') || 
            currentPath.includes('/barangs') || 
            currentPath.includes('/schedules') || 
            currentPath.includes('/check-indicators') || 
            currentPath.includes('/suppliers') || 
            currentPath.includes('/users')) {
            defaultState.master = true;
        }
        
        if (currentPath.includes('/request-parts') || 
            currentPath.includes('/general-checkups')) {
            defaultState.transaction = true;
        }
        
        if (currentPath.includes('/pdd/confirm') || 
            currentPath.includes('/subcont/confirm')) {
            defaultState.approval = true;
        }
        
        if (currentPath.includes('/andon/inhouse') || 
            currentPath.includes('/andon/outhouse') || 
            currentPath.includes('/andon/general-checkup')) {
            defaultState.andon = true;
        }
        
        if (currentPath.includes('/history-checkups') || 
            currentPath.includes('/history-request-parts')) {
            defaultState.history = true;
        }

        return {
            sidebarOpen: true,
            
            // 🔥 MERGE: Preloaded state + current route state
            openMenus: {
                master: window.__PRELOADED_MENU_STATE__?.master ?? defaultState.master,
                transaction: window.__PRELOADED_MENU_STATE__?.transaction ?? defaultState.transaction,
                approval: window.__PRELOADED_MENU_STATE__?.approval ?? defaultState.approval,
                andon: window.__PRELOADED_MENU_STATE__?.andon ?? defaultState.andon,
                history: window.__PRELOADED_MENU_STATE__?.history ?? defaultState.history
            },
            
            init() {
                // 🔥 FORCE OPEN MENU YANG SESUAI DENGAN ROUTE (Override localStorage if needed)
                if (defaultState.master) this.openMenus.master = true;
                if (defaultState.transaction) this.openMenus.transaction = true;
                if (defaultState.approval) this.openMenus.approval = true;
                if (defaultState.andon) this.openMenus.andon = true;
                if (defaultState.history) this.openMenus.history = true;
                
                // 🔥 SAVE STATE SETIAP KALI ADA PERUBAHAN
                this.$watch('openMenus', value => {
                    localStorage.setItem('sidebar_menu_state', JSON.stringify(value));
                }, { deep: true });

                console.log('Menu initialized:', this.openMenus); // Debug
            },
            
            toggleMenu(menu) {
                // Toggle menu yang diklik
                this.openMenus[menu] = !this.openMenus[menu];
            }
        }
    }

    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: "Apakah Anda yakin ingin keluar dari sistem?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E4080A',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-sign-out-alt mr-2"></i> Ya, Logout',
            cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
            reverseButtons: true,
            background: '#fff',
            backdrop: `
                rgba(0,0,0,0.5)
                left top
                no-repeat
            `,
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-lg px-6 py-2.5',
                cancelButton: 'rounded-lg px-6 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // 🔥 CLEAR LOCALSTORAGE PAS LOGOUT
                localStorage.removeItem('sidebar_menu_state');
                
                Swal.fire({
                    title: 'Logging out...',
                    html: '<div class="flex items-center justify-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-2xl'
                    }
                });
                
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 500);
            }
        });
    }

    function updateDateTime() {     
        const now = new Date();
        
        // Format tanggal: Senin, 25 November 2024
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const dateStr = now.toLocaleDateString('id-ID', options);
        
        // Format waktu: 14:30:45
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        
        document.getElementById('current-date').textContent = dateStr;
        document.getElementById('current-time').textContent = timeStr;
    }

    // Update setiap detik
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // ========================================
    // 🔔 APPROVAL NOTIFICATION SYSTEM
    // ========================================
    
    // Store previous counts untuk detect perubahan
    let previousCounts = {
        inhouse: 0,
        outhouse: 0
    };

    // Function untuk show notification toast
    function showNotification(type, count) {
        const container = document.getElementById('notificationContainer');
        
        const notification = document.createElement('div');
        notification.className = 'notification-toast';
        
        const iconBg = type === 'inhouse' ? 'inhouse' : 'outhouse';
        const title = type === 'inhouse' ? 'Permintaan Inhouse Baru' : 'Permintaan Outhouse Baru';
        const message = `Ada ${count} permintaan yang perlu di-approve`;
        
        notification.innerHTML = `
            <div class="notification-icon ${iconBg}">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
                <div class="notification-time">Baru saja</div>
            </div>
            <div class="notification-badge">${count}</div>
        `;
        
        // Click handler untuk redirect
        notification.addEventListener('click', function() {
            if (type === 'inhouse') {
                window.location.href = '{{ route("pdd.confirm.index") }}';
            } else {
                window.location.href = '{{ route("subcont.confirm.index") }}';
            }
        });
        
        container.appendChild(notification);
        
        // Play notification sound (optional)
        playNotificationSound();
        
        // Auto remove setelah 10 detik
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 10000);
    }

    // Optional: Play notification sound
    function playNotificationSound() {
        // Uncomment jika punya file sound
        // const audio = new Audio('/sounds/notification.mp3');
        // audio.volume = 0.5;
        // audio.play().catch(e => console.log('Sound play failed:', e));
    }

    // Function untuk update approval counts dengan notification
    async function updateApprovalCounts() {
        try {
            const response = await fetch('/api/approval-counts');
            const data = await response.json();
            
            const inhouseCount = data.inhouse;
            const outhouseCount = data.outhouse;
            const totalCount = inhouseCount + outhouseCount;
            
            // 🔔 Check for new inhouse requests
            if (inhouseCount > previousCounts.inhouse) {
                const newCount = inhouseCount - previousCounts.inhouse;
                showNotification('inhouse', newCount);
            }
            
            // 🔔 Check for new outhouse requests
            if (outhouseCount > previousCounts.outhouse) {
                const newCount = outhouseCount - previousCounts.outhouse;
                showNotification('outhouse', newCount);
            }
            
            // Update previous counts
            previousCounts.inhouse = inhouseCount;
            previousCounts.outhouse = outhouseCount;
            
            // Update total badge
            const totalBadge = document.getElementById('totalApprovalBadge');
            if (totalCount > 0) {
                totalBadge.textContent = totalCount;
                totalBadge.style.display = 'inline-block';
            } else {
                totalBadge.style.display = 'none';
            }
            
            // Update inhouse badge
            const inhouseBadge = document.getElementById('inhouseBadge');
            if (inhouseCount > 0) {
                inhouseBadge.textContent = inhouseCount;
                inhouseBadge.style.display = 'inline-block';
            } else {
                inhouseBadge.style.display = 'none';
            }
            
            // Update outhouse badge
            const outhouseBadge = document.getElementById('outhouseBadge');
            if (outhouseCount > 0) {
                outhouseBadge.textContent = outhouseCount;
                outhouseBadge.style.display = 'inline-block';
            } else {
                outhouseBadge.style.display = 'none';
            }
            
        } catch (error) {
            console.error('Error fetching approval counts:', error);
        }
    }

    // Initialize: Load counts pertama kali tanpa notif
    async function initializeApprovalCounts() {
        try {
            const response = await fetch('/api/approval-counts');
            const data = await response.json();
            
            // Set initial counts tanpa trigger notif
            previousCounts.inhouse = data.inhouse;
            previousCounts.outhouse = data.outhouse;
            
            const totalCount = data.inhouse + data.outhouse;
            
            // Update badges
            const totalBadge = document.getElementById('totalApprovalBadge');
            if (totalCount > 0) {
                totalBadge.textContent = totalCount;
                totalBadge.style.display = 'inline-block';
            }
            
            const inhouseBadge = document.getElementById('inhouseBadge');
            if (data.inhouse > 0) {
                inhouseBadge.textContent = data.inhouse;
                inhouseBadge.style.display = 'inline-block';
            }
            
            const outhouseBadge = document.getElementById('outhouseBadge');
            if (data.outhouse > 0) {
                outhouseBadge.textContent = data.outhouse;
                outhouseBadge.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Error initializing approval counts:', error);
        }
    }

    // ========================================
    // 🔢 TRANSACTION COUNTS SYSTEM
    // ========================================
    
    // Function untuk update transaction counts
    async function updateTransactionCounts() {
        try {
            const response = await fetch('/api/transaction-counts');
            const data = await response.json();
            
            const requestPartCount = data.request_parts;
            const checkupCount = data.general_checkups;
            const totalCount = requestPartCount + checkupCount;
            
            // Update total badge
            const totalBadge = document.getElementById('totalTransactionBadge');
            if (totalBadge) {
                if (totalCount > 0) {
                    totalBadge.textContent = totalCount;
                    totalBadge.style.display = 'inline-block';
                } else {
                    totalBadge.style.display = 'none';
                }
            }
            
            // Update request part badge
            const requestPartBadge = document.getElementById('requestPartBadge');
            if (requestPartBadge) {
                if (requestPartCount > 0) {
                    requestPartBadge.textContent = requestPartCount;
                    requestPartBadge.style.display = 'inline-block';
                } else {
                    requestPartBadge.style.display = 'none';
                }
            }
            
            // Update checkup badge
            const checkupBadge = document.getElementById('checkupBadge');
            if (checkupBadge) {
                if (checkupCount > 0) {
                    checkupBadge.textContent = checkupCount;
                    checkupBadge.style.display = 'inline-block';
                } else {
                    checkupBadge.style.display = 'none';
                }
            }
            
        } catch (error) {
            console.error('Error fetching transaction counts:', error);
        }
    }

    // Initialize transaction counts saat page load
    async function initializeTransactionCounts() {
        try {
            const response = await fetch('/api/transaction-counts');
            const data = await response.json();
            
            const totalCount = data.request_parts + data.general_checkups;
            
            // Update badges
            const totalBadge = document.getElementById('totalTransactionBadge');
            if (totalBadge && totalCount > 0) {
                totalBadge.textContent = totalCount;
                totalBadge.style.display = 'inline-block';
            }
            
            const requestPartBadge = document.getElementById('requestPartBadge');
            if (requestPartBadge && data.request_parts > 0) {
                requestPartBadge.textContent = data.request_parts;
                requestPartBadge.style.display = 'inline-block';
            }
            
            const checkupBadge = document.getElementById('checkupBadge');
            if (checkupBadge && data.general_checkups > 0) {
                checkupBadge.textContent = data.general_checkups;
                checkupBadge.style.display = 'inline-block';
            }
        } catch (error) {
            console.error('Error initializing transaction counts:', error);
        }
    }

    // ========================================
    // REQUEST PARTS REAL-TIME SYSTEM (GLOBAL)
    // ========================================
    const REQUEST_PART_CHECK_INTERVAL = 10000; // 10 detik
    let lastRequestPartCheck = new Date().toISOString();
    let previousRequestPartStatuses = {};
    let isRequestPartInitialized = false;

    function getRequestPartStatusLabel(status) {
        const labels = {
            'pending': 'Pending',
            'approved_kadiv': 'Approved Kadiv',
            'approved_kagud': 'Approved Kagud',
            'ready': 'Ready',
            'completed': 'Completed',
            'verified': 'Verified',
            'rejected': 'Rejected',
        };
        return labels[status] || status;
    }

    function getRequestPartStatusBadgeClass(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved_kadiv': 'bg-blue-100 text-blue-800',
            'approved_kagud': 'bg-purple-100 text-purple-800',
            'ready': 'bg-emerald-100 text-emerald-800',
            'completed': 'bg-green-100 text-green-800',
            'verified': 'bg-gray-100 text-gray-800',
            'rejected': 'bg-red-100 text-red-800',
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    function getRequestPartKeterangan(status) {
        const map = {
            'pending': 'Menunggu approval Kepala Dept.',
            'approved_kadiv': 'Menunggu approval PUD',
            'approved_kagud': 'Barang sedang disiapkan',
            'ready': 'Barang siap diambil',
            'completed': 'Menunggu verifikasi penerimaan',
            'verified': 'Request selesai - Barang diterima',
            'rejected': 'Request ditolak'
        };
        return map[status] || '-';
    }

    // Show notification toast
    function showRequestPartNotification(request, oldStatus, newStatus) {
        const container = document.getElementById('notificationContainer');
        if (!container) return;
        
        const notification = document.createElement('div');
        notification.className = 'notification-toast';
        
        notification.innerHTML = `
            <div class="notification-icon" style="background: #3b82f6;">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="notification-content">
                <div class="notification-title">Request Part Update</div>
                <div class="notification-message">
                    <strong>${request.request_number}</strong><br>
                    ${getRequestPartStatusLabel(oldStatus)} → ${getRequestPartStatusLabel(newStatus)}
                </div>
                <div class="notification-time">Baru saja</div>
            </div>
            <div class="notification-badge" style="background: #3b82f6;">NEW</div>
        `;
        
        notification.addEventListener('click', function() {
            window.location.href = '/request-parts';
        });
        
        container.appendChild(notification);
        
        if (typeof playNotificationSound === 'function') {
            playNotificationSound();
        }
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => {
                if (notification.parentNode) notification.remove();
            }, 300);
        }, 10000);
    }

    // Update UI kalau lagi di halaman request-parts
    function updateRequestPartUI(request) {
        // Update row di table
        const row = document.querySelector(`tr[data-id="${request.id}"]`);
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRequestPartStatusBadgeClass(request.status)}`;
                statusBadge.textContent = getRequestPartStatusLabel(request.status);
            }
            
            const keteranganCell = row.querySelector('.keterangan-cell');
            if (keteranganCell) {
                keteranganCell.textContent = request.keterangan || getRequestPartKeterangan(request.status);
            }
            
            row.dataset.status = request.status;
            
            // Update verify button
            const actionButtons = row.querySelector('.action-buttons');
            if (actionButtons) {
                const existingVerify = actionButtons.querySelector('.verify-btn');
                if (existingVerify) existingVerify.remove();
                
                if (request.status === 'completed') {
                    const verifyBtn = document.createElement('button');
                    verifyBtn.className = 'verify-btn bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 transition';
                    verifyBtn.textContent = 'Verify';
                    verifyBtn.onclick = () => verifyRequest(request.id);
                    actionButtons.appendChild(verifyBtn);
                }
            }
            
            // Highlight
            row.style.backgroundColor = '#dcfce7';
            setTimeout(() => row.style.backgroundColor = '', 3000);
        }
    }

    // Update stats
    function updateRequestPartStats(stats) {
        const elements = {
            'stat-pending': stats.pending,
            'stat-approved-kadiv': stats.approved_kadiv,
            'stat-approved-kagud': stats.approved_kagud,
            'stat-completed': stats.completed,
            'stat-rejected': stats.rejected,
        };
        
        for (const [id, value] of Object.entries(elements)) {
            const el = document.getElementById(id);
            if (el && parseInt(el.textContent) !== value) {
                el.textContent = value;
            }
        }
    }

    // Main check function
    async function checkRequestPartUpdates() {
        if (document.hidden) return;
        
        try {
            const response = await fetch(`/request-parts/check-updates?since=${encodeURIComponent(lastRequestPartCheck)}`);
            const data = await response.json();
            
            // Update stats kalau ada
            if (data.stats) {
                updateRequestPartStats(data.stats);
            }
            
            if (data.has_changes && data.changed_requests && data.changed_requests.length > 0) {
                const isOnRequestPartsPage = window.location.pathname.includes('/request-parts');
                
                data.changed_requests.forEach(request => {
                    const oldStatus = previousRequestPartStatuses[request.id];
                    const newStatus = request.status;
                    
                    if (isRequestPartInitialized && oldStatus && oldStatus !== newStatus) {
                        // Show notification
                        showRequestPartNotification(request, oldStatus, newStatus);
                        
                        // Update UI kalau di halaman request-parts
                        if (isOnRequestPartsPage) {
                            updateRequestPartUI(request);
                        }
                    }
                    
                    previousRequestPartStatuses[request.id] = newStatus;
                });
                
                lastRequestPartCheck = data.timestamp;
            }
            
        } catch (error) {
            console.error('Request part check error:', error);
        }
    }

    // Initialize
    async function initializeRequestPartStatuses() {
        try {
            const response = await fetch(`/request-parts/check-updates?since=${encodeURIComponent(lastRequestPartCheck)}`);
            const data = await response.json();
            
            if (data.changed_requests) {
                data.changed_requests.forEach(request => {
                    previousRequestPartStatuses[request.id] = request.status;
                });
            }
            
            if (data.timestamp) {
                lastRequestPartCheck = data.timestamp;
            }
            
            isRequestPartInitialized = true;
            console.log('Request part notification initialized');
            
        } catch (error) {
            console.error('Initialize error:', error);
        }
    }

    // ========================================
    // 🚀 INITIALIZE ALL SYSTEMS
    // ========================================
    
    // Initialize saat page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize approval counts
        initializeApprovalCounts();
        
        // Initialize transaction counts
        initializeTransactionCounts();
        
        // Initialize request part statuses (delay 2 detik)
        setTimeout(initializeRequestPartStatuses, 2000);
    });
    
    // Polling setiap 10 detik untuk check update
    setInterval(updateApprovalCounts, 10000);
    setInterval(updateTransactionCounts, 10000);
    setInterval(checkRequestPartUpdates, REQUEST_PART_CHECK_INTERVAL);
    
    // Update saat user kembali ke tab
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            updateApprovalCounts();
            updateTransactionCounts();
            checkRequestPartUpdates();
        }
    });
</script>
    
    @stack('scripts')
</body>
    
</html>