<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('¡Bienvenido a Motogestor!') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-2">
                        {{ __('Hola, ') }} {{ Auth::user()->name ?? 'Usuario' }} 👋
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ __('Has iniciado sesión correctamente en Motogestor.') }}
                    </p>
                    <p class="mt-2 text-gray-700 dark:text-gray-300">
                        {{ __('Desde aquí podrás gestionar toda la logica de tu establecimiento.') }}
                    </p>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400 italic">
                        {{ __('¡Que tengas un excelente y productivo día de trabajo en el taller!') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

