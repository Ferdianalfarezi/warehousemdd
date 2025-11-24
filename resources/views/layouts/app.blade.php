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
</head>
<style>
    [x-cloak] { display: none !important; }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
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
    }

    .submenu-container.open {
        grid-template-rows: 1fr;
    }

    .submenu-container > div {
        overflow: hidden;
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

            <!-- Navigation -->
            <nav class="mt-4 px-3 space-y-1 overflow-y-auto h-[calc(100vh-11rem)] pb-4">
                
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

                <!-- Data Transaksi Group -->
                <div>
                    <button @click="toggleMenu('transaction')" 
                            class="menu-group-header w-full flex items-center justify-between"
                            :class="openMenus.transaction ? 'active' : ''">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="font-semibold text-sm">Data Transaksi</span>
                        </div>
                        <svg class="w-4 h-4 chevron-icon" :class="openMenus.transaction ? 'open' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div class="submenu-container" :class="openMenus.transaction ? 'open' : ''">
                        <div class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('general-checkups.index') }}" 
                               class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('general-checkups.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                General Checkups
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
                        </div>
                        <svg class="w-4 h-4 chevron-icon" :class="openMenus.approval ? 'open' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div class="submenu-container" :class="openMenus.approval ? 'open' : ''">
                        <div class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('pdd.confirm.index') }}" 
                               class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('pdd.confirm.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Konfirmasi Inhouse
                            </a>

                            <a href="{{ route('subcont.confirm.index') }}" 
                               class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('subcont.confirm.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Konfirmasi Outhouse
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
                            <a href="{{ route('andon.inhouse.index') }}" 
                               class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('andon.inhouse.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Inhouse
                            </a>

                            <a href="{{ route('andon.outhouse.index') }}" 
                               class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('andon.outhouse.*') ? 'active' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Outhouse
                            </a>

                            <a href="{{ route('andon.general-checkup.index') }}" 
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
                                <a href="#" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
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
        </div>
    </div>

    <div x-data="notificationBadge()" x-init="init()" x-show="show" x-cloak
     class="fixed bottom-6 right-6 z-50 transition-all duration-300"
     x-transition:enter="transform ease-out duration-300"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transform ease-in duration-200"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0">
    
    <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl shadow-2xl p-4 pr-12 min-w-[320px] relative overflow-hidden">
        <!-- Close Button -->
        <button @click="close()" 
                class="absolute top-3 right-3 text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-1.5 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Icon -->
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm animate-pulse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <h3 class="font-bold text-lg mb-1">Approval Needed!</h3>
                <p class="text-sm text-white/90 mb-3">You have pending requests to review</p>
                
                <!-- Stats -->
                <div class="space-y-2">
                    <!-- Inhouse -->
                    <a :href="inhouseUrl" 
                       x-show="inhouseCount > 0"
                       class="flex items-center justify-between bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 hover:bg-white/30 transition-all duration-200 group">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="text-sm font-medium">Inhouse</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold" x-text="inhouseCount"></span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>

                    <!-- Outhouse -->
                    <a :href="outhouseUrl" 
                       x-show="outhouseCount > 0"
                       class="flex items-center justify-between bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 hover:bg-white/30 transition-all duration-200 group">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-sm font-medium">Outhouse</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-lg font-bold" x-text="outhouseCount"></span>
                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Animated background effect -->
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -skew-x-12 animate-shimmer"></div>
    </div>
</div>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
            
            if (currentPath.includes('/general-checkups')) {
                defaultState.transaction = true;
            }
            
            if (currentPath.includes('/pdd/confirm') || 
                currentPath.includes('/subcont/confirm')) {
                defaultState.approval = true;
            }
            
            if (currentPath.includes('/andon/inhouse') || 
                currentPath.includes('/andon/outhouse')) {
                defaultState.andon = true;
            }
            
            if (currentPath.includes('/history-checkups')) {
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
    </script>
    
    @stack('scripts')
</body>
    
    @stack('scripts')
</body>
</html>