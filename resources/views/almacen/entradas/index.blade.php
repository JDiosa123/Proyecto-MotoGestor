<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Entradas de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <div class="flex justify-start mb-4">
                <a href="{{ route('entradas.create') }}"
                    class="px-6 py-2 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 shadow">
                    + Registrar Entrada
                </a>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    Historial de Entradas
                </div>

                
                <div class="overflow-x-auto">
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($entradas as $entrada)
                                <tr>

                                    
                                    <td class="px-6 py-4 text-sm">
                                        {{ $entrada->producto->nombre ?? '—' }}
                                    </td>

                                    
                                    <td class="px-6 py-4 text-sm">
                                        {{ ucfirst($entrada->producto->categoria) ?? '—' }}
                                    </td>

                                    
                                    <td class="px-6 py-4 text-sm">
                                        @if(isset($entrada->producto->precio))
                                            ${{ number_format($entrada->producto->precio, 0, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    
                                    <td class="px-6 py-4 text-sm">
                                        {{ $entrada->cantidad }}
                                    </td>

                                    
                                    <td class="px-6 py-4 text-sm">
                                        {{ $entrada->fecha ?? $entrada->created_at->format('Y-m-d H:i:s') ?? '—' }}
                                    </td>

                                    
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $entrada->descripcion ?? '—' }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay entradas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                
                @if(method_exists($entradas, 'links'))
                    <div class="p-4">
                        {{ $entradas->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
