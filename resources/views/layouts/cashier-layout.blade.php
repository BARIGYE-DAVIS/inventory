<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Cashier Dashboard') - {{ auth()->user()->business->name }}</title>
   <!-- <script src="https://cdn.tailwindcss.com"></script>-->

 @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar.hidden-mobile {
                transform: translateX(-100%);
            }
        }

        /* Accordion Styles */
        .accordion-toggle {
            cursor: pointer;
            user-select: none;
        }

        .accordion-toggle i:last-child {
            transition: transform 0.3s ease;
            transform-origin: center;
        }

        .accordion-content {
            will-change: max-height, opacity;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar fixed md:relative z-20 w-64 bg-indigo-900 text-white overflow-y-auto h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-yellow-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-cash-register text-2xl text-yellow-300"></i>
                        <span class="ml-2 text-lg font-bold">{{ auth()->user()->business->name }}</span>
                    </div>
                    <button onclick="toggleSidebar()" class="md:hidden text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-xs text-yellow-200 mt-1">💰 Cashier Portal</p>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <!-- Dashboard -->
                <a href="{{ route('cashier.dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('cashier.dashboard') ? 'bg-yellow-800' : 'hover:bg-yellow-800' }} transition-colors">
                    <i class="fas fa-home text-lg"></i>
                    <span>My Dashboard</span>
                </a>

                <!-- My Profit -->
                <a href="{{ route('profit.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-yellow-800 transition-colors">
                    <i class="fas fa-chart-pie text-lg"></i>
                    <span>My Profit</span>
                </a>

                <!-- NEW SALE (BIGGEST BUTTON) -->
                <a href="{{ route('pos.index') }}" class="flex items-center justify-center space-x-3 p-4 rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-lg shadow-lg transition-colors">
                    <i class="fas fa-plus-circle text-2xl"></i>
                    <span>NEW SALE</span>
                </a>

                <!-- ... existing sidebar code ... -->

<li>
    <a href="{{ route('cashier.invoices.index') }}"
       class="flex items-center px-4 py-2 text-gray-700 rounded-md hover:bg-indigo-600 hover:text-white transition group {{ request()->routeIs('cashier.invoices.*') ? 'bg-indigo-100 text-indigo-700 font-bold' : '' }}">
        <i class="fas fa-file-invoice-dollar text-indigo-500 group-hover:text-white w-5"></i>
        <span class="ml-3">Invoices</span>
    </a>
</li>

<!-- ... further sidebar code ... -->



                <!-- My Sales -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-yellow-200 font-semibold">
                        <i class="fas fa-receipt text-lg"></i>
                        <span>My Sales</span>
                    </div>
                </div>

                <!-- EXPENSES SECTION (Accordion) -->
                <div class="accordion-group">
                    <button type="button" class="accordion-toggle flex items-center justify-between w-full p-3 rounded-lg text-left bg-indigo-700 hover:bg-indigo-600 transition-colors font-semibold" data-accordion="expenses">
                        <span class="flex items-center space-x-3">
                            <i class="fas fa-wallet text-lg"></i>
                            <span>Expenses</span>
                        </span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content expenses-content space-y-1 mt-2 max-h-96 overflow-hidden transition-all duration-300">
                        <a href="{{ route('cashier.expenses.create') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800 transition-colors text-sm">
                            <i class="fas fa-plus-circle text-xs"></i>
                            <span>Record Expense</span>
                        </a>
                    </div>
                </div>

                <!-- ANALYTICS SECTION (Accordion) -->
                <div class="accordion-group">
                    <button type="button" class="accordion-toggle flex items-center justify-between w-full p-3 rounded-lg text-left bg-purple-700 hover:bg-purple-600 transition-colors font-semibold" data-accordion="analytics">
                        <span class="flex items-center space-x-3">
                            <i class="fas fa-chart-line text-lg"></i>
                            <span>Analytics</span>
                        </span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content analytics-content space-y-1 mt-2 max-h-96 overflow-hidden transition-all duration-300">
                        <a href="{{ route('cashier.performance') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-purple-800 transition-colors text-sm">
                            <i class="fas fa-tachometer-alt text-xs"></i>
                            <span>My Performance</span>
                        </a>
                    </div>
                </div>

                <!-- MANAGEMENT SECTION (Accordion) -->
                <div class="accordion-group">
                    <button type="button" class="accordion-toggle flex items-center justify-between w-full p-3 rounded-lg text-left bg-cyan-700 hover:bg-cyan-600 transition-colors font-semibold" data-accordion="management">
                        <span class="flex items-center space-x-3">
                            <i class="fas fa-cog text-lg"></i>
                            <span>Management</span>
                        </span>
                        <i class="fas fa-chevron-down transition-transform duration-300"></i>
                    </button>
                    <div class="accordion-content management-content space-y-1 mt-2 max-h-96 overflow-hidden transition-all duration-300">
                        <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-cyan-800 transition-colors text-sm">
                            <i class="fas fa-users text-xs"></i>
                            <span>Customers</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-cyan-800 transition-colors text-sm">
                            <i class="fas fa-search text-xs"></i>
                            <span>Search Product</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-cyan-800 transition-colors text-sm">
                            <i class="fas fa-user-circle text-xs"></i>
                            <span>My Profile</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-yellow-800 mt-auto">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 bg-yellow-800 rounded-full flex items-center justify-center">
                        <span class="text-lg font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-yellow-200">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 p-2 bg-red-600 rounded-lg hover:bg-red-700">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Top Bar -->
            <header class="bg-white shadow-sm z-10">
                <div class="flex items-center justify-between p-4">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800">
                        @yield('page-title', 'Cashier Dashboard')
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="hidden md:inline text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('D, d M Y h:i A') }}
                        </span>
                        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-cash-register mr-1"></i>
                            <span class="hidden md:inline">NEW SALE</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded animate-pulse">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded animate-pulse">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
                @endif

                @if(session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded">
                    <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                </div>
                @endif

                @if(session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded">
                    <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                        <div>
                            <p class="font-semibold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Page Content -->
                @yield('content')

            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden-mobile');
        }

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = event.target.closest('button');
            
            if (window.innerWidth < 768 && !sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.add('hidden-mobile');
            }
        });

        // Accordion Functionality
        document.querySelectorAll('.accordion-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const accordionName = this.getAttribute('data-accordion');
                const content = document.querySelector('.' + accordionName + '-content');
                const icon = this.querySelector('i:last-child');
                const isExpanded = content.style.maxHeight && content.style.maxHeight !== '0px';

                if (isExpanded) {
                    // Collapse
                    content.style.maxHeight = '0px';
                    content.style.opacity = '0';
                    content.style.marginTop = '0px';
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    // Expand
                    content.style.maxHeight = content.scrollHeight + 'px';
                    content.style.opacity = '1';
                    content.style.marginTop = 'var(--tw-space-y-reverse, 0) calc(0.5rem * calc(1 - var(--tw-space-y-reverse)))';
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Set initial state for accordion contents (collapsed by default)
        document.querySelectorAll('.accordion-content').forEach(content => {
            content.style.maxHeight = '0px';
            content.style.opacity = '0';
            content.style.overflow = 'hidden';
            content.style.transition = 'all 0.3s ease';
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('[class*="bg-green-100"], [class*="bg-red-100"], [class*="bg-blue-100"], [class*="bg-yellow-100"]');
            alerts.forEach(alert => {
                if (alert.classList.contains('animate-pulse')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>