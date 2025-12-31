<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ auth()->user()->business->name }}</title>
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar.hidden-mobile {
                transform: translateX(-100%);
            }
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar fixed md:relative z-20 w-64 bg-indigo-900 text-white overflow-y-auto h-screen">
            <!-- Logo -->
            <div class="p-4 border-b border-indigo-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-boxes text-2xl text-yellow-400"></i>
                        <span class="ml-2 text-lg font-bold truncate">{{ auth()->user()->business->name }}</span>
                    </div>
                    <button onclick="toggleSidebar()" class="md:hidden text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-xs text-indigo-300 mt-1">{{ auth()->user()->role->display_name }}</p>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                    <i class="fas fa-home text-lg"></i>
                    <span>Dashboard</span>
                </a>


             <a href="{{ route('profit.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-700">
            <i class="fas fa-chart-pie text-lg"></i>
         <span>Profit Report</span>
        </a>
      

                <a href="{{ route('pos.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-cash-register text-lg text-green-400"></i>
                    <span>POS - New Sale</span>
                </a>
             <a href="{{route('invoices.index')}}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                    <span>Invoices/credit sales</span>
            </a>
  <a href="{{ route('invoices.customersWithInvoices') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
    <i class="fas fa-building text-lg"></i>
    <span>Creditors</span>
</a>


                <!-- Products -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
                        <i class="fas fa-box text-lg"></i>
                        <span>Products</span>
                    </div>
                    <a href="{{ route('products.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg {{ request()->routeIs('products.index') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                        <i class="fas fa-list text-sm"></i>
                        <span>All Products</span>
                    </a>
                    <a href="{{ route('products.create') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg {{ request()->routeIs('products.create') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                        <i class="fas fa-plus text-sm"></i>
                        <span>Add Product</span>
                    </a>
                    <a href="{{ route('products.expired') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg {{ request()->routeIs('products.expired') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                        <i class="fas fa-exclamation-triangle text-sm text-red-400"></i>
                        <span>Expired Products</span>
                    </a>
                    <a href="{{ route('products.expiring-soon') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg {{ request()->routeIs('products.expiring-soon') ? 'bg-indigo-800' : 'hover:bg-indigo-800' }}">
                        <i class="fas fa-clock text-sm text-yellow-400"></i>
                        <span>Expiring Soon</span>
                    </a>
                </div>

                <!-- Sales -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span>Sales</span>
                    </div>
                    <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-list text-sm"></i>
                        <span>All Sales</span>
                    </a>
                    <a href="{{ route('sales.today') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-calendar-day text-sm"></i>
                        <span>Today's Sales</span>
                    </a>
                    <a href="{{ route('sales.weekly') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-calendar-week text-sm"></i>
                        <span>Weekly Sales</span>
                    </a>
                    <a href="{{ route('sales.monthly') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-calendar-alt text-sm"></i>
                        <span>Monthly Sales</span>
                    </a>
                </div>

                <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-warehouse text-lg"></i>
                    <span>Inventory</span>
                </a>

                <a href="{{ route('suppliers.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-truck text-lg"></i>
                    <span>Suppliers</span>
                </a>

                <a href="{{ route('customers.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-users text-lg"></i>
                    <span>Customers</span>
                </a>

{{-- Owner Expenses links --}}
<div class="space-y-1">
  <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
    <i class="fas fa-wallet text-lg"></i><span>Expenses</span>
  </div>
  <a href="{{ route('expenses.create') }}" class="block p-3 pl-12 hover:bg-indigo-800">Record Expense</a>
  <a href="{{ route('expenses.index') }}" class="block p-3 pl-12 hover:bg-indigo-800">All Expenses</a>
  <a href="{{ route('expenses.today') }}" class="block p-3 pl-12 hover:bg-indigo-800">Today's Expenses</a>
  <a href="{{ route('expenses.weekly') }}" class="block p-3 pl-12 hover:bg-indigo-800">Weekly</a>
  <a href="{{ route('expenses.monthly') }}" class="block p-3 pl-12 hover:bg-indigo-800">Monthly</a>
