<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Entradas de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Botón de Registro con Estilo Consistente --}}
            
            <div class="flex justify-end mb-4">
                <a href="{{ route('entradas.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Registrar Entrada
                </a>
                
            </div>
            
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-extrabold text-xl sm:rounded-t-lg border-b-2 border-gray-600">
                    Historial de Entradas
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Encabezados de la Tabla con Estilo Consistente --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($entradas as $entrada)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">

                                    {{-- Producto (Texto principal) --}}
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $entrada->producto->nombre ?? '—' }}
                                    </td>

                                    {{-- Categoría y Precio (Texto secundario) --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ ucfirst($entrada->producto->categoria) ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        @if(isset($entrada->producto->precio))
                                            ${{ number_format($entrada->producto->precio, 0, ',', '.') }}
                                        @else
                                            —
                                        @endif
                                    </td>

                                    {{-- Cantidad (Resaltada) --}}
                                    <td class="px-6 py-4 text-sm font-bold text-green-600 dark:text-green-400">
                                        {{ $entrada->cantidad }}
                                    </td>

                                    {{-- Fecha y Descripción (Texto secundario/Truncado) --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $entrada->fecha ?? $entrada->created_at->format('Y-m-d H:i') ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                        {{ $entrada->descripcion ?? '—' }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-md text-gray-500 dark:text-gray-400 font-medium">
                                        No hay entradas registradas en el historial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if(method_exists($entradas, 'links'))
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 sm:rounded-b-lg">
                        {{ $entradas->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>