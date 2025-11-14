<x-guest-layout>
    <div class="flex justify-center mb-6">
        <a href="/">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="w-50 h-50 fill-current text-gray-500">
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Correo')" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full text-gray-900" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" class="text-white" />
            <x-text-input id="password" class="block mt-1 w-full text-gray-900"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recordar Sesión -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-700 text-cyan-500 shadow-sm focus:ring-cyan-500 dark:focus:ring-cyan-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-white">{{ __('Recordar Sesión') }}</span>
            </label>
        </div>
        @if (Route::has('password.request'))
            <a class="underline text-sm text-white hover:text-cyan-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                {{ __('¿Olvidaste tu contraseña?') }}
            </a>
        @endif

        <div class="flex flex-col justify-end mt-6 space-y-4"> 
            <x-primary-button class="w-full justify-center"> 
                {{ __('Iniciar Sesión') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>