</div>
                
                <!-- Reports -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-3 p-3 text-indigo-300 font-semibold">
                        <i class="fas fa-chart-bar text-lg"></i>
                        <span>Reports</span>
                    </div>


                    <a href="{{ route('dashboard.annual') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-chart-line"></i>
                    <span>Annual Performance</span>
                </a>
                    <a href="{{ route('reports.sales') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-chart-line text-sm"></i>
                        <span>Sales Report</span>
                    </a>
                    <a href="{{ route('reports.products') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-box-open text-sm"></i>
                        <span>Product Report</span>
                    </a>
                    <a href="{{ route('reports.top-selling') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-fire text-sm text-orange-400"></i>
                        <span>Top Selling</span>
                    </a>
                    <a href="{{ route('reports.custom') }}" class="flex items-center space-x-3 p-3 pl-12 rounded-lg hover:bg-indigo-800">
                        <i class="fas fa-calendar-check text-sm"></i>
                        <span>Custom Range</span>
                    </a>
                </div>

                @if(auth()->user()->isOwner() || auth()->user()->role->name === 'manager')
                <a href="{{ route('staff.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-user-tie text-lg"></i>
                    <span>Staff Management</span>
                </a>
                @endif

                @if(auth()->user()->isOwner())
                <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-indigo-800">
                    <i class="fas fa-cog text-lg"></i>
                    <span>Settings</span>
                </a>
                @endif
            </nav>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-indigo-800 mt-auto">
            <a href="{{ route('owner.profile.edit') }}" class="flex items-center space-x-3 mb-3 hover:opacity-90 transition">
  @if(auth()->user()?->profile_image)
    <img
      src="{{ route('owner.profile.avatar') }}"
      alt="Profile"
      class="w-10 h-10 rounded-full object-cover border border-indigo-600"
    />
  @else
    <div class="w-10 h-10 bg-indigo-700 rounded-full flex items-center justify-center">
      <span class="text-lg font-bold">
        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
      </span>
    </div>
  @endif

  <div class="flex-1">
    <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
    <p class="text-xs text-indigo-300 truncate">{{ auth()->user()->email }}</p>
  </div>
</a>



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
                        @yield('page-title', 'Dashboard')
                    </h1>
                     
                    <div class="flex items-center space-x-4">
                        <span class="hidden md:inline text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ now()->format('D, M d, Y') }}
                        </span>



            


                        <a href="{{ route('pos.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-cash-register mr-1"></i>
                            <span class="hidden md:inline">POS</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ session('error') }}</span>
                    </div>
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
    // ========================================
    // ✅ RESPONSIVE SIDEBAR TOGGLE
    // ========================================
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden-mobile');
        updateBodyScroll();
    }

    // ✅ Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = event.target.closest('button[onclick="toggleSidebar()"]');
        
        // Only apply on mobile screens
        if (window.innerWidth < 768) {
            // If click is not on sidebar and not on toggle button
            if (!sidebar.contains(event.target) && !toggleBtn) {
                sidebar.classList.add('hidden-mobile');
                updateBodyScroll();
            }
        }
    });

    // ✅ Close sidebar on mobile when clicking any link inside
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarLinks = sidebar.querySelectorAll('a, button[type="submit"]');
        
        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    sidebar.classList.add('hidden-mobile');
                    updateBodyScroll();
                }
            });
        });
    });

    // ✅ Ensure sidebar is hidden on mobile on page load
    window.addEventListener('load', function() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth < 768) {
            sidebar.classList.add('hidden-mobile');
        }
    });

    // ✅ Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 768) {
                sidebar.classList.add('hidden-mobile');
            } else {
                sidebar.classList. remove('hidden-mobile');
            }
            updateBodyScroll();
        }, 250);
    });

    // ✅ Prevent body scroll when sidebar is open on mobile
    function updateBodyScroll() {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth < 768 && ! sidebar.classList.contains('hidden-mobile')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }

    // ✅ Close sidebar with ESC key on mobile
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && window.innerWidth < 768) {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add('hidden-mobile');
            updateBodyScroll();
        }
    });
</script>
</body>
</html>