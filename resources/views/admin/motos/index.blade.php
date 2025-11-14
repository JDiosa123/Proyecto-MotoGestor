<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Motos') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex justify-end mb-4">
                {{-- Botón 'Crear Moto' con estilo detallado --}}
                <a href="{{ route('admin.motos.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Crear Moto
                </a>
            </div>

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-200 text-green-800 rounded-md dark:bg-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Contenedor principal de la tabla con encabezado de tarjeta --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">

                    {{-- Encabezado de la Tarjeta --}}
                    <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                        <h4>Gestionar Motos</h4>
                    </div>

                    <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Placa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Marca</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($motos as $moto)
                                <tr>
                                    {{-- Celdas de datos con estilos de texto uniformes --}}
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $moto->placa }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $moto->marca }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $moto->modelo }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $moto->cliente->nombre }} {{ $moto->cliente->apellido }}</td>
                                    
                                    {{-- Columna de acciones --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex justify-end space-x-2">
                                            {{-- Botón Editar con estilo detallado --}}
                                            <a href="{{ route('admin.motos.edit', $moto->id_moto) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                Editar
                                            </a>

                                            <form action="{{ route('admin.motos.destroy', $moto->id_moto) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta moto?');">
                                                @csrf
                                                @method('DELETE')
                                                {{-- Botón Eliminar con estilo detallado --}}
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No hay motos registradas.
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