<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Movimiento') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta --}}
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    <h4>Editar Movimiento de Inventario</h4>
                </div>

                <div class="p-6">
                    <form action="{{ route('inventario.actualizar', $movimiento->id_movimiento) }}" method="POST">
                        @csrf
                        {{-- Laravel necesita el método PUT para las actualizaciones --}}
                        @method('PUT') 
                        
                        {{-- Producto (Deshabilitado) --}}
                        <div class="mb-4">
                            <label for="producto" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Producto</label>
                            <input id="producto" type="text" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm opacity-60" value="{{ $movimiento->producto->nombre }}" disabled>
                        </div>

                        {{-- Tipo --}}
                        <div class="mb-4">
                            <label for="tipo" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tipo</label>
                            <select id="tipo" name="tipo" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="entrada" {{ $movimiento->tipo == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="salida" {{ $movimiento->tipo == 'salida' ? 'selected' : '' }}>Salida</option>
                            </select>
                        </div>

                        {{-- Cantidad --}}
                        <div class="mb-4">
                            <label for="cantidad" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Cantidad</label>
                            <input id="cantidad" type="number" name="cantidad" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $movimiento->cantidad }}" required>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label for="descripcion" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción</label>
                            <input id="descripcion" type="text" name="descripcion" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ $movimiento->descripcion }}">
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="flex justify-end space-x-3">
                            {{-- Botón Cancelar (Enlace) --}}
                            <a href="{{ route('inventario.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            {{-- Botón Guardar --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>