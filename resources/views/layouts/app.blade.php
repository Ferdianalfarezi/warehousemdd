<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - WarehousMDD</title>

    {{-- ✅ FIX 1: Alpine HARUS di head, SEBELUM body render --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- ✅ FIX 2: Vite assets (CSS dulu biar tidak FOUC) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ✅ FIX 3: CDN scripts di-load async/defer supaya tidak blocking render --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js" defer></script>

    {{-- CSS tetap di head (tidak bisa defer) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

    <link rel="icon" type="image/png" href="{{ asset('images/logomdddark.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" href="{{ asset('images/logomddwhite.png') }}" media="(prefers-color-scheme: dark)">

    {{-- ✅ FIX 4: Preload sidebar state SEBELUM Alpine init (inline, tidak butuh network) --}}
    <script>
        // Jalankan sync sebelum apapun — tidak ada network request, cuma baca localStorage
        (function() {
            try {
                const saved = localStorage.getItem('sidebar_menu_state');
                window.__PRELOADED_MENU_STATE__ = saved ? JSON.parse(saved) : null;
            } catch(e) {
                window.__PRELOADED_MENU_STATE__ = null;
            }
        })();
    </script>
</head>

<style>
    /* Prevent layout shift saat Alpine belum ready */
    [x-cloak] { display: none !important; }

    /* ===================== SCROLLBAR ===================== */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
    ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.5); }

    .sidebar-nav-container {
        height: calc(100vh - 80px - 80px);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.3) rgba(255,255,255,0.05);
    }

    /* ===================== MODAL ===================== */
    #createModal, #editModal {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        background-color: rgba(0,0,0,0);
        transition: backdrop-filter 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }
    #createModal.modal-fade-in, #editModal.modal-fade-in {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        background-color: rgba(0,0,0,0.3);
    }
    #imagePreviewModal {
        backdrop-filter: blur(0px);
        -webkit-backdrop-filter: blur(0px);
        background-color: rgba(0,0,0,0);
        transition: backdrop-filter 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }
    #imagePreviewModal.modal-fade-in {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        background-color: rgba(0,0,0,0.75);
    }
    #createModal > div, #editModal > div {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    #createModal.modal-fade-in > div, #editModal.modal-fade-in > div {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    #imagePreviewModal > div {
        opacity: 0;
        transform: scale(0.9);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    #imagePreviewModal.modal-fade-in > div { opacity: 1; transform: scale(1); }
    .modal-fade-in { animation: fadeIn 0.3s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

    /* ===================== SELECT2 ===================== */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db; border-radius: 0.5rem; height: 42px; padding: 0.5rem 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 1.5; padding-left: 0; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: #000; }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem;
    }
    .select2-dropdown { border: 1px solid #d1d5db; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }

    /* ===================== SIDEBAR ===================== */
    .sidebar-gradient { background: linear-gradient(180deg, #000000 0%, #1a1a1a 100%); }
    .logo-container { position: relative; overflow: hidden; }
    .logo-image { width: 75px; height: 50px; object-fit: contain; transition: transform 0.3s ease; }
    .logo-container:hover .logo-image { transform: scale(1.05); }

    .menu-group-header {
        position: relative; padding: 0.875rem 1rem; margin: 0.5rem 0;
        cursor: pointer; border-radius: 0.75rem; transition: background 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .menu-group-header:hover { background: rgba(255,255,255,0.05); }
    .menu-group-header.active { background: rgba(255,255,255,0.1); }

    .submenu-container {
        display: grid; grid-template-rows: 0fr;
        transition: grid-template-rows 0.35s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }
    .submenu-container.open { grid-template-rows: 1fr; }
    .submenu-container > div { min-height: 0; overflow: visible; }

    .menu-item {
        position: relative; margin: 0.5rem 0; border-radius: 0.75rem;
        overflow: hidden; transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .menu-item::before {
        content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px;
        background: white; transform: scaleY(0); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .menu-item.active::before { transform: scaleY(1); }
    .menu-item.active { background: white; color: black; }
    .menu-item:not(.active):hover { background: rgba(255,255,255,0.08); }

    .submenu-item {
        position: relative; margin: 0.25rem 0;
        border-radius: 0.5rem; transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    }
    .submenu-item::before {
        content: ''; position: absolute; left: -1rem; top: 50%;
        width: 0.5rem; height: 1px; background: rgba(255,255,255,0.2); transition: all 0.3s ease;
    }
    .submenu-item:hover::before { width: 0.75rem; background: rgba(255,255,255,0.5); }
    .submenu-item.active { background: rgba(255,255,255,0.15); color: white; font-weight: 500; }
    .submenu-item.active::before { width: 0.75rem; background: white; }
    .submenu-item:not(.active):hover { background: rgba(255,255,255,0.08); transform: translateX(2px); }

    .menu-icon { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
    .menu-group-header:hover .menu-icon, .menu-item:hover .menu-icon { transform: scale(1.1); }
    .chevron-icon { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
    .chevron-icon.open { transform: rotate(180deg); }

    .logout-btn {
        background: linear-gradient(135deg, rgba(220,38,38,0.1) 0%, rgba(153,27,27,0.1) 100%);
        border: 1px solid rgba(220,38,38,0.2); transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
    }
    .logout-btn:hover {
        background: linear-gradient(135deg, rgba(220,38,38,0.2) 0%, rgba(153,27,27,0.2) 100%);
        border-color: rgba(220,38,38,0.4); transform: translateY(-1px);
    }

    /* ===================== NOTIFICATION ===================== */
    .notification-container {
        position: fixed; bottom: 20px; right: 20px; z-index: 9999;
        display: flex; flex-direction: column; gap: 10px; max-width: 400px;
    }
    .notification-toast {
        background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        padding: 16px; display: flex; align-items: start; gap: 12px;
        animation: slideInRight 0.3s ease-out; cursor: pointer; transition: transform 0.2s;
    }
    .notification-toast:hover { transform: translateX(-5px); }
    .notification-toast.fade-out { animation: slideOutRight 0.3s ease-out forwards; }
    @keyframes slideInRight { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }
    .notification-icon { flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .notification-icon.inhouse { background: #3b82f6; }
    .notification-icon.outhouse { background: #9333ea; }
    .notification-content { flex: 1; }
    .notification-title { font-weight: 600; color: #111827; font-size: 14px; margin-bottom: 4px; }
    .notification-message { color: #6b7280; font-size: 13px; line-height: 1.4; }
    .notification-time { color: #9ca3af; font-size: 11px; margin-top: 4px; }
    .notification-badge {
        background: #ef4444; color: white; font-size: 11px; font-weight: 700;
        padding: 2px 8px; border-radius: 12px; min-width: 20px; text-align: center;
    }
</style>

<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden" x-data="menuState()">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient text-white transform transition-transform duration-300 ease-in-out shadow-2xl"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="logo-container flex items-center justify-center h-20 border-b border-gray-800 border-opacity-50 px-4">
                <img src="{{ asset('images/logomdd.png') }}" alt="Logo" class="logo-image">
                <h1 class="text-xl tracking-wider mt-2"><i>Warehouse</i></h1>
            </div>

            <div class="sidebar-nav-container px-3 space-y-1">
                <nav class="mt-4 space-y-1 pb-4">
                @if(auth()->check() && auth()->user()->role_id != 4)
                    <a href="{{ route('dashboard') }}"
                    class="menu-item flex items-center px-4 py-2.5 text-gray-300 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span class="font-semibold text-sm">Dashboard</span>
                    </a>

                    <!-- Data Master -->
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
                                    Spareparts
                                </a>
                                <a href="{{ route('barangs.index') }}"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('barangs.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                    </svg>
                                    Dies
                                </a>
                                <a href="{{ route('lines.index') }}"
                                    class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('lines.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                    </svg>
                                    Line
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
                    @endif
                    
                    <!-- Data Transaksi -->
                    <div>
                        <button @click="toggleMenu('transaction')"
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.transaction ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="font-semibold text-sm">Data Transaksi</span>
                                <span id="totalTransactionBadge" class="ml-2 bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
                            </div>
                            <svg class="w-4 h-4 chevron-icon" :class="openMenus.transaction ? 'open' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="submenu-container" :class="openMenus.transaction ? 'open' : ''">
                            <div class="ml-8 mt-1 space-y-1">
                                <a href="{{ route('request-parts.index') }}"
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('request-parts.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-width="2" d="M12 6v12m6-6H6"/>
                                        </svg>
                                        Request Part
                                    </div>
                                    <span id="requestPartBadge" class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
                                </a>
                                <a href="{{ route('general-checkups.index') }}"
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('general-checkups.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        General Checkups
                                    </div>
                                    <span id="checkupBadge" class="bg-orange-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
                                </a>
                                <a href="{{ route('request-repairs.index') }}"
                                    class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('request-repairs.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Request Repair
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                @if(auth()->check() && auth()->user()->role_id != 4)
                    <!-- Approval -->
                    <div>
                        <button @click="toggleMenu('approval')"
                                class="menu-group-header w-full flex items-center justify-between"
                                :class="openMenus.approval ? 'active' : ''">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3 menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-sm">Approval</span>
                                <span id="totalApprovalBadge" class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
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
                                    <span id="inhouseBadge" class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
                                </a>
                                <a href="{{ route('subcont.confirm.index') }}"
                                class="submenu-item flex items-center justify-between px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('subcont.confirm.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Outhouse
                                    </div>
                                    <span id="outhouseBadge" class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full" style="display:none;">0</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Andon -->
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
                                <a href="{{ route('andon.inhouse.index') }}" target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Inhouse
                                </a>
                                <a href="{{ route('andon.outhouse.index') }}" target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Outhouse
                                </a>
                                <a href="{{ route('andon.general-checkup.index') }}" target="_blank"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    General Checkup
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Data History -->
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
                                <a href="{{ route('history-checkups.index') }}"
                                class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('history-checkups.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Preventive Checkups
                                </a>
                                <a href="{{ route('history-repairs.index') }}"
                                    class="submenu-item flex items-center px-3 py-2.5 text-sm text-gray-300 {{ request()->routeIs('history-repairs.*') ? 'active' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Request Repair
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                </nav>
            </div>

            <!-- Logout -->
            <div class="absolute bottom-0 left-0 right-0 p-3 border-t border-gray-800 border-opacity-50 bg-gradient-to-t from-black to-transparent">
                <button onclick="confirmLogout()"
                        class="logout-btn flex items-center w-full px-4 py-3 rounded-lg text-gray-300 transition-all duration-300">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="font-semibold text-sm">Logout</span>
                </button>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
            <header class="bg-white shadow-sm z-40 sticky top-0">
                <div class="flex items-center justify-between px-6 py-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-600 hover:text-black focus:outline-none transition-all duration-200 hover:scale-110">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2 text-gray-600">
                            <div class="text-sm">
                                <span id="current-date" class="font-medium"></span>
                                <span class="mx-2">|</span>
                                <span id="current-time" class="font-semibold"></span>
                            </div>
                        </div>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-3 hover:bg-gray-50 px-4 py-2 rounded-xl transition-all duration-200">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/users/'.auth()->user()->avatar) }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-200">
                                @else
                                    <div class="w-9 h-9 bg-gradient-to-br from-gray-800 to-black text-white rounded-full flex items-center justify-center font-bold ring-2 ring-gray-200">
                                        {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->nama }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->role->nama ?? 'User' }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-500 transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg py-2 z-50 border border-gray-100"
                                x-cloak>
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

            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                @yield('content')
            </main>

            <footer style="background:#ffffff00;border-top:1px solid #ffffffa2;padding:1.5rem 2rem;text-align:center;">
                <p style="margin:0;font-size:0.875rem;color:#888888;">
                    &copy; {{ date('Y') }} <strong style="color:#000000;"><i>STEP IT DEPT</i></strong> - All rights reserved.
                </p>
            </footer>
        </div>
    </div>

    <div id="notificationContainer" class="notification-container"></div>

    <script>
    // ============================================
    // ALPINE MENU STATE
    // ============================================
    function menuState() {
        const currentPath = window.location.pathname;

        const defaultState = {
            master:      currentPath.includes('/parts') || currentPath.includes('/barangs') || currentPath.includes('/schedules') || currentPath.includes('/check-indicators') || currentPath.includes('/suppliers') || currentPath.includes('/users'),
            transaction: currentPath.includes('/request-parts') || currentPath.includes('/general-checkups'),
            approval:    currentPath.includes('/pdd/confirm') || currentPath.includes('/subcont/confirm'),
            andon:       currentPath.includes('/andon/'),
            history:     currentPath.includes('/history-checkups') || currentPath.includes('/history-request-parts'),
        };

        const saved = window.__PRELOADED_MENU_STATE__ || {};

        return {
            sidebarOpen: true,
            openMenus: {
                master:      defaultState.master      || saved.master      || false,
                transaction: defaultState.transaction || saved.transaction || false,
                approval:    defaultState.approval    || saved.approval    || false,
                andon:       defaultState.andon       || saved.andon       || false,
                history:     defaultState.history     || saved.history     || false,
            },
            init() {
                this.$watch('openMenus', val => {
                    try { localStorage.setItem('sidebar_menu_state', JSON.stringify(val)); } catch(e) {}
                }, { deep: true });
            },
            toggleMenu(menu) {
                this.openMenus[menu] = !this.openMenus[menu];
            }
        };
    }

    // ============================================
    // LOGOUT
    // ============================================
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#E4080A',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-lg px-6 py-2.5', cancelButton: 'rounded-lg px-6 py-2.5' }
        }).then(result => {
            if (result.isConfirmed) {
                try { localStorage.removeItem('sidebar_menu_state'); } catch(e) {}
                document.getElementById('logout-form').submit();
            }
        });
    }

    // ============================================
    // CLOCK
    // ============================================
    function updateDateTime() {
        const now = new Date();
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // ============================================
    // ✅ BADGE POLLING — delay 3 detik, jangan block render
    // ============================================
    let previousCounts = { inhouse: 0, outhouse: 0 };
    let isFirstLoad = true;

    function setBadge(id, count) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = count;
        el.style.display = count > 0 ? 'inline-block' : 'none';
    }

    async function fetchApprovalCounts() {
        try {
            const data = await fetch('/api/approval-counts').then(r => r.json());
            if (!isFirstLoad) {
                if (data.inhouse > previousCounts.inhouse) showNotification('inhouse', data.inhouse - previousCounts.inhouse);
                if (data.outhouse > previousCounts.outhouse) showNotification('outhouse', data.outhouse - previousCounts.outhouse);
            }
            previousCounts = { inhouse: data.inhouse, outhouse: data.outhouse };
            setBadge('totalApprovalBadge', data.inhouse + data.outhouse);
            setBadge('inhouseBadge', data.inhouse);
            setBadge('outhouseBadge', data.outhouse);
        } catch(e) {}
    }

    async function fetchTransactionCounts() {
        try {
            const data = await fetch('/api/transaction-counts').then(r => r.json());
            setBadge('totalTransactionBadge', data.request_parts + data.general_checkups);
            setBadge('requestPartBadge', data.request_parts);
            setBadge('checkupBadge', data.general_checkups);
        } catch(e) {}
    }

    // ✅ Delay 3 detik — biar page render dulu, badge tidak urgent
    setTimeout(async () => {
        await Promise.all([fetchApprovalCounts(), fetchTransactionCounts()]);
        isFirstLoad = false;

        // Polling setiap 30 detik (bukan 10 — tidak perlu se-real-time itu)
        setInterval(fetchApprovalCounts, 30000);
        setInterval(fetchTransactionCounts, 30000);
    }, 3000);

    // Update saat tab aktif kembali
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && !isFirstLoad) {
            fetchApprovalCounts();
            fetchTransactionCounts();
        }
    });

    // ============================================
    // NOTIFICATION TOAST
    // ============================================
    function showNotification(type, count) {
        const container = document.getElementById('notificationContainer');
        const el = document.createElement('div');
        el.className = 'notification-toast';
        el.innerHTML = `
            <div class="notification-icon ${type}">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="notification-content">
                <div class="notification-title">${type === 'inhouse' ? 'Permintaan Inhouse Baru' : 'Permintaan Outhouse Baru'}</div>
                <div class="notification-message">Ada ${count} permintaan yang perlu di-approve</div>
                <div class="notification-time">Baru saja</div>
            </div>
            <div class="notification-badge">${count}</div>`;
        el.addEventListener('click', () => {
            window.location.href = type === 'inhouse' ? '{{ route("pdd.confirm.index") }}' : '{{ route("subcont.confirm.index") }}';
        });
        container.appendChild(el);
        setTimeout(() => {
            el.classList.add('fade-out');
            setTimeout(() => el.remove(), 300);
        }, 10000);
    }
    </script>

    @stack('scripts')
</body>
</html>