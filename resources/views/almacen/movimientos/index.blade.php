<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Inventario') }}
        </h2>
    </x-slot>

    <div class="w-full flex justify-center mt-4">
        <div class="flex gap-4">

            <a href="{{ route('entradas.index') }}"
            class="px-6 py-2 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 shadow">
                Entradas
            </a>

            <a href="{{ route('salidas.index') }}"
            class="px-6 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 shadow">
                Salidas
            </a>

        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-md dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-200 text-red-800 rounded-md dark:bg-red-800 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{-- SOLO HISTORIAL --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    Historial de Movimientos
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($movimientos as $mov)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $mov->fecha }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $mov->producto->nombre }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $mov->producto->categoria ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">${{ number_format($mov->producto->precio, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full
                                            {{ $mov->tipo === 'entrada'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($mov->tipo) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $mov->cantidad }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $mov->descripcion }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay movimientos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
