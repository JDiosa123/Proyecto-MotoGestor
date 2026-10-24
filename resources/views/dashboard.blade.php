<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Motogestor') }}
        </h2>
    </x-slot>

    @php
        $role = Auth::user()->role;
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-2">{{ __('Hola, ') }}{{ Auth::user()->name }}</h3>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @if (in_array($role, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT], true))
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('admin.citas.index') }}">Citas</a>
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('admin.clientes.index') }}">Clientes</a>
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('admin.motos.index') }}">Motos</a>
                        @endif

                        @if (in_array($role, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_ASSISTANT, \App\Models\User::ROLE_STOREKEEPER], true))
                            <a class="rounded-lg bg-emerald-600 p-5 font-semibold text-white" href="{{ route('inventario.index') }}">Inventario</a>
                            <a class="rounded-lg bg-emerald-600 p-5 font-semibold text-white" href="{{ route('productos.index') }}">Productos</a>
                        @endif

                        @if ($role === \App\Models\User::ROLE_ADMIN)
                            <a class="rounded-lg bg-gray-700 p-5 font-semibold text-white" href="{{ route('admin.users.index') }}">Usuarios</a>
                        @elseif ($role === \App\Models\User::ROLE_MECHANIC)
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('mecanico.citas.index') }}">Mis citas asignadas</a>
                        @elseif ($role === \App\Models\User::ROLE_CLIENT)
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('mis-citas.index') }}">Mis citas</a>
                            <a class="rounded-lg bg-blue-600 p-5 font-semibold text-white" href="{{ route('mis-motos.index') }}">Mis motos</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
