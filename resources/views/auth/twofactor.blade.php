<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Login - Uganda Inventory</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-600 min-h-screen">
  <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">

<form method="POST" action="{{ route('logout') }}" class="inline">
  @csrf
  <button type="submit" class="text-white hover:text-gray-200 font-medium">
    <i class="fas fa-arrow-left mr-2"></i> Back to Login
  </button>
</form>

      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <div class="text-center mb-8">
          <i class="fas fa-shield-alt text-5xl text-indigo-600 mb-4"></i>
          <h2 class="text-3xl font-extrabold text-gray-900">
            Two-Factor Verification
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Enter the 6-digit code sent to your email. It expires in 3 minutes.
          </p>
        </div>

        @if(session('success'))
          <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <div class="flex items-center">
              <i class="fas fa-check-circle mr-2"></i>
              <span>{{ session('success') }}</span>
            </div>
          </div>
        @endif

        @if($errors->any())
          <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <div class="flex items-start">
              <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
              <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endif>

        <form method="POST" action="{{ route('auth.twofactor.verify') }}" class="space-y-6">
          @csrf

          <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
              <i class="fas fa-key text-indigo-600 mr-1"></i>
              Verification Code <span class="text-red-500">*</span>
            </label>
            <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
                   class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg text-center tracking-widest text-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                   placeholder="______" value="{{ old('code') }}">
          </div>

          <div class="flex items-center justify-between">
            <button type="submit"
                    class="group relative flex justify-center py-3 px-5 border border-transparent text-sm font-bold rounded-lg text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow">
              <i class="fas fa-unlock mr-2"></i>
              Verify and Continue
            </button>

            <form method="POST" action="{{ route('auth.twofactor.resend') }}">
              @csrf
              <button class="py-3 px-4 rounded-lg bg-gray-100 text-gray-800 text-sm font-semibold hover:bg-gray-200 border border-gray-200">
                <i class="fas fa-redo mr-2"></i> Resend Code
              </button>
            </form>
          </div>

          <p class="mt-2 text-xs text-gray-500">
            Didn’t get the email? Check your spam folder or resend a new code.
          </p>
        </form>
      </div>

      <div class="mt-8 text-center text-white">
        <p class="text-sm">
          <i class="fas fa-lock mr-1"></i>
          Secured by Uganda Inventory
        </p>
      </div>
    </div>
  </div>
</body>
</html>