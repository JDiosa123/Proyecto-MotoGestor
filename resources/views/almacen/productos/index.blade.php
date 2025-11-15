<x-app-layout>

<div class="container mx-auto px-4 mt-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Productos en Stock</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead class="bg-gray-200 dark:bg-gray-700">
                <tr>
                    
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Cantidad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Descripción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Fecha Registro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Acciones</th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-700">
                @foreach ($productos as $producto)
                    <tr>
                        

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $producto->nombre }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $producto->categoria }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            ${{ number_format($producto->precio, 0, ',', '.') }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-3 py-1 rounded-full text-white 
                                {{ $producto->cantidad <= 5 ? 'bg-red-500' : 'bg-green-500' }}">
                                {{ $producto->cantidad }}
                            </span>
                        </td>

                        
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $producto->descripcion ?? '—' }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                            {{ $producto->fecha_registro }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap flex gap-3">

                            
                            <a href="{{ route('productos.editar', $producto->id_producto) }}"
                               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                Editar
                            </a>

                            
                            <form action="{{ route('productos.eliminar', $producto->id_producto) }}" 
                                  method="POST"
                                  onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    Eliminar
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</x-app-layout>
