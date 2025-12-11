<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your Business - Uganda Inventory</title>
  <!-- <script src="https://cdn.tailwindcss.com"></script>-->
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <!-- Back to Home -->
            <div class="text-center mb-6">
                <a href="{{ route('welcome') }}" class="text-white hover:text-gray-200 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Home
                </a>
            </div>

            <!-- Registration Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-rocket text-5xl text-indigo-600 mb-4"></i>
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Register Your Business
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        <i class="fas fa-gift text-green-500 mr-1"></i>
                        Get started with <span class="font-bold text-green-600">30 days FREE trial</span>
                    </p>
                </div>

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Business Name -->
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-store text-indigo-600 mr-1"></i>
                            Business Name <span class="text-red-500">*</span>
                        </label>
                        <input id="business_name" name="business_name" type="text" required
                               value="{{ old('business_name') }}"
                               class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="e.g., Davis Electronics Store">
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Category -->
                    <div>
                        <label for="business_category_id" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tags text-indigo-600 mr-1"></i>
                            Business Category <span class="text-red-500">*</span>
                        </label>
                        <select id="business_category_id" name="business_category_id" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">-- Select Your Business Type --</option>
                            @foreach($businessCategories as $category)
                                <option value="{{ $category->id }}" {{ old('business_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('business_category_id')
                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Email -->
                        <div>
                            <label for="business_email" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-envelope text-indigo-600 mr-1"></i>
                                Business Email <span class="text-red-500">*</span>
                            </label>
                            <input id="business_email" name="business_email" type="email" required
                                   value="{{ old('business_email') }}"
                                   class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="business@example.com">
                            @error('business_email')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Number -->
                        <div>
                            <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-phone text-indigo-600 mr-1"></i>
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input id="contact" name="contact" type="tel" required
                                   value="{{ old('contact') }}"
                                   class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="0700123456">
                            @error('contact')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Personal Name -->
                    <div>
                        <label for="personal_name" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-user text-indigo-600 mr-1"></i>
                            Your Full Name (Business Owner) <span class="text-red-500">*</span>
                        </label>
                        <input id="personal_name" name="personal_name" type="text" required
                               value="{{ old('personal_name') }}"
                               class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                               placeholder="Barigye Davis">
                        @error('personal_name')
                            <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            This will be your name as the business owner
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-lock text-indigo-600 mr-1"></i>
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input id="password" name="password" type="password" required
                                   class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Min. 8 characters">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-lock text-indigo-600 mr-1"></i>
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Re-enter password">
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">What happens after registration?</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>You'll be logged in automatically as the business owner</li>
                                    <li>You can add staff members with different roles from your dashboard</li>
                                    <li>This password will be used to login as "Owner"</li>
                                    <li>You get 30 days free trial - no credit card needed!</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform transition hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>
                            Create My Business Account
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center">
                        <span class="text-gray-600">Already have an account?</span>
                        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500 ml-1">
                            Login here <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Trust Badges -->
            <div class="mt-8 text-center text-white">
                <div class="flex justify-center space-x-6 text-sm">
                    <div><i class="fas fa-shield-alt mr-1"></i> Secure & Encrypted</div>
                    <div><i class="fas fa-clock mr-1"></i> Setup in 2 Minutes</div>
                    <div><i class="fas fa-headset mr-1"></i> 24/7 Support</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>