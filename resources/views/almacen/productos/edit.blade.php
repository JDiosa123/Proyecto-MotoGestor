<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-gray-700 text-white font-semibold text-lg sm:rounded-t-lg">
                    Editar información del producto
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('productos.actualizar', $producto->id_producto) }}">
                        @csrf
                        @method('PUT')

                        
                        <div class="mb-4">
                            <label for="nombre" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nombre</label>

                            
                            <input type="hidden" name="nombre" value="{{ $producto->nombre }}">

                            
                            <input id="nombre" type="text" value="{{ $producto->nombre }}" 
                                disabled
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label for="categoria" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Categoría</label>
                            <input id="categoria" type="text" name="categoria" value="{{ old('categoria', $producto->categoria) }}"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label for="precio" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Precio</label>
                            <input id="precio" type="number" name="precio" value="{{ old('precio', $producto->precio) }}" min="0" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="mb-4">
                            <label for="cantidad" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Cantidad</label>
                            <input id="cantidad" type="number" name="cantidad" value="{{ old('cantidad', $producto->cantidad) }}" min="0" required
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                        </div>

                        <div class="mb-6">
                            <label for="descripcion" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="2"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">{{ old('descripcion', $producto->descripcion) }}</textarea>
                        </div>

                        <div class="flex items-center justify-center space-x-4">
                            <x-primary-button>
                                {{ __('Guardar cambios') }}
                            </x-primary-button>
                        </div><br>

                        <div class="flex items-center justify-center space-x-4">
                            <a href="{{ route('productos.index') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
