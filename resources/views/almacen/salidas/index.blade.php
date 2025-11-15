<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Salidas de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="flex justify-start mb-4">
                <a href="{{ route('salidas.create') }}"
                    class="px-6 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 shadow">
                    + Registrar Salida
                </a>
            </div>

            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    Historial de Salidas
                </div>

                
                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider max-w-xs">Descripción</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($salidas as $salida)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium whitespace-nowrap">
                                        {{ $salida->producto->nombre ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $salida->cantidad }}
                                    </td>

                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $salida->fecha ? \Carbon\Carbon::parse($salida->fecha)->format('Y-m-d') : 'Sin fecha' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm max-w-xs truncate" title="{{ $salida->descripcion }}">
                                        {{ $salida->descripcion ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay salidas registradas.
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
