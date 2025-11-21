{{-- resources/views/layouts/andon.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Andon Display') - WarehousMDD</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts - Montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Base styles */
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #000000;
            color: #fff;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* Header Navigation - sama seperti app.blade.php */
        .navbar {
            background-color: #000000;
            border: 3px solid #ffffff;
            padding: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        /* Header Table Structure - sama seperti app.blade.php */
        .table-container {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            vertical-align: middle;
            padding: 10px;
            text-align: center;
        }

        .table-cell:not(:last-child) {
            border-right: 3px solid white;
        }

        .logo-cell {
            width: 150px;
        }

        .title-cell {
            width: auto;
        }

        .datetime-cell {
            width: 200px;
        }

        .logo {
            display: block;
            margin: 0 auto;
            width: 120px;
            height: auto;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .page-title {
            font-weight: bold;
            font-size: 60px;
            text-align: center;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .date-time {
            font-weight: bold;
            font-size: 30px;
            color: white;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Controls Bar */
        .controls-bar {
            background: #0a0a0a;
            padding: 1rem 2rem;
            border-bottom: 2px solid #333;
        }

        /* Counter Badges */
        .counter-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            margin-right: 1rem;
        }

        .counter-open {
            background: #059669;
            border: 2px solid #10b981;
        }

        .counter-delay {
            background: #dc2626;
            border: 2px solid #ef4444;
        }

        .counter-process {
            background: #2563eb;
            border: 2px solid #3b82f6;
        }

        /* Table Styles - UPDATE YANG INI */
        .table-dark-custom {
            background-color: #000000;
            color: #ffffff;
        }

        .table-dark-custom th,
        .table-dark-custom td {
            border: 1px solid #ffffff !important; /* Border putih */
        }

        .table-dark-custom th {
            background-color: #111111;
            border: 1px solid #ffffff !important; /* Border putih untuk header */
        }

        .table-bordered {
            border: 1px solid #ffffff !important; /* Border luar tabel */
        }

        .andon-table {
            width: 100%;
            border-collapse: collapse; /* UBAH dari separate menjadi collapse */
            font-size: 0.9rem;
            border: 1px solid #ffffff !important; /* Border luar */
        }

        .andon-table thead {
            background: #1a1a1a;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .andon-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #ffffff !important; /* Border putih */
            background: #1a1a1a;
            font-size: 0.85rem;
            color: #fbbf24;
        }

        .andon-table tbody tr {
            background: #0a0a0a;
            transition: background 0.2s ease;
        }

        .andon-table tbody tr:hover {
            background: #1a1a1a;
        }

        .andon-table tbody tr:nth-child(even) {
            background: #0f0f0f;
        }

        .andon-table tbody tr:nth-child(even):hover {
            background: #1a1a1a;
        }

        .andon-table td {
            padding: 1rem;
            border: 1px solid #ffffff !important; /* Border putih */
            font-weight: 500;
            color: #ffffff;
        }
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 1px;
            border-radius: 0.375rem;
            font-weight: 700;
            font-size: 0.875rem;
            text-align: center;
            min-width: 100px;
        }

        .status-open {
            background: #059669;
            color: #ffffff;
            border: 2px solid #10b981;
        }

        .status-process {
            background: #2563eb;
            color: #ffffff;
            border: 2px solid #3b82f6;
        }

        .status-completed {
            background: #16a34a;
            color: #ffffff;
            border: 2px solid #22c55e;
        }

        .status-delay {
            background: #dc2626;
            color: #ffffff;
            border: 2px solid #ef4444;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.7; }
        }

        /* Monospace Numbers */
        .mono-number {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #0a0a0a;
            border: 1px solid #333;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #666;
        }

        .empty-state svg {
            width: 120px;
            height: 120px;
            margin: 0 auto 1rem;
            opacity: 0.3;
        }

        /* Auto Refresh Indicator */
        .refresh-indicator {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: #1a1a1a;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            border: 2px solid #333;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .refresh-indicator.active {
            border-color: #10b981;
        }

        .refresh-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Filter Button */
        .filter-btn {
            background: #1a1a1a;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: 2px solid #333;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            background: #2a2a2a;
            border-color: #555;
        }

        .filter-btn.active {
            background: #fbbf24;
            color: #000000;
            border-color: #fbbf24;
        }

        /* Search Input */
        .search-input {
            background: #1a1a1a;
            color: #ffffff;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 2px solid #333;
            font-size: 1rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #fbbf24;
        }

        .search-input::placeholder {
            color: #666;
        }

        /* Mobile-first responsive styles */
        @media (max-width: 1199px) {
            .page-title {
                font-size: 48px;
            }
        }

        @media (max-width: 991px) {
            .page-title {
                font-size: 36px;
            }
            
            .logo {
                width: 100px;
            }
            
            .date-time {
                font-size: 24px;
            }
        }

        @media (max-width: 767px) {
            .navbar {
                border-width: 2px;
            }
            
            /* Single row header layout */
            .table-container {
                display: table;
                width: 100%;
                table-layout: fixed;
            }
            
            .table-row {
                display: table-row;
            }
            
            .table-cell {
                display: table-cell;
                vertical-align: middle;
                padding: 5px;
            }
            
            .logo-cell {
                width: 70px;
            }
            
            .title-cell {
                width: auto;
            }
            
            .datetime-cell {
                width: 90px;
            }
            
            .logo {
                width: 50px;
                height: auto;
            }
            
            .page-title {
                font-size: 20px;
                padding: 0;
            }
            
            .date-time {
                font-size: 14px;
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .logo-cell {
                width: 50px;
            }
            
            .datetime-cell {
                width: 70px;
            }
            
            .logo {
                width: 40px;
            }
            
            .page-title {
                font-size: 16px;
            }
            
            .date-time {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <!-- Header Navigation - sama seperti app.blade.php -->
    <nav class="navbar navbar-dark">
        <div class="table-container">
            <div class="table-row">
                <!-- Logo -->
                <div class="table-cell logo-cell">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/logostep.png') }}" alt="Logo" class="logo">
                    </a>
                </div>
                <!-- Title -->
                <div class="table-cell title-cell">
                    <span class="page-title">
                        @yield('page-title', 'ANDON MONITORING DISPLAY')
                    </span>
                </div>
                <!-- Date/Time -->
                <div class="table-cell datetime-cell">
                    <div class="date-time" id="date-time">
                        <span id="currentDate"></span>
                        <span id="currentTime"></span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="p-0">
        @yield('content')
    </main>

    <!-- Auto Refresh Indicator -->
    <div class="refresh-indicator" id="refreshIndicator">
        <div class="refresh-dot"></div>
        <span>Auto Refresh: <span id="refreshCountdown">30</span>s</span>
    </div>

    <!-- Scripts -->
    <script>
        // Update Date Time - sama seperti app.blade.php
        function updateDateTime() {
            const date = new Date();
            const formattedDate = date.toLocaleDateString('id-ID');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            const formattedTime = `${hours}:${minutes}:${seconds}`;
            
            const dateElement = document.getElementById('currentDate');
            const timeElement = document.getElementById('currentTime');
            
            if (dateElement && timeElement) {
                dateElement.textContent = formattedDate;
                timeElement.textContent = formattedTime;
            }
        }

        // Auto Refresh
        let refreshCountdown = 30;
        let countdownInterval;

        function startRefreshCountdown() {
            refreshCountdown = 30;
            
            if (countdownInterval) {
                clearInterval(countdownInterval);
            }
            
            countdownInterval = setInterval(() => {
                refreshCountdown--;
                const countdownElement = document.getElementById('refreshCountdown');
                if (countdownElement) {
                    countdownElement.textContent = refreshCountdown;
                }
                
                if (refreshCountdown <= 0) {
                    location.reload();
                }
            }, 1000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            // Auto refresh every 30 seconds
            startRefreshCountdown();
        });

        // Pause auto-refresh on user interaction
        document.addEventListener('click', function() {
            startRefreshCountdown();
        });

        document.addEventListener('keydown', function() {
            startRefreshCountdown();
        });
    </script>

    @stack('scripts')
</body>
</html>