<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Entrada') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                
                {{-- Encabezado de la Tarjeta (Ahora Verde, color de Entradas) --}}
                <div class="p-6 bg-green-600 text-white font-extrabold text-xl sm:rounded-t-lg border-b-2 border-green-700">
                    Registrar Nueva Entrada de Inventario
                </div>

                <div class="p-6">
                    
                    @if ($errors->any())
                        {{-- Estilo de Alerta de Error --}}
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-800 rounded-lg dark:bg-red-900 dark:border-red-700 dark:text-red-200 shadow-sm">
                            <p class="font-semibold mb-2">Ocurrieron los siguientes errores:</p>
                            <ul class="list-disc list-inside pl-2">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('entradas.store') }}">
                        @csrf

                        {{-- Layout de 12 columnas (optimizado para campos más pequeños) --}}
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                            {{-- Nombre del Producto (6/12) --}}
                            <div class="col-span-full md:col-span-6">
                                <label for="nombre" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Nombre del producto</label>
                                <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 @error('nombre') border-red-500 @enderror"
                                    required>
                                @error('nombre') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Categoría (3/12) --}}
                            <div class="col-span-full md:col-span-3">
                                <label for="categoria" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Categoría</label>
                                <select id="categoria" name="categoria" required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 @error('categoria') border-red-500 @enderror">
                                    <option value="">Seleccionar</option>
                                    <option value="Insumos" {{ old('categoria') == 'Insumos' ? 'selected' : '' }}>Insumos</option>
                                    <option value="General"  {{ old('categoria') == 'General' ? 'selected' : '' }}>General</option>
                                    <option value="Dotación" {{ old('categoria') == 'Dotación' ? 'selected' : '' }}>Dotación</option>
                                </select>
                                @error('categoria') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Cantidad (3/12) --}}
                            <div class="col-span-full md:col-span-3">
                                <label for="cantidad" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Cantidad</label>
                                <input id="cantidad" type="number" name="cantidad" min="1" value="{{ old('cantidad', 1) }}"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 @error('cantidad') border-red-500 @enderror"
                                    required>
                                @error('cantidad') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            {{-- Precio (4/12) --}}
                            <div class="col-span-full md:col-span-4">
                                <label for="precio" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Precio Unitario</label>
                                <input id="precio" type="number" name="precio" step="0.01" min="0" value="{{ old('precio') }}"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 @error('precio') border-red-500 @enderror"
                                    required>
                                @error('precio') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Descripción (8/12) --}}
                            <div class="col-span-full md:col-span-8">
                                <label for="descripcion" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Descripción / Notas de la Entrada</label>
                                <input id="descripcion" type="text" name="descripcion" value="{{ old('descripcion') }}"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-green-500 @error('descripcion') border-red-500 @enderror"
                                    placeholder="Detalles sobre el proveedor, factura, o motivo de la entrada (Opcional)">
                                @error('descripcion') <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            
                            {{-- Botones de Acción --}}
                            <div class="col-span-full pt-4 flex justify-end gap-3 border-t border-gray-200 dark:border-gray-700 mt-4">
                                
                                {{-- Botón Cancelar (Gris) --}}
                                <a href="{{ route('entradas.index') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-lg font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar
                                </a>

                                {{-- Botón Guardar (Verde) --}}
                                <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Registrar Entrada
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>