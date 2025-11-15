<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Productos en Stock') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de Sesión (Éxito) --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-md dark:bg-green-800 dark:text-green-200 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Tarjeta Principal (Lista de Productos) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>Productos en Stock</h4>
                </div>

                {{-- Se eliminó la clase overflow-x-auto --}}
                <div> 
                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Encabezado de la Tabla --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fecha Registro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>

                        {{-- Cuerpo de la Tabla --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($productos as $producto)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">

                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $producto->nombre }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $producto->categoria }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        ${{ number_format($producto->precio, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        {{-- Indicador de Stock Bajo --}}
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                            {{ $producto->cantidad <= 5 
                                                ? 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' 
                                                : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-700 dark:text-emerald-100' }}">
                                            {{ $producto->cantidad }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $producto->descripcion ?? '—' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                        {{ $producto->fecha_registro }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex justify-end space-x-2">
                                            {{-- Botón Eliminar --}}
                                            <form action="{{ route('productos.eliminar', $producto->id_producto) }}" 
                                                    method="POST"
                                                    onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Asumiendo que necesitas un mensaje si no hay productos --}}
                            @empty($productos)
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay productos registrados en el stock.
                                    </td>
                                </tr>
                            @endempty
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>