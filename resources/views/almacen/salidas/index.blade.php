<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Salidas de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Botón de Registro con Estilo Consistente (Rojo) --}}
            <div class="flex justify-end mb-4">
                <a href="{{ route('salidas.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                    Registrar Salida
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-extrabold text-xl sm:rounded-t-lg border-b-2 border-gray-600">
                    Historial de Salidas
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Encabezados de la Tabla con Estilo Consistente --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($salidas as $salida)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    {{-- Producto (Texto principal) --}}
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                        {{ $salida->producto->nombre ?? '—' }}
                                    </td>

                                    {{-- Cantidad (Resaltada en Rojo para Salidas) --}}
                                    <td class="px-6 py-4 text-sm font-bold text-red-600 dark:text-red-400 whitespace-nowrap">
                                        {{ $salida->cantidad }}
                                    </td>

                                    {{-- Fecha --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                        {{ $salida->fecha ? \Carbon\Carbon::parse($salida->fecha)->format('Y-m-d') : '—' }}
                                    </td>

                                    {{-- Descripción --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate" title="{{ $salida->descripcion }}">
                                        {{ $salida->descripcion ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-md text-gray-500 dark:text-gray-400 font-medium">
                                        No hay salidas registradas en el historial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación (si se implementa) --}}
                @if(method_exists($salidas, 'links'))
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 sm:rounded-b-lg">
                        {{ $salidas->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>