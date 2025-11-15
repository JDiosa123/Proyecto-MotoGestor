<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Inventario') }}
        </h2>
    </x-slot>

    {{-- Botones de Navegación con Estilo Consistente --}}
    <div class="w-full flex justify-center mt-6">
        <div class="flex gap-4">
            {{-- Botón Entradas (Verde - Registro Primario) --}}
            <a href="{{ route('entradas.index') }}"
                class="inline-flex items-center px-6 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Entradas
            </a>

            {{-- Botón Salidas (Rojo - Acción de Egreso) --}}
            <a href="{{ route('salidas.index') }}"
                class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                Salidas
            </a>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes de Sesión --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-lg dark:bg-green-800 dark:text-green-200 font-medium border border-green-300 dark:border-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-200 text-red-800 rounded-lg dark:bg-red-800 dark:text-red-200 font-medium border border-red-300 dark:border-red-700">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tarjeta de Historial de Movimientos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-extrabold text-xl sm:rounded-t-lg border-b-2 border-gray-600">
                    Historial de Movimientos
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Encabezados de la Tabla con estilo del Listado de Clientes --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                            </tr>
                        </thead>

                        {{-- Cuerpo de la Tabla --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    {{-- Fecha, Categoría, Precio, Descripción (Texto secundario) --}}
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $mov->fecha }}</td>
                                    
                                    {{-- Producto (Texto primario) --}}
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $mov->producto->nombre }}</td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $mov->producto->categoria ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">${{ number_format($mov->producto->precio, 0, ',', '.') }}</td>
                                    
                                    {{-- Tipo de Movimiento (Etiquetas) --}}
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full shadow-sm uppercase
                                            {{ $mov->tipo === 'entrada'
                                                ? 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100'
                                                : 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' }}">
                                            {{ ucfirst($mov->tipo) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Cantidad y Descripción --}}
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-bold">{{ $mov->cantidad }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $mov->descripcion }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-md text-gray-500 dark:text-gray-400 font-medium">
                                        No hay movimientos registrados en el historial.
